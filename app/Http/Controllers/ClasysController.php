<?php

namespace App\Http\Controllers;

// 1. Importar el Form Request
use App\Http\Requests\GuardarGestionRequest;
// Importamos el servicio
use App\Services\KonnexiaService;
use App\Services\CatalogService;

use App\Models\Agenda;
use App\Models\G110;
use Illuminate\Support\Facades\DB; // <-- Importamos DB si no usas un modelo para los controles
use Illuminate\Http\Request;
use Carbon\Carbon;





class ClasysController extends Controller
{

    protected KonnexiaService $konnexiaService;
    protected CatalogService $catalogService;


    // Inyección de dependencias
    public function __construct(KonnexiaService $konnexiaService, CatalogService $catalogService)
    {
        $this->konnexiaService = $konnexiaService;
        $this->catalogService  = $catalogService;
    }

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

        // Obtener catálogos mediante el servicio // respuestas- sub_res / condiciones etc
        $catalogos = $this->catalogService->obtenerCatalogos();

        // Arrays de Promesas y Confirmaciones
        $codigosPromesaX      = $this->catalogService->obtenerCodigosPromesa();
        $codigosConfirmacionX = $this->catalogService->obtenerCodigosConfirmacion();


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

        $metricas = $cliente->gestiones()
            ->selectRaw("
            COUNT(CASE WHEN tip_con NOT IN ('IV', 'ML') THEN 1 END) as total,
            COUNT(CASE WHEN tip_con NOT IN ('IV', 'ML') AND corta IN ('CEF', 'CNE') THEN 1 END) as positives,
            COUNT(CASE WHEN tip_con = 'IV' THEN 1 END) as ivr,
            COUNT(CASE WHEN tip_con = 'ML' THEN 1 END) as mail
        ")
            ->first();

        $historialRapido = [
            'total'     => $metricas->total ?? 0,
            'positives' => $metricas->positives ?? 0,
            'ivr'       => $metricas->ivr ?? 0,
            'mail'      => $metricas->mail ?? 0,
            'sms'       => $cliente->gestiones_sms()->count(),
            'abono'     => 0
        ];

        // Obtenemos la fecha de cierre específica según el grupo del cliente
        // 1. Obtener objeto Carbon
        $fechaCierreObj = $this->catalogService->obtenerFechaCierre($cliente->grupo);

        // 2. Calcular días de diferencia
        $diasParaCierre = (int) Carbon::now()->diffInDays($fechaCierreObj);

        // 3. Formatear la fecha a día/mes/año para enviarla a la vista
        $fechaCierreFormateada = $fechaCierreObj->format('d/m/Y'); // Ej: 31/12/2026

        // esto es para el modal de correos
        $porcentajeEnvMail = (int) round(((float) ($cliente->datos1 ?? 0)) * 100);


        return view('crm.principal', array_merge([
            'cliente'              => $cliente,
            'otrasCuentas'         => $otrasCuentas,
            'recibos'              => $recibos,
            'historialRapido'      => $historialRapido,
            'diasParaCierre'       => $diasParaCierre,
            'fechaCierreFormateada' => $fechaCierreFormateada,
            'codigosPromesaX'      => $codigosPromesaX,
            'codigosConfirmacionX' => $codigosConfirmacionX,
            'telefonosSugeridos'   => $telefonosSugeridos,
            'paramsLlamada'        => $paramsLlamada,
            'promesaActiva'        => $promesaActiva,
            'porcentajeEnvMail'    => $porcentajeEnvMail
        ], $catalogos)); // <-- $catalogos pasa 'tipo_gestiones', 'respuestas', etc. automáticamente a la vista
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
                    'telef_ges'  => $item->telef_ges ?? 'N/A',
                    'estado'      => $item->tip_rb ?? 'SMS',
                    'comentario'  => $item->comentario ?? $item->detalle ?? 'Sin detalle',
                    'usuario'    => $item->usuario ?? '91',
                    'fec_reg'    => $item->fec_reg ?? '-',
                    'mon_pro'    => $item->mon_pro ?? 0,
                    'item'       => $item->item ?? '-',
                    'con_cam'    => $item->con_cam ?? '',
                ],
            ],

            'ivr' => [
                'relacion' => 'gestiones',
                'campos' => fn($item) => [
                    'telef_ges'  => $item->telef_ges ?? 'N/A',
                    'estado'      => 'IVR',
                    'comentario'  => $item->comentario ?? $item->detalle ?? 'Sin detalle',
                    'usuario'    => $item->usuario ?? '91',
                    'fec_reg'    => $item->fec_reg ?? '-',
                    'mon_pro'    => $item->mon_pro ?? 0,
                    'item'       => $item->item ?? '-',
                    'con_cam'    => $item->con_cam ?? '',
                ],
            ],

            'mail' => [
                'relacion' => 'gestiones',
                'campos' => fn($item) => [
                    'telef_ges'  => $item->comenta3 ?? $item->correo ?? 'N/A',
                    'estado'      => 'MAIL',
                    'comentario'  => $item->comentario ?? $item->asunto ?? 'Sin detalle',
                    'usuario'    => $item->usuario ?? '91',
                    'fec_reg'    => $item->fec_reg ?? '-',
                    'mon_pro'    => $item->mon_pro ?? 0,
                    'item'       => $item->item ?? '-',
                    'con_cam'    => $item->con_cam ?? '',
                ],
            ],

            'gestiones', 'positivas' => [
                'relacion' => 'gestiones',
                'campos' => function ($item) use ($codigosPromesa, $codigosConfirmacion) {
                    $codigoRespuesta = trim($item->tip_rb ?? '');

                    return [
                        'telef_ges'       => $item->telef_ges ?? 'N/A',
                        'estado'          => $item->tip_rb ?? 'GESTIÓN',
                        'comentario'      => $item->comentario ?? $item->detalle ?? 'Sin detalle',
                        'es_promesa'      => in_array($codigoRespuesta, $codigosPromesa, true),
                        'es_confirmacion' => in_array($codigoRespuesta, $codigosConfirmacion, true),
                        'usuario'         => $item->usuario ?? '91',
                        'fec_reg'         => $item->fec_reg ?? '-',
                        'mon_pro'         => $item->mon_pro ?? 0,
                        'item'            => $item->item ?? '-',
                        'con_cam'         => $item->con_cam ?? '',
                    ];
                },
            ],

            'editar_gestiones' => [
                'relacion' => 'gestiones',
                'campos' => fn($item) => [
                    'id'            => $item->item,
                    'respuesta'     => trim(($item->corta ?? '') . ' - ' . ($item->comentario ?? '')),
                    'sub_respuesta' => $item->sub_res ?? '',
                    'monto_pdp'     => $item->mon_pro ?? 0,
                    'condicion'     => $item->cond_gral ?? '',
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
        // 1. Obtener configuraciones mediante el servicio
        $codigosPromesa = $this->catalogService->obtenerCodigosPromesa();
        $codigosConfirmacion = $this->catalogService->obtenerCodigosConfirmacion();
        $config = $this->configHistorial($tipo, $codigosPromesa, $codigosConfirmacion);

        // 2. Cargar modelo principal
        $cliente = G110::findOrFail($id);

        // 3. Resolver la relación de forma explícita y segura
        $query = match ($config['relacion']) {
            'gestiones_sms' => $cliente->gestiones_sms(),
            default         => $cliente->gestiones(),
        };

        // 4. Aplicar filtros directamente según el tipo
        match ($tipo) {
            'positivas'        => $query->whereNotIn('tip_con', ['IV', 'ML'])->whereIn('corta', ['CEF', 'CNE']),
            'gestiones'        => $query->whereNotIn('tip_con', ['IV', 'ML']),
            'ivr'              => $query->whereIn('tip_con', ['IV']),
            'mail'             => $query->whereIn('tip_con', ['ML']),
            'editar_gestiones' => $query->whereNotIn('usuario', ['91']),
            default            => null,
        };

        // 5. Ordenar y Paginar
        $paginados = $query->orderBy('item', 'desc')->paginate(5);
        $total     = $paginados->total();
        $firstItem = $paginados->firstItem() ?? 0;

        // 6. Transformar la colección paginada
        $paginados->getCollection()->transform(function ($item, $key) use ($total, $firstItem, $config) {
            $numeroItem = $total - ($firstItem + $key - 1);

            // Formateo limpio de Fecha / Hora
            $fechaFormateada = 'N/A';
            if (!empty($item->fec_sin)) {
                $fechaRaw = is_object($item->fec_sin)
                    ? $item->fec_sin->format('Y-m-d')
                    : trim(explode(' ', $item->fec_sin)[0]);

                $horaRaw = !empty($item->control1) ? trim($item->control1) : '00:00:00';
                $fechaFormateada = "{$fechaRaw} {$horaRaw}";
            }

            return array_merge(
                [
                    'item_num' => $numeroItem,
                    'fecha'    => $fechaFormateada,
                ],
                ($config['campos'])($item)
            );
        });

        // 7. Retornar respuesta estructurada
        return response()->json([
            'data'         => $paginados->items(),
            'current_page' => $paginados->currentPage(),
            'last_page'    => $paginados->lastPage(),
            'first_item'   => $firstItem,
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

    // Inyectamos GuardarGestionRequest directamente en el método
    public function guardarGestion(GuardarGestionRequest $request, $id)
    {
        // La validación se ejecuta automáticamente ANTES de entrar a este método.

        // Buscar al cliente principal
        $cliente = G110::where('cod_deu', $id)->firstOrFail();

        // Variable 'accion' enviada por los botones ('grabar' o 'multiple')
        $accion = $request->input('accion', 'grabar');

        // 1. Guardar en Base de Datos (Gestión + Agendas)
        $itemsRegistrados = DB::transaction(function () use ($request, $cliente, $accion) {
            $items = [];
            $rowsToInsert = [];

            // Incluimos siempre la cuenta cliente principal
            $cuentasAProcesar = collect([$cliente]);

            // Si es 'multiple', buscamos las demás cuentas bajo el mismo nro_doc
            if ($accion === 'multiple' && !empty($cliente->nro_doc)) {
                $otrasCuentas = G110::where('nro_doc', trim($cliente->nro_doc))
                    ->where('cod_deu', '!=', $cliente->cod_deu)
                    ->get();

                $cuentasAProcesar = $cuentasAProcesar->merge($otrasCuentas);
            }

            // Mapeo delegado al Form Request
            $datosBase = $request->toDatabaseArray();

            // Generar los correlativos e insumos de cada cuenta
            foreach ($cuentasAProcesar as $cuenta) {
                $siguienteItem = DB::selectOne(
                    "SELECT nuevo_item_por_codigo(?) AS item",
                    [$cuenta->cod_deu]
                )->item;

                // Ensamblar la fila de la cuenta actual
                $rowsToInsert[] = array_merge($datosBase, [
                    'cod_deu' => $cuenta->cod_deu,
                    'item'    => $siguienteItem,
                    'cod_ban' => $cuenta->cod_ban,
                    'grupo'   => $cuenta->grupo,
                    'nro_cta' => $cuenta->nro_cta,
                ]);

                // Actualizar condición del cliente si hubo cambio
                $this->actualizarCondicionCliente($cuenta, $request);

                $items[] = [
                    'cod_deu' => $cuenta->cod_deu,
                    'item'    => $siguienteItem
                ];
            }

            // Inserción masiva única (Bulk Insert) en g220
            DB::table('g220')->insert($rowsToInsert);

            // Registrar en la tabla agendas si se solicita
            if ($request->boolean('agendar')) {
                Agenda::create([
                    'cod_deu'         => $cliente->cod_deu,
                    'fecha'           => $request->input('fec_agenda'),
                    'hora'            => $request->input('hor_agenda'),
                    'usuario'         => $request->input('usuario'),
                    'obs'             => $request->input('comentario'),
                    'cartera'         => $cliente->cod_ban,
                    'cod_ban'         => $cliente->cod_ban,
                    'usuario_creador' => $request->input('usuario'),
                ]);
            }

            return $items;
        });

        // 2. Consumir Konnexia usando el Servicio delegado
        $konnexiaRes = null;
        if ($request->filled('comenta2') && $accion !== 'grabar') {
            $konnexiaRes = $this->konnexiaService->finalizarLlamada($request);
        }

        // 3. Evaluar respuesta de Promesa y Confirmación para el Frontend
        $codigoControl = trim($request->input('control', ''));

        // --- EVALUACIÓN DE PROMESA ---
        $codigosPromesaX = $this->catalogService->obtenerCodigosPromesa();
        $esPromesa       = in_array($codigoControl, $codigosPromesaX, true);

        $promesaActivaData = null;
        if ($esPromesa) {
            $montoPromesaRaw = $request->input('monto_promesa');
            $montoPromesaFormateado = is_numeric($montoPromesaRaw) ? number_format((float)$montoPromesaRaw, 2, '.', ',') : null;

            $promesaActivaData = [
                'existe' => true,
                'fecha'  => $request->input('fecha_promesa_input') ?? $request->input('fecha_promesa'),
                'monto'  => $montoPromesaFormateado,
            ];
        }

        // --- EVALUACIÓN DE CONFIRMACIÓN ---
        $codigosConfirmacionX = $this->catalogService->obtenerCodigosConfirmacion();
        $esConfirmacion       = in_array($codigoControl, $codigosConfirmacionX, true);

        $confirmacionActivaData = null;
        if ($esConfirmacion) {
            $montoConfRaw = $request->input('monto_confirmacion');
            $montoConfFormateado = is_numeric($montoConfRaw) ? number_format((float)$montoConfRaw, 2, '.', ',') : null;

            $confirmacionActivaData = [
                'existe'             => true,
                'fecha_confirmacion' => $request->input('fecha_confirmacion_input') ?? $request->input('fecha_confirmacion'),
                'monto_confirmacion' => $montoConfFormateado,
            ];
        }

        // 4. Retornar Respuesta Unificada
        return response()->json([
            'success'             => true,
            'mensaje'             => ($accion === 'multiple' && count($itemsRegistrados) > 1)
                ? "Gestión registrada exitosamente en " . count($itemsRegistrados) . " cuentas vinculadas."
                : 'Gestión registrada correctamente.',
            'items'               => $itemsRegistrados,
            'accion'              => $accion,
            'promesa_activa'      => $promesaActivaData,
            'confirmacion_activa' => $confirmacionActivaData,
            'konnexia'            => $konnexiaRes
        ], 201);
    }


    public function actualizarGestion(GuardarGestionRequest $request, $id, $item)
    {
        // La validación se ejecuta automáticamente antes de entrar a esta función
        $cliente = G110::where('cod_deu', $id)->firstOrFail();

        DB::transaction(function () use ($request, $cliente, $item) {
            // Uso directo del método delegado

            // 1. Obtenemos el arreglo mapeado desde el Form Request
            $datos = $request->toDatabaseArray();
            // 2. Excluimos los tiempos originales y el campo de usuario original
            unset(
                $datos['grupo'],
                $datos['usuario'],
                $datos['fec_sin'],
                $datos['con_cam'],
                $datos['control1'],
                $datos['control2'],
                $datos['comenta2'],
                $datos['comenta3'],
                $datos['uid'],
                $datos['anexo'],
                $datos['telef_ges'],
                $datos['fec_ges_ini'],
                $datos['fec_ges_fin'],
                $datos['horainia'],
                $datos['horafina'],
            );

            // 3. Asignamos el usuario y fecha  a la columna de edición deseada
            $datos['control'] = $request->input('usuario'); // Reemplaza 'usuario_edit' por el nombre real de tu columna
            $datos['fecha1']     = now()->format('Y-m-d'); // Opcional: registrar fecha/hora de modificación

            DB::table('g220')
                ->where('cod_deu', $cliente->cod_deu)
                ->where('item', $item)
                ->update($datos);

            $this->actualizarCondicionCliente($cliente, $request);
        });

        return response()->json([
            'success' => true,
            'mensaje' => 'Gestión actualizada correctamente.',
        ]);
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
