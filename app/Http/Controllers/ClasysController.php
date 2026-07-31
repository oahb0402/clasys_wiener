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
    public function index($id)
    {
        // 1. Buscamos el cliente en la tabla 'g110' con sus cuentas asociadas en 'g110_cta'
        $cliente = G110::with('detalles')->findOrFail($id);

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
            ->where('tipo', 'TELEFONO') // <-- Filtra solo las respuestas activas
            ->orderBy('corta', 'asc') // Ajustado a 'descrip'
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
            ->select('codigo', 'descrip')
            ->where('activo', '1') // <-- Filtra solo las respuestas activas
            ->whereNotIn('codigo', ['AC', 'AG', 'AJ', 'AD', 'BQ', 'DB', 'DV', 'DC', 'EP', 'RF', 'SS', 'IF', 'IT', 'IN', 'MN', 'NG', 'NM', 'ND', 'NT', 'PC', 'PT', 'PH', 'PU', 'X1', 'Y1', 'PF', 'PV', 'VF', 'RR', 'RW', 'SM', 'ST', 'UT', 'UF', 'UM', 'UG', 'IC', 'RN', 'UP', 'ZP', 'CA'])
            ->orderBy('descrip', 'asc') // Ajustado a 'descrip'
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
        $diasParaCierre = Carbon::now()->diffInDays($fechaCierre);

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
            'telefonosSugeridos' => $telefonosSugeridos
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
}
