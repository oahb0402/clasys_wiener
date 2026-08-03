<?php

namespace App\Http\Controllers;

use App\Models\G110;
use Illuminate\Support\Facades\DB; // <-- Importamos DB si no usas un modelo para los controles
use App\Models\G220;
use App\Models\G220Sms;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClasysController extends Controller
{
    public function index(Request $request, $id)
    {
        // 1. Buscamos el cliente en la tabla 'g110' con sus cuentas asociadas en 'g110_cta'
        $cliente = G110::with('detalles')->findOrFail($id);

        // Capturas los datos de la llamada/marcador
        $paramsLlamada = [
            'telf'             => $request->query('telf'),
            'uid'              => $request->query('uid'),
            'campania'         => $request->query('campania'),
            'idllamada'        => $request->query('idllamada'),
            'extension'        => $request->query('extension'),
            'accionPredictivo' => $request->query('accion_predictivo'),
        ];

        // Para aplanar todos los recibos de todas sus cuentas en una sola colección fácil de recorrer:
        // 2. Usas directamente la colección de detalles como tus recibos
        $recibos = $cliente->detalles;


        //SELECT substr(codigo,3,3) as codigo1,descri from f190 where codigo like 'TB%' $adicion and codigo in ('TBTM','TBTC','TBML','TBMT','TBWA') order by codigo1

        $tipo_gestiones = DB::table('f190')
            ->select(DB::raw("SUBSTR(codigo, 3, 3) AS codigo"), 'descri')
            ->where('activo', '1')
            ->whereIn('codigo', ['TBTM', 'TBTC', 'TBML', 'TBMT', 'TBWA']) // <-- Se cambia 'where' por 'whereIn'
            ->orderBy('descri', 'asc')                   // <-- Ordenado alfabéticamente por descripción
            ->get();


        $tipo_contactos = DB::table('f190')
            ->select(DB::raw("SUBSTR(codigo, 3, 3) AS codigo"), 'descri')
            ->where('activo', '1')
            ->where('codigo', 'LIKE', 'UN%')
            ->orderBy('descri', 'asc')                   // <-- Ordenado alfabéticamente por descripción
            ->get();


        $respuestas = DB::table('respuestas')
            ->select('codigo', 'descrip', 'corta', 'promesa')
            ->where('activo', '1') // <-- Filtra solo las respuestas activas
            ->where('tipo', 'TELEFONO') // <-- Filtra solo las respuestas del tipo TELEFONO
            ->orderBy('corta', 'asc') // Ajustado a 'corta'
            ->orderBy('descrip', 'asc') // Ajustado a 'descrip'
            ->get()
            ->groupBy('corta');


        // 2. Obtenemos TODAS las sub-respuestas activas incluyendo el campo padre (ej: 'respuesta_codigo' o 'padre_id')
        $sub_respuestas = DB::table('sub_respuestas')
            ->select('codigo', 'descrip')
            ->where('activo', '1')
            ->where('tipo', 'TELEFONO')
            // Quitamos el ->where('codigo', '!=', '12') para que se envíe a la vista
            ->orderByRaw('codigo::int ASC')
            ->get();


        $condiciones = DB::table('condiciong110')
            ->select(DB::raw('TRIM(codigo) as codigo'), 'descrip')
            ->where('activo', '1')
            ->whereNotIn(DB::raw('TRIM(codigo)'), ['AC', 'AG', 'AJ', 'AD', 'BQ', 'DB', 'DV', 'DC', 'EP', 'RF', 'SS', 'IF', 'IT', 'IN', 'MN', 'NG', 'NM', 'ND', 'NT', 'PC', 'PT', 'PH', 'PU', 'X1', 'Y1', 'PF', 'PV', 'VF', 'RR', 'RW', 'SM', 'ST', 'UT', 'UF', 'UM', 'UG', 'IC', 'RN', 'UP', 'ZP', 'CA'])
            ->orderBy('descrip', 'asc')
            ->get();

        // Obtener solo los códigos como un array: ['21', '22', '31']
        $codigosPromesaX = DB::table('r_respuestas_x')
            ->where('tipo', 'PROMESA')
            ->pluck('codigo')
            ->toArray();

        // Obtener solo los códigos como un array: ['21', '22', '31']
        $codigosConfirmacionX = DB::table('r_respuestas_x')
            ->where('tipo', 'CONFIRMACION')
            ->pluck('codigo')
            ->toArray();


        // Pasamos el $id del cliente de forma segura en un arreglo de parámetros
        $telefonosSugeridos = [];
        /* $telefonosSugeridos = DB::select('SELECT * FROM f_telefonos_totales_banco_cod_deu(:cod_deu)', [
        'cod_deu' => $id
        ]); */



        // Obtener el cliente con sus conteos
        $clienteId = $id;
        $historialRapido = [

            // Total de gestiones excluyendo IV y SMS y MAIL
            'total' => $cliente->gestiones()
                ->whereNotIn('tip_con', ['IV', 'ML'])
                ->count(),

            // Gestiones donde hubo contacto efectivo o compromiso (según tu regla de negocio)
            'positives' => $cliente->gestiones()
                ->whereNotIn('tip_con', ['IV', 'ML'])
                ->whereIn('corta', ['CEF', 'CNE'])
                ->count(),

            // Herramienta IVR (Interactive Voice Response)
            'ivr' => $cliente->gestiones()
                ->where('tip_con', 'IV') // O filtrando por tabla/relación de IVR
                ->count(),

            'mail' => $cliente->gestiones()
                ->whereIn('tip_con', ['ML'])
                ->count(),

            'sms' => $cliente->gestiones_sms()
                ->count(),

            'abono' => 0
        ];




        // 3. Días restantes para el cierre de cartera
        // Tomando como referencia la fecha de hoy
        $fechaCierre = Carbon::create(2026, 12, 31);
        $diasParaCierre = (int) Carbon::now()->diffInDays($fechaCierre);

        // 4. Renderizamos la vista principal pasándole las variables
        return view('crm.principal', [
            'cliente' => $cliente,
            'recibos' => $recibos,
            'historialRapido' => $historialRapido,
            'diasParaCierre' => $diasParaCierre,
            'tipo_gestiones' => $tipo_gestiones,
            'tipo_contactos' => $tipo_contactos,
            'respuestas' => $respuestas,
            'sub_respuestas' => $sub_respuestas,
            'codigosPromesaX' => $codigosPromesaX,
            'codigosConfirmacionX' => $codigosConfirmacionX,
            'condiciones' => $condiciones,
            'telefonosSugeridos' => $telefonosSugeridos,
            'paramsLlamada' => $paramsLlamada
        ]);
    }


    /**
     * Configuración por tipo de historial: qué relación de G110 consultar
     * y cómo mapear sus columnas al formato genérico que espera el frontend
     * (item_num, fecha, contacto, estado, comentario).
     **/

    protected function configHistorial(string $tipo): array
    {
        return match ($tipo) {
            'sms' => [
                'relacion' => 'gestiones_sms',
                'campos' => fn($item) => [
                    'contacto'   => $item->telef_ges ?? 'N/A',
                    'estado'     => $item->tip_rb ?? 'SMS',
                    'comentario' => $item->comentario ?? $item->detalle ?? 'Sin detalle',
                ],
            ],

            'ivr' => [
                'relacion' => 'gestiones', // <-- confirma el nombre real de esta relación en G110
                'campos' => fn($item) => [
                    'contacto'   => $item->telef_ges ?? 'N/A',
                    //'estado'     => $item->tip_rb ?? 'IVR',
                    'estado'     => 'IVR',
                    'comentario' => $item->comentario ?? $item->detalle ?? 'Sin detalle',
                ],
            ],

            'mail' => [
                'relacion' => 'gestiones', // <-- confirma el nombre real
                'campos' => fn($item) => [
                    'contacto'   => $item->comenta3 ?? $item->correo ?? 'N/A', // <-- confirma la columna del correo
                    //'estado'     => $item->control4 ?? 'MAIL',
                    'estado'     => 'MAIL',
                    'comentario' => $item->comentario ?? $item->asunto ?? 'Sin detalle',
                ],
            ],

            'gestiones' => [
                'relacion' => 'gestiones', // <-- confirma el nombre real (todas las gestiones, sin filtrar canal)
                'campos' => fn($item) => [
                    'contacto'   => $item->telef_ges ?? 'N/A',
                    'estado'     => $item->tip_rb ?? 'GESTIÓN',
                    'comentario' => $item->comentario ?? $item->detalle ?? 'Sin detalle',
                ],
            ],
            'positivas' => [
                'relacion' => 'gestiones', // <-- confirma el nombre real (todas las gestiones, sin filtrar canal)
                'campos' => fn($item) => [
                    'contacto'   => $item->telef_ges ?? 'N/A',
                    'estado'     => $item->tip_rb ?? 'GESTIÓN',
                    'comentario' => $item->comentario ?? $item->detalle ?? 'Sin detalle',
                ],
            ],
            'editar_gestiones' => [
                'relacion' => 'gestiones',
                'campos' => fn($item) => [
                    // 'id' es el identificador real que se usa en editarGestion(id) desde el botón "Editar"
                    'id'            => $item->item,
                    'respuesta'     => trim(($item->corta ?? '') . ' - ' . ($item->comentario ?? '')), // <-- confirma: ¿de dónde sale "802 - Contestan y cuelgan"? ¿es una relación a la tabla "respuestas"?
                    'sub_respuesta' => $item->sub_res ?? '', // <-- confirma la columna/relación real
                    'monto_pdp'     => $item->mon_pro ?? 0,           // <-- confirma el nombre real de la columna
                    'condicion'     => $item->condicion ?? '',          // <-- confirma el nombre real de la columna (código tipo "GN")
                    'telefono'      => $item->telef_ges ?? '',
                    'hora'          => $item->control1 ?? '',
                ],
            ],

            default => abort(404, "Tipo de historial \"$tipo\" no existe"),
        };
    }

    /**
     * Un solo endpoint para sms / ivr / mail / gestiones.
     * Ruta sugerida: Route::get('/crm/gestion/{id}/historial/{tipo}', [Controller::class, 'historial']);
     */
    public function historial(Request $request, $id, string $tipo)
    {
        $config = $this->configHistorial($tipo);

        $query = G110::findOrFail($id)->{$config['relacion']}()
            ->orderBy('item', 'desc');

        // Filtro opcional (ej. ?solo_positivas=1 para "Gestiones Positivas")
        if ($tipo === 'positivas') {
            $query
                ->whereNotIn('tip_con', ['IV', 'ML'])
                ->whereIn('corta', ['CEF', 'CNE']); // <-- ajusta al nombre real de esta columna/condición
        } elseif ($tipo === 'gestiones') {
            $query
                ->whereNotIn('tip_con', ['IV', 'ML']);
        } elseif ($tipo === 'ivr') {
            $query
                ->whereIn('tip_con', ['IV']);
        } elseif ($tipo === 'mail') {
            $query
                ->whereIn('tip_con', ['ML']);
        } elseif ($tipo === 'editar_gestiones') {
            $query
                ->whereNotIn('usuario', ['91']);
        }

        $paginados = $query->paginate(5);

        $total     = $paginados->total();
        $firstItem = $paginados->firstItem();

        $data = collect($paginados->items())->map(function ($item, $key) use ($total, $firstItem, $config) {
            $numeroItem = $total - ($firstItem + $key - 1);

            // Formateo de Fecha / Hora (igual para todos los tipos)
            $fechaFormateada = 'N/A';
            if (!empty($item->fec_sin)) {
                $fechaFormateada = is_object($item->fec_sin)
                    ? $item->fec_sin->format('Y-m-d H:i:s')
                    : trim($item->fec_sin);

                if (!empty($item->control1)) {
                    $fechaClean = trim(explode(' ', $fechaFormateada)[0]);
                    $horaClean = trim($item->control1);
                    $fechaFormateada = "{$fechaClean} {$horaClean}";
                }
            }

            return array_merge(
                [
                    'item_num' => $numeroItem,
                    'fecha'    => $fechaFormateada,
                ],
                ($config['campos'])($item)
            );
        });

        return response()->json([
            'data'         => $data,
            'current_page' => $paginados->currentPage(),
            'last_page'    => $paginados->lastPage(),
            'first_item'   => $firstItem ?? 0,
            'last_item'    => $paginados->lastItem() ?? 0,
            'total'        => $total,
        ]);
    }


    public function obtenerGestionParaEditar(Request $request, $id, $item)
    {
        $cliente = G110::findOrFail($id);

        $gestion = $cliente->gestiones()
            ->where('item', $item)
            ->firstOrFail();

        return response()->json([
            'tipcon'         => trim($gestion->tip_con ?? ''),
            'tipgb'          => trim($gestion->tip_gb ?? ''),
            'control'        => trim($gestion->tip_rb ?? ''),
            'subres'         => trim($gestion->sub_res ?? ''),
            'comentario'     => $gestion->comentario ?? '',
            'condicion'      => trim($gestion->cond_gral ?? ''),
            'telefono'       => trim($gestion->telef_ges ?? ''),
            'monto_promesa'  => $gestion->mon_pro ?? 0,
            'moneda_promesa' => trim($gestion->moneda ?? ''),
            'fecha_promesa'  => $gestion->fec_reg,
            'nombre_titular' => $gestion->control3,
            'dni_titular'    => $gestion->control4,
            'datos_tarjeta'  => $gestion->control5,
            'medio_pago'     => trim($gestion->condicion ?? ''),
            'comprobante_confirmacion_url' => $gestion->comenta3, // solo para mostrar link, no se puede precargar el <input type="file">
        ]);
    }

    public function guardarGestion(Request $request, $id)
    {
        $cliente = G110::findOrFail($id);

        $resultado = DB::transaction(function () use ($request, $cliente) {
            $siguienteItem = DB::selectOne(
                "SELECT nuevo_item_por_codigo(?) AS item",
                [$cliente->cod_deu]
            )->item;

            $datos = $this->mapearDatosGestion($request);
            $datos['cod_deu'] = $cliente->cod_deu;
            $datos['item']    = $siguienteItem;
            $datos['cod_ban'] = $cliente->cod_ban;
            $datos['grupo'] = $cliente->grupo;
            $datos['nro_cta'] = $cliente->nro_cta;

            DB::table('g220')->insert($datos);

             $this->actualizarCondicionCliente($cliente, $request);
            return $siguienteItem;
        });

        return response()->json([
            'mensaje' => 'Gestión registrada correctamente.',
            'item'    => $resultado,
        ]);
    }

    public function actualizarGestion(Request $request, $id, $item)
    {
        $cliente = G110::findOrFail($id);

        DB::transaction(function () use ($request, $cliente, $item) {
            $datos = $this->mapearDatosGestion($request);

            DB::table('g220')
                ->where('cod_deu', $cliente->cod_deu)
                ->where('item', $item)
                ->update($datos);

            $this->actualizarCondicionCliente($cliente, $request);
        });

        return response()->json([
            'mensaje' => 'Gestión actualizada correctamente.',
        ]);
    }

    /**
     * Mapea los campos del form a las columnas reales de g220.
     * NOTA: nombre_titular, dni_titular, datos_tarjeta, medio_pago no tienen
     * columna confirmada en g220 -> no se persisten aquí todavía.
     * NOTA: fecha_promesa -> asumido como 'fecha1' (pendiente de confirmar
     * con un registro real de PDP).
     */
    protected function mapearDatosGestion(Request $request): array
    {
        $datos = [
            'tip_con'    => $request->input('tipcon'),
            'tip_gb'     => $request->input('tipgb'),
            'tip_rb'     => $request->input('control'),
            'sub_res'    => $request->input('subres'),
            'comentario' => $request->input('comentario'),
            'cond_gral'  => $request->input('condicion'),
            'mon_pro'    => $request->input('monto_promesa') ?: 0,
            'moneda'     => $request->input('moneda_promesa') ?: '',
            'fec_reg'    => $request->input('fecha_promesa') ?: null,
            'control3'   => $request->input('nombre_titular') ?: '',
            'control4'   => $request->input('dni_titular') ?: '',
            'control5'   => $request->input('datos_tarjeta') ?: '',
            'condicion'  => $request->input('medio_pago') ?: '',
            'comenta2'   => $request->input('comenta2') ?: '',
            'uid'        => substr($request->input('comenta2') ?: '', 0, 20),
            'anexo'      => $request->input('anexo') ?: '',
            'con_cam'    => $request->input('con_cam') ?: '',
            'fec_con'    => now()->format('Y-m-d'),
            'fec_sin'    => now()->format('Y-m-d'),
            'control1'   => $request->input('hora_apertura'),
            'control2'   => now()->format('H:i:s'),
            'usuario'    => $request->input('usuario'),
            'telef_ges'  => $request->input('telef_ges'),
            'opcion'     => 'U',
            'corta'      => $request->input('control_grupo'),
            'fec_ges_ini'    => now()->format('Y-m-d') . ' ' . $request->input('hora_apertura'),
            'fec_ges_fin'    => now()->format('Y-m-d') . ' ' . now()->format('H:i:s'),
            'horainia'  => $request->input('hora_apertura'),
            'horafina'   => now()->format('H:i:s'),
        ];

        if ($request->hasFile('comprobante_confirmacion')) {
            $datos['comenta3'] = $request->file('comprobante_confirmacion')
                ->store('comprobantes_gestion', 'public');
        }

        return $datos;
    }

    /**
 * Actualiza g110.condicion y, solo si cambió respecto al valor anterior,
 * deja registro histórico en g225 (unico es serial, no se envía).
 */
protected function actualizarCondicionCliente(G110 $cliente, Request $request): void
{
    $condicionAnterior = $cliente->condicion;
    $condicionNueva    = $request->input('condicion');

    if ($condicionAnterior === $condicionNueva) {
        return; // no cambió, no se toca nada
    }

    $cliente->update(['condicion' => $condicionNueva]);

    DB::table('g225')->insert([
        'cod_deu'   => $cliente->cod_deu,
        'condicion' => $condicionNueva,
        'fecha'     => now()->format('Y-m-d'),
        'hora'      => now()->format('H:i'),
        'usuario'   => $request->input('usuario'),
    ]);
}
}
