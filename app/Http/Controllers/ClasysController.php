<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\G110;
use Illuminate\Support\Facades\DB; // <-- Importamos DB si no usas un modelo para los controles
use App\Models\G220;
use App\Models\G220Sms;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClasysController extends Controller
{
    public function index(Request $request)
{
    // 1. Obtiene cod_deu de la Query String ?cod_deu=... o ?id=...
    $id = $request->query('cod_deu') ?? $request->query('id');

    if (!$id) {
        abort(400, 'El parámetro cod_deu es requerido en la URL.');
    }

    // 2. Buscamos el cliente especificando el campo cod_deu
    $cliente = G110::with('detalles')
        ->where('cod_deu', $id)
        ->firstOrFail();

    // Captura los datos enviados por el marcador predictivo
    $paramsLlamada = [
        'telf'              => $request->query('telf'),
        'uid'               => $request->query('uid'),
        'campania'          => $request->query('campania'),
        'idllamada'         => $request->query('idllamada'),
        'extension'         => $request->query('extension'),
        'accion_predictivo' => $request->query('accion_predictivo'),
    ];

    // Buscar en g110 otros clientes/cuentas con el mismo nro_doc
    $otrasCuentas = collect();
    if (!empty($cliente->nro_doc)) {
        $otrasCuentas = G110::select('cod_deu', 'grupo', 'condicion', 'nro_cta', 'ult_mov', 'fec_ini')
            ->where('nro_doc', trim($cliente->nro_doc))
            ->where('cod_deu', '!=', $cliente->cod_deu)
            ->get();
    }

    // Recibos pendientes
    $recibos = $cliente->detalles->where('estado', '');

    // Listados de catálogos f190
    $tipo_gestiones = DB::table('f190')
        ->select(DB::raw("SUBSTR(codigo, 3, 3) AS codigo"), 'descri')
        ->where('activo', '1')
        ->whereIn('codigo', ['TBTM', 'TBTC', 'TBML', 'TBMT', 'TBWA'])
        ->orderBy('descri', 'asc')
        ->get();

    $tipo_contactos = DB::table('f190')
        ->select(DB::raw("SUBSTR(codigo, 3, 3) AS codigo"), 'descri')
        ->where('activo', '1')
        ->where('codigo', 'LIKE', 'UN%')
        ->orderBy('descri', 'asc')
        ->get();

    // Respuestas y Sub-respuestas
    $respuestas = DB::table('respuestas')
        ->select('codigo', 'descrip', 'corta', 'promesa')
        ->where('activo', '1')
        ->where('tipo', 'TELEFONO')
        ->orderBy('corta', 'asc')
        ->orderBy('descrip', 'asc')
        ->get()
        ->groupBy('corta');

    $sub_respuestas = DB::table('sub_respuestas')
        ->select('codigo', 'descrip')
        ->where('activo', '1')
        ->where('tipo', 'TELEFONO')
        ->orderByRaw('codigo::int ASC')
        ->get();

    // Condiciones activas
    $condiciones = DB::table('condiciong110')
        ->select(DB::raw('TRIM(codigo) as codigo'), 'descrip')
        ->where('activo', '1')
        ->whereNotIn(DB::raw('TRIM(codigo)'), ['AC', 'AG', 'AJ', 'AD', 'BQ', 'DB', 'DV', 'DC', 'EP', 'RF', 'SS', 'IF', 'IT', 'IN', 'MN', 'NG', 'NM', 'ND', 'NT', 'PC', 'PT', 'PH', 'PU', 'X1', 'Y1', 'PF', 'PV', 'VF', 'RR', 'RW', 'SM', 'ST', 'UT', 'UF', 'UM', 'UG', 'IC', 'RN', 'UP', 'ZP', 'CA'])
        ->orderBy('descrip', 'asc')
        ->get();

    // Arrays de Promesas y Confirmaciones
    $codigosPromesaX = $this->obtenerCodigosPromesa();
    $codigosConfirmacionX = $this->obtenerCodigosConfirmacion();

    $telefonosSugeridos = [];

    // Promesa activa en g220
    $promesaActivaQuery = DB::table('g220')
        ->where('cod_deu', $cliente->cod_deu)
        ->whereIn('tip_rb', $codigosPromesaX)
        ->where('fec_reg', '>=', now()->format('Y-m-d'))
        ->orderBy('item', 'desc')
        ->first();

    $promesaActiva = $promesaActivaQuery ? [
        'existe' => true,
        'fecha'  => $promesaActivaQuery->fec_reg ?? null,
        'monto'  => $promesaActivaQuery->mon_pro ?? null,
    ] : null;

    // Métricas rápidas
    $historialRapido = [
        'total' => $cliente->gestiones()
            ->whereNotIn('tip_con', ['IV', 'ML'])
            ->count(),

        'positives' => $cliente->gestiones()
            ->whereNotIn('tip_con', ['IV', 'ML'])
            ->whereIn('corta', ['CEF', 'CNE'])
            ->count(),

        'ivr' => $cliente->gestiones()
            ->where('tip_con', 'IV')
            ->count(),

        'mail' => $cliente->gestiones()
            ->whereIn('tip_con', ['ML'])
            ->count(),

        'sms' => $cliente->gestiones_sms()
            ->count(),

        'abono' => 0
    ];

    // Días al cierre
    $fechaCierre = Carbon::create(2026, 12, 31);
    $diasParaCierre = (int) Carbon::now()->diffInDays($fechaCierre);

    return view('crm.principal', [
        'cliente'              => $cliente,
        'otrasCuentas'         => $otrasCuentas,
        'recibos'              => $recibos,
        'historialRapido'      => $historialRapido,
        'diasParaCierre'       => $diasParaCierre,
        'tipo_gestiones'       => $tipo_gestiones,
        'tipo_contactos'       => $tipo_contactos,
        'respuestas'           => $respuestas,
        'sub_respuestas'       => $sub_respuestas,
        'codigosPromesaX'      => $codigosPromesaX,
        'codigosConfirmacionX' => $codigosConfirmacionX,
        'condiciones'          => $condiciones,
        'telefonosSugeridos'   => $telefonosSugeridos,
        'paramsLlamada'        => $paramsLlamada,
        'promesaActiva'        => $promesaActiva
    ]);
}


    // === 1. Helper reusable — agrégalo como método privado de la clase ===
    // (evita repetir la consulta que ya tienes en index())
    protected function obtenerCodigosPromesa(): array
    {
        return DB::table('r_respuestas_x')
            ->where('tipo', 'PROMESA')
            ->pluck('codigo')
            ->toArray();
    }

    // === 1. Helper reusable — agrégalo como método privado de la clase ===
    // (evita repetir la consulta que ya tienes en index())
    protected function obtenerCodigosConfirmacion(): array
    {
        return DB::table('r_respuestas_x')
            ->where('tipo', 'CONFIRMACION')
            ->pluck('codigo')
            ->toArray();
    }


    /**
     * Configuración por tipo de historial: qué relación de G110 consultar
     * y cómo mapear sus columnas al formato genérico que espera el frontend
     * (item_num, fecha, contacto, estado, comentario).
     **/

    protected function configHistorial(string $tipo, array $codigosPromesa = [], array $codigosConfirmacion = []): array
    {
        return match ($tipo) {
            'sms' => [
                'relacion' => 'gestiones_sms',
                'campos' => fn($item) => [
                    'telef_ges'   => $item->telef_ges ?? 'N/A',
                    'estado'     => $item->tip_rb ?? 'SMS',
                    'comentario' => $item->comentario ?? $item->detalle ?? 'Sin detalle',
                    'usuario'   => $item->usuario ?? '91',
                    'fec_reg'   => $item->fec_reg ?? '-',
                    'mon_pro'   => $item->mon_pro ?? 0,
                    'item'   => $item->item ?? '-',
                    'con_cam'   => $item->con_cam ?? '',
                ],
            ],

            'ivr' => [
                'relacion' => 'gestiones', // <-- confirma el nombre real de esta relación en G110
                'campos' => fn($item) => [
                    'telef_ges'   => $item->telef_ges ?? 'N/A',
                    'estado'     => 'IVR',
                    'comentario' => $item->comentario ?? $item->detalle ?? 'Sin detalle',
                    'usuario'   => $item->usuario ?? '91',
                    'fec_reg'   => $item->fec_reg ?? '-',
                    'mon_pro'   => $item->mon_pro ?? 0,
                    'item'   => $item->item ?? '-',
                    'con_cam'   => $item->con_cam ?? '',
                ],
            ],

            'mail' => [
                'relacion' => 'gestiones', // <-- confirma el nombre real
                'campos' => fn($item) => [
                    'telef_ges'   => $item->comenta3 ?? $item->correo ?? 'N/A', // <-- confirma la columna del correo
                    'estado'     => 'MAIL',
                    'comentario' => $item->comentario ?? $item->asunto ?? 'Sin detalle',
                    'usuario'   => $item->usuario ?? '91',
                    'fec_reg'   => $item->fec_reg ?? '-',
                    'mon_pro'   => $item->mon_pro ?? 0,
                    'item'   => $item->item ?? '-',
                    'con_cam'   => $item->con_cam ?? '',
                ],
            ],
            'gestiones', 'positivas' => [
                'relacion' => 'gestiones',
                'campos' => function ($item) use ($codigosPromesa, $codigosConfirmacion) {
                    $codigoRespuesta = trim($item->tip_rb ?? '');

                    return [
                        'telef_ges'    => $item->telef_ges ?? 'N/A',
                        'estado'      => $item->tip_rb ?? 'GESTIÓN',
                        'comentario'  => $item->comentario ?? $item->detalle ?? 'Sin detalle',
                        'es_promesa'  => in_array($codigoRespuesta, $codigosPromesa, true),
                        'es_confirmacion' => in_array($codigoRespuesta, $codigosConfirmacion, true), // <-- Agregado
                        'usuario'   => $item->usuario ?? '91',
                        'fec_reg'   => $item->fec_reg ?? '-',
                        'mon_pro'   => $item->mon_pro ?? 0,
                        'item'   => $item->item ?? '-',
                        'con_cam'   => $item->con_cam ?? '',
                    ];
                },
            ],

            /* 'positivas' => [
                'relacion' => 'gestiones',
                'campos' => function ($item) use ($codigosPromesa, $codigosConfirmacion) {
                    $codigoRespuesta = trim($item->tip_rb ?? '');

                    return [
                        'telef_ges'    => $item->telef_ges ?? 'N/A',
                        'estado'      => $item->tip_rb ?? 'GESTIÓN',
                        'comentario'  => $item->comentario ?? $item->detalle ?? 'Sin detalle',
                        'es_promesa'  => in_array($codigoRespuesta, $codigosPromesa, true),
                        'es_confirmacion' => in_array($codigoRespuesta, $codigosConfirmacion, true), // <-- Agregado
                        'usuario'    => $item->usuario ?? '91',
                        'fec_reg'   => $item->fec_reg ?? '-',
                        'mon_pro'   => $item->mon_pro ?? 0,
                        'item'   => $item->item ?? '-',
                        'con_cam'   => $item->con_cam ?? '',
                    ];
                },
            ], */
            'editar_gestiones' => [
                'relacion' => 'gestiones',
                'campos' => fn($item) => [
                    // 'id' es el identificador real que se usa en editarGestion(id) desde el botón "Editar"
                    'id'            => $item->item,
                    'respuesta'     => trim(($item->corta ?? '') . ' - ' . ($item->comentario ?? '')), // <-- confirma: ¿de dónde sale "802 - Contestan y cuelgan"? ¿es una relación a la tabla "respuestas"?
                    'sub_respuesta' => $item->sub_res ?? '',
                    'monto_pdp'     => $item->mon_pro ?? 0,
                    'condicion'     => $item->condicion ?? '',
                    'telefono'      => $item->telef_ges ?? '',
                    'hora'          => $item->control1 ?? '',
                    'usuario'       => $item->usuario ?? '91',
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
        $codigosPromesa = $this->obtenerCodigosPromesa();
        $codigosConfirmacion = $this->obtenerCodigosConfirmacion();
        $config = $this->configHistorial($tipo, $codigosPromesa, $codigosConfirmacion);

        $query = G110::findOrFail($id)->{$config['relacion']}()
            ->orderBy('item', 'desc');

        // Filtros según tipo
        // Filtros según tipo
        match ($tipo) {
            'positivas'        => $query->whereNotIn('tip_con', ['IV', 'ML'])->whereIn('corta', ['CEF', 'CNE']),
            'gestiones'        => $query->whereNotIn('tip_con', ['IV', 'ML']),
            'ivr'              => $query->whereIn('tip_con', ['IV']),
            'mail'             => $query->whereIn('tip_con', ['ML']),
            'editar_gestiones' => $query->whereNotIn('usuario', ['91']),
            default            => null
        };

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
        // Pasamos $id a validarGestion para que pueda consultar las promesas del cliente
        $this->validarGestion($request, $id);

        $cliente = G110::findOrFail($id);
        // Leemos la variable 'accion' que envían tus botones
        $accion  = $request->input('accion', 'grabar');

        $itemsRegistrados = DB::transaction(function () use ($request, $cliente, $accion) {
            $items = [];

            // 1. Incluimos siempre la cuenta cliente principal
            $cuentasAProcesar = collect([$cliente]);

            // 2. Si es 'multiple', buscamos todas las demás cuentas registradas bajo el mismo nro_doc en g110
            if ($accion === 'multiple' && !empty($cliente->nro_doc)) {
                $otrasCuentas = G110::where('nro_doc', trim($cliente->nro_doc))
                    ->where('cod_deu', '!=', $cliente->cod_deu)
                    ->get();

                $cuentasAProcesar = $cuentasAProcesar->merge($otrasCuentas);
            }

            // 3. Iteramos para guardar en g220 de cada cuenta
            foreach ($cuentasAProcesar as $cuenta) {
                $siguienteItem = DB::selectOne(
                    "SELECT nuevo_item_por_codigo(?) AS item",
                    [$cuenta->cod_deu]
                )->item;

                $datos = $this->mapearDatosGestion($request);
                $datos['cod_deu'] = $cuenta->cod_deu;
                $datos['item']    = $siguienteItem;
                $datos['cod_ban'] = $cuenta->cod_ban;
                $datos['grupo']   = $cuenta->grupo;
                $datos['nro_cta'] = $cuenta->nro_cta;

                DB::table('g220')->insert($datos);

                $this->actualizarCondicionCliente($cuenta, $request);

                $items[] = [
                    'cod_deu' => $cuenta->cod_deu,
                    'item'    => $siguienteItem
                ];
            }

            // 1. Validar los datos recibidos desde la interfaz
            $validated = $request->validate([
                'fec_agenda' => 'required_if:agendar,1|nullable|date',
                'hor_agenda' => 'required_if:agendar,1|nullable|date_format:H:i',
            ]);
            // 2. Registrar en la tabla `agendas` solo si se marcó el checkbox
            if ($request->boolean('agendar')) {
                Agenda::create([
                    'cod_deu'         => $cuenta->cod_deu,
                    'fecha'           => $validated['fec_agenda'],
                    'hora'            => $validated['hor_agenda'],
                    'usuario'         => $request->input('usuario'),
                    'obs'             => $request->input('comentario'),
                    'cartera'         => $cuenta->cod_ban,
                    'cod_ban'         => $cuenta->cod_ban,
                    'usuario_creador' => $request->input('usuario'),
                ]);
            }

            return $items;
        });

        $total = count($itemsRegistrados);

        // 1. OBTENER LOS CÓDIGOS LLAMANDO AL MÉTODO AUXILIAR
        $codigosPromesaX = $this->obtenerCodigosPromesa();
        // 2. Verificar si la gestión guardada es una Promesa
        $codigoControl = trim($request->input('control', ''));
        $esPromesa = in_array($codigoControl, $codigosPromesaX, true);

        // 3. Estructurar los datos de la promesa para la respuesta JSON
        $promesaActivaData = null;
        if ($esPromesa) {
            $montoRaw = $request->input('monto_promesa');
            // Formatea a 2 decimales (ej: 2.00 o 1,500.50)
            $montoFormateado = is_numeric($montoRaw) ? number_format((float)$montoRaw, 2, '.', ',') : null;
            $promesaActivaData = [
                'existe' => true,
                'fecha'  => $request->input('fecha_promesa'),
                'monto'  => $montoFormateado,
            ];
        }

        return response()->json([
            'success' => true,
            'mensaje' => ($accion === 'multiple' && $total > 1)
                ? "Gestión registrada exitosamente en {$total} cuentas vinculadas."
                : 'Gestión registrada correctamente.',
            'items'   => $itemsRegistrados,
            'accion'  => $accion,
            'promesa_activa' => $promesaActivaData

        ], 201);
    }

    public function actualizarGestion(Request $request, $id, $item)
    {
        // Se recomienda agregar validación previa
        $this->validarGestion($request);

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

    protected function validarGestion(Request $request, $clienteId = null): void
    {
        $request->validate([
            'tipcon'    => 'required|string',
            'control'   => 'required|string',
            'usuario'   => 'required|string',
            'telef_ges' => 'nullable|string|max:15',
        ]);

        // OBTENER LOS CÓDIGOS DE PROMESA
        $codigosPromesa = $this->obtenerCodigosPromesa();
        $codigosConfirmacion = $this->obtenerCodigosConfirmacion(); // Si aplica

        $codigoControl  = trim($request->input('control', ''));
        $fechaPromesa        = $request->input('fecha_promesa');

        $hoy    = now()->toDateString();
        $manana = now()->addDay()->toDateString();

        // Validar rango para Secciones de Promesa (Hoy y Mañana)
        if (in_array($codigoControl, $codigosPromesa, true) && $fechaPromesa) {
            if ($fechaPromesa < $hoy || $fechaPromesa > $manana) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'fecha_promesa' => ['La fecha de la promesa solo puede ser hoy o mañana.']
                ]);
            }
        }

        // Validar rango para Secciones de Confirmación (Desde el 1 del mes hasta hoy)
        if (in_array($codigoControl, $codigosConfirmacion, true) && $fechaPromesa) {
            $inicioMes = now()->startOfMonth()->toDateString();

            if ($fechaPromesa < $inicioMes || $fechaPromesa > $hoy) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'fecha_promesa' => ['La fecha de confirmación debe pertenecer al mes en curso hasta el día de hoy.']
                ]);
            }
        }

        // SI LA GESTIÓN ACTUAL ES UNA PROMESA
        if (in_array($codigoControl, $codigosPromesa, true)) {
            // Obtenemos el cod_deu enviado o buscando en el cliente
            $codDeu = $request->input('cod_deu');

            if (!$codDeu && $clienteId) {
                $codDeu = DB::table('g110')->where('cod_deu', $clienteId)->value('cod_deu');
            }

            if ($codDeu) {
                // Verificar si el cliente ya registra una promesa en la tabla g220
                $tienePromesaActiva = DB::table('g220')
                    ->where('cod_deu', $codDeu)
                    ->where('tip_rb', $codigoControl)
                    //->whereIn('tip_rb', $codigosPromesa)
                    ->where('fec_reg', '>=', now()->format('Y-m-d'))
                    ->exists();

                if ($tienePromesaActiva) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'control' => ["El cliente ya cuenta con una promesa activa bajo el código {$codigoControl}. No es posible repetir esta gestión."]
                    ]);
                }
            }
        }
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
