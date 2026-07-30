<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clasys v3 - Panel de Gestión</title>
    <script src="https://cdn.tailwindcss.com"></script>


    <style>
        .active-tab {
            background-color: white;
            color: #2563eb;
            border: 1px solid #e2e8f0;
            border-bottom: 2px solid #2563eb;
        }

        .inactive-tab {
            color: #64748b;
            border: 1px solid transparent;
        }

        .inactive-tab:hover {
            color: #1e293b;
            background-color: #f1f5f9;
        }
    </style>
</head>

<body class="h-full font-sans text-slate-900 antialiased">

    <div class="min-h-full p-6 max-w-7xl mx-auto space-y-6">


        <div class="max-w-6xl mx-auto space-y-4">

            <!-- 1. BARRA SUPERIOR DE ESTADO / DIALER -->
            <div class="bg-slate-900 text-white rounded-xl p-4 shadow-md flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span class="text-sm font-medium text-slate-300">Marcando a:</span>
                    <span class="text-xl font-bold tracking-wide text-emerald-400">947 623 565</span>
                </div>

                <div class="flex items-center gap-6 text-sm">
                    <div class="bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                        <span class="text-slate-400">Tipo:</span> <span class="font-semibold text-amber-400">Saliente</span>
                    </div>
                    <div class="bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                        <span class="text-slate-400">Cierre de campaña:</span>
                        <span class="font-semibold text-white">{{ $diasParaCierre }} días</span>
                        <span class="text-xs text-slate-400">(2026-12-31)</span>
                    </div>
                </div>
            </div>

            <!-- 2. TARJETA DE INFORMACIÓN DEL CLIENTE -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <div class="flex flex-wrap justify-between items-start gap-4 border-b border-slate-100 pb-4 mb-4">
                    <div>
                        <span class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Titular de la Cuenta</span>
                        <h1 class="text-2xl font-bold text-slate-900 mt-0.5">{{ $cliente->nom_deu }}</h1>
                        <p class="text-sm text-slate-500 mt-1">
                            Código: <span class="font-mono font-medium text-slate-700">{{ $cliente->cod_deu }}</span> |
                            RUC: <span class="font-mono font-medium text-slate-700">{{ $cliente->nro_doc }}</span>
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <span class="bg-red-100 text-red-700 font-bold text-xs px-3 py-1.5 rounded-full uppercase tracking-wider border border-red-200">
                            Campaña: {{ $cliente->comentario }}
                        </span>
                        <span class="bg-blue-100 text-blue-700 font-bold text-xs px-3 py-1.5 rounded-full uppercase tracking-wider border border-blue-200">
                            Condición: {{ $cliente->condicion }}
                        </span>
                    </div>

                </div>

                <!-- Métricas rápidas de deuda -->
                <!-- RESUMEN DE CARRERA Y DEUDA -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-4">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-center sm:text-left">
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Cód. Estudiante</span>
                            <span class="text-sm font-bold font-mono text-slate-800">{{ $cliente->nro_cta }}</span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 sm:col-span-2">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Carrera</span>
                            <span class="text-sm font-bold text-slate-800 truncate block" title="ODONTOLOGÍA">{{ $cliente->dir_fia ?? '' }}</span>
                        </div>
                        <div class="bg-amber-50 p-3 rounded-lg border border-amber-100">
                            <span class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider block">Deuda Total</span>
                            <span class="text-sm font-extrabold text-amber-800">S/ {{ number_format((float)($cliente->mon_ini ?? 0), 2) }}</span>
                        </div>
                        <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-100">
                            <span class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider block">Total c/ Descuento</span>
                            <span class="text-base font-extrabold text-emerald-700">S/ {{ number_format((float)($cliente->total_final ?? 0), 2) }}</span>
                        </div>
                    </div>
                </div>
                <!-- 3. TABLA DE LA CUENTA / OPERACIONES -->
            </div>





            <!-- TABLA DE DETALLE DE RECIBOS / CUOTAS -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- CABECERA DE LA TABLA -->
                <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex flex-wrap justify-between items-center gap-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h2 class="font-bold text-slate-700 text-sm uppercase tracking-wide">Detalle de Recibos y Cuotas</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-slate-200 text-slate-700 px-2.5 py-1 rounded-full font-medium">
                            Moneda: Soles (PEN)
                        </span>
                    </div>
                </div>

                <!-- CUERPO / TABLA (AQUÍ VAN LAS CLASES) -->
                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="w-full text-left text-xs">
                        <!-- Agregamos sticky top-0 al thead para que los títulos de la tabla no se pierdan al hacer scroll -->
                        <thead class="bg-slate-100 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider sticky top-0">
                            <tr>
                                <th class="py-3 px-3 text-center">
                                    <!-- <input type="checkbox" id="check-all-recibos" class="h-4 w-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer"> -->
                                <th class="py-3 px-3">#</th>
                                </th>
                                <th class="py-3 px-3">Periodo</th>
                                <th class="py-3 px-3 text-center">N° Cuota</th>
                                <th class="py-3 px-3">Boleta / N°</th>
                                <th class="py-3 px-3">Concepto</th>
                                <th class="py-3 px-3 text-center">Fec. Venc.</th>
                                <th class="py-3 px-3 text-right">Importe</th>
                                <th class="py-3 px-3 text-center">Desc.</th>
                                <th class="py-3 px-3 text-right">Importe c/ Desc.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($recibos ?? [] as $recibo)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-2.5 px-3 text-center">
                                    <!-- <input type="checkbox" name="recibos_selected[]" value="{{ $recibo->id ?? $loop->index }}" checked class="h-4 w-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer"> -->
                                <td class="py-2.5 px-3 font-mono text-slate-600">{{ $loop->index+1 }}</td>
                                </td>
                                <td class="py-2.5 px-3 font-mono text-slate-600">{{ $recibo->tiser }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-slate-700">{{ $recibo->obs2 }}</td>
                                <td class="py-2.5 px-3 font-mono text-slate-800 font-semibold">{{ $recibo->nro_recibo }}</td>
                                <td class="py-2.5 px-3 text-slate-700 font-normal">{{ $recibo->afiliacion }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600 font-mono">{{ $recibo->fec_ven }}</td>
                                <td class="py-2.5 px-3 text-right text-slate-800 font-mono font-semibold">
                                    S/ {{ number_format((float)$recibo->mon_ini, 2) }}
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    @if(($recibo->dato4 ?? 0) > 0)
                                    <span class="bg-emerald-100 text-emerald-800 font-bold text-[11px] px-2 py-0.5 rounded-full">
                                        {{ $recibo->dato4 }}%
                                    </span>
                                    @else
                                    <span class="text-slate-400 font-mono">0%</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-700">
                                    @if(($recibo->dato4 ?? 0) > 0)
                                    <span class="bg-emerald-100 text-emerald-800 font-bold text-[11px] px-2 py-0.5 rounded-full">
                                        S/ {{ number_format($recibo->importe_calculado, 2) }}
                                    </span>
                                    @else
                                    <span class="text-slate-400 font-mono"> {{ $recibo->mon_ini }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-2.5 px-3 text-center"><input type="checkbox" checked class="h-4 w-4 text-blue-600 rounded border-slate-300"></td>
                                <td class="py-2.5 px-3 font-mono text-slate-600"></td>
                                <td class="py-2.5 px-3 text-center font-bold text-slate-700"></td>
                                <td class="py-2.5 px-3 font-mono text-slate-800 font-semibold"></td>
                                <td class="py-2.5 px-3 text-slate-700"></td>
                                <td class="py-2.5 px-3 text-center text-slate-600 font-mono"></td>
                                <td class="py-2.5 px-3 text-right text-slate-800 font-mono font-semibold"></td>
                                <td class="py-2.5 px-3 text-center"><span class="text-slate-400 font-mono"></span></td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-700"></td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>



            <!-- PANEL DE REGISTRO DE GESTIÓN -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                <!-- ENCABEZADO DEL PANEL -->
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Registrar Nueva Gestión</h3>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">CRM Cobranzas</span>
                </div>

                <div class="p-6 space-y-6">
                    <!-- BANNER DE RESUMEN DE INTERACCIONES -->
                    <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-slate-100">
                        <span class="text-xs font-bold text-slate-500 mr-1">Historial rápido:</span>

                        <!-- Gestiones Positivas -->
                        <button type="button" onclick="abrirHistorial('gestiones_positivas')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition-colors">
                            <span>Gest. Positivas:</span>
                            <span class="bg-emerald-200 text-emerald-900 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{$historialRapido['positives']}}</span>
                        </button>

                        <!-- Total Gestiones -->
                        <button type="button" onclick="abrirHistorial('total_gestiones')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                            <span>Total Gestiones:</span>
                            <span class="bg-blue-200 text-blue-900 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{$historialRapido['total']}}</span>
                        </button>

                        <!-- IVR -->
                        <button type="button" onclick="abrirHistorial('ivr')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors">
                            <span>IVR:</span>
                            <span class="bg-indigo-200 text-indigo-900 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{$historialRapido['ivr']}}</span>
                        </button>

                        <!-- Mensaje (SMS / WA) -->
                        <button type="button" onclick="abrirHistorial('sms')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-800 hover:bg-sky-200 transition-colors">
                            <span>Mensajes:</span>
                            <span class="bg-sky-200 text-sky-900 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{$historialRapido['sms']}}</span>
                        </button>

                        <!-- Mail -->
                        <button type="button" onclick="abrirHistorial('mail')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-100 text-violet-800 hover:bg-violet-200 transition-colors">
                            <span>Mail:</span>
                            <span class="bg-violet-200 text-violet-900 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{$historialRapido['mail']}}</span>
                        </button>

                        <!-- Abonos -->
                        <button type="button" onclick="abrirHistorial('abonos')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 hover:bg-rose-200 transition-colors">
                            <span>Abonos:</span>
                            <span class="bg-rose-200 text-rose-900 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{$historialRapido['abono']}}</span>
                        </button>
                    </div>

                    <!-- CHIPS / BOTONES DE RESPUESTA RÁPIDA -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Comentarios Rápidos</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="setComentario('Equivocado')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg border border-slate-200 transition-colors flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Equivocado
                            </button>
                            <button type="button" onclick="setComentario('Grabadora')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg border border-slate-200 transition-colors flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Grabadora
                            </button>
                            <button type="button" onclick="setComentario('No Contestan')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg border border-slate-200 transition-colors flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> No Contestan
                            </button>
                            <button type="button" onclick="setComentario('Corta Llamada')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg border border-slate-200 transition-colors flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Corta Llamada
                            </button>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <!-- FORMULARIO PRINCIPAL -->
                    <form action="#" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- COLUMNA IZQUIERDA: CONFIGURACIÓN DE LA GESTIÓN -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Gestión</label>
                                    <select id="select-tipcon" name="tipcon" class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                                        <option value="">-- SELECCIONE GESTION --</option>
                                        @foreach($tipo_gestiones as $tipo_gestion)
                                        <option value="{{ $tipo_gestion->codigo }}">
                                            {{ $tipo_gestion->codigo }} - {{ $tipo_gestion->descri }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Control / Respuesta</label>
                                    <select id="select-control"
                                        name="control"
                                        data-promesas-x="{{ json_encode($codigosPromesaX) }}"
                                        data-confirmaciones-x="{{ json_encode($codigosConfirmacionX) }}"
                                        class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                                        <option value="">-- SELECCIONE RESPUESTA --</option>
                                        @foreach($respuestas as $nombreGrupo => $listaRespuestas)
                                        <optgroup label="{{ $nombreGrupo }}" class="font-bold text-slate-900 bg-slate-100">
                                            @foreach($listaRespuestas as $item)
                                            <option value="{{ trim($item->codigo) }}" data-promesa="{{ $item->promesa }}" class="font-normal text-slate-700 bg-white">
                                                {{ $item->codigo }} - {{ $item->descrip }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="seccion-promesa" class="hidden bg-emerald-50/60 border border-emerald-200 rounded-xl p-5 space-y-4 transition-all">
                                    <div class="flex items-center gap-2 border-b border-emerald-200/60 pb-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h4 class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Detalle del Compromiso / Promesa de Pago</h4>
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-3">
                                        <!-- FILA 1 -->
                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Fecha Confirmacion</label>
                                            <input type="date" name="fecha_promesa" class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Monto Confirmacion</label>
                                            <input type="number" step="0.01" name="monto_promesa" placeholder="0.00" class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-900 text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Moneda</label>
                                            <select name="moneda_promesa" class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                                <option value="S">1 - Soles</option>
                                                <option value="D">2 - Dolares</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div id="seccion-confirmacion" class="hidden bg-emerald-50/60 border border-emerald-200 rounded-xl p-5 space-y-4 transition-all">

                                    <div class="flex items-center gap-2 border-b border-emerald-200/60 pb-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h4 class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Detalle de la Confirmacion</h4>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-3">
                                        <!-- FILA 1 -->
                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Fecha Confirmacion</label>
                                            <input type="date" name="fecha_promesa" class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Monto Confirmacion</label>
                                            <input type="number" step="0.01" name="monto_promesa" placeholder="0.00" class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-900 text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Moneda</label>
                                            <select name="moneda_promesa" class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                                <option value="S">1 - Soles</option>
                                                <option value="D">2 - Dolares</option>
                                            </select>
                                        </div>

                                        <!-- FILA 2 -->
                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Nombre Titular</label>
                                            <input type="text" name="nombre_titular" class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">DNI Titular</label>
                                            <input type="text" name="dni_titular" class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Datos Tarjeta</label>
                                            <input type="text" name="datos_tarjeta" class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <!-- FILA 3 -->
                                        <div>
                                            <label class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Medio de Pago</label>
                                            <select name="medio_pago" class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                                <option value="BCP">BCP</option>
                                                <option value="BBVA">BBVA</option>
                                            </select>
                                        </div>

                                        <!-- CAMPO COMPROBANTE CON COL-SPAN-2 (Ocupa 2 columnas) -->
                                        <div class="sm:col-span-2 lg:col-span-2">
                                            <label class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Comprobante de Pago</label>
                                            <input type="file"
                                                name="comprobante_confirmacion"
                                                id="comprobante_confirmacion"
                                                accept="image/*,.pdf"
                                                class="w-full text-xs text-slate-700 bg-white rounded-lg border border-emerald-300 cursor-pointer 
                                                        file:mr-3 file:py-2 file:px-4 file:rounded-l-lg file:border-0 
                                                        file:text-xs file:font-bold file:bg-emerald-600 file:text-white 
                                                        hover:file:bg-emerald-700 focus:outline-none transition-all">
                                        </div>
                                    </div>
                                </div>

                                <!-- SELECT SUB RESPUESTA -->
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Sub Respuesta</label>
                                    <select id="select-subres" name="subres" disabled class="w-full rounded-lg border border-slate-300 p-2.5 bg-slate-100 text-slate-800 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">-- SELECCIONE SUB RESPUESTA --</option>
                                        @foreach($sub_respuestas as $sub)
                                        <option value="{{ trim($sub->codigo) }}">
                                            {{ $sub->codigo }} - {{ $sub->descrip }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- COLUMNA DERECHA: COMENTARIOS Y CONTACTO -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Contacto</label>
                                    <select id="select-tipgb" name="tipgb" class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                                        <option value="">-- SELECCIONE CONTACTO --</option>
                                        @foreach($tipo_contactos as $tipo_contacto)
                                        <option value="{{ $tipo_contacto->codigo }}">
                                            {{ $tipo_contacto->codigo }} - {{ $tipo_contacto->descri }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Comentario / Observación</label>
                                    <textarea id="comentario" name="comentario" rows="4" placeholder="Escriba los detalles de la conversación..." class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-400"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Condición</label>
                                    <select name="condicion" class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                                        @foreach($condiciones as $cond)
                                        <option value="{{ $cond->codigo }}" {{ trim($cliente->condicion) == trim($cond->codigo) ? 'selected' : '' }}>
                                            {{ $cond->codigo }} - {{ $cond->descrip }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>



                        <!-- BARRA DE ACCIONES (BOTONES) -->
                        <div class="flex flex-col sm:flex-row justify-end items-center gap-3 border-t border-slate-100 pt-5">
                            <button type="submit" name="accion" value="multiple" class="w-full sm:w-auto px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider rounded-lg transition-colors border border-slate-200">
                                Grabar Múltiple
                            </button>
                            <button type="submit" name="accion" value="cerrar" class="w-full sm:w-auto px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs uppercase tracking-wider rounded-lg transition-colors shadow-sm">
                                Grabar y Cerrar
                            </button>
                            <button type="submit" name="accion" value="grabar" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-sm shadow-blue-200 transition-colors">
                                Grabar
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>


        <!-- MODAL DE HISTORIAL DETALLADO -->
        <div id="modal-historial" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-4xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">

                <!-- Header del Modal -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 id="modal-titulo" class="text-sm font-bold text-slate-800 uppercase tracking-wide">Detalle de Gestiones</h3>
                            <p class="text-[11px] text-slate-500">Historial de interacciones del cliente</p>
                        </div>
                    </div>
                    <button onclick="cerrarHistorial()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Tabla de Datos -->
                <div class="p-6">
                    <div class="min-h-[260px] max-h-[320px] overflow-y-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] tracking-wider sticky top-0 border-b border-slate-200">
                                <tr>
                                    <th class="p-3 w-12 text-center">#</th>
                                    <th class="p-3 whitespace-nowrap">Fecha / Hora</th>
                                    <th class="p-3">Teléfono</th>
                                    <th class="p-3">Control / Tipo</th>
                                    <th class="p-3">Comentario / Detalle</th>
                                </tr>
                            </thead>
                            <tbody id="modal-tabla-body" class="divide-y divide-slate-100">
                                <!-- Las filas dinámicas con paginación se renderizan aquí -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer con Controles de Paginación -->
                <div class="px-6 py-3.5 bg-slate-50/80 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <!-- Texto informativo de la página -->
                    <div class="text-xs text-slate-500">
                        Mostrando <span id="pag-inicio" class="font-bold text-slate-700">0</span> a
                        <span id="pag-fin" class="font-bold text-slate-700">0</span> de
                        <span id="pag-total" class="font-bold text-slate-700">0</span> registros
                    </div>

                    <!-- Botones de Navegación -->
                    <div class="flex items-center gap-1.5">
                        <button id="btn-pag-prev" onclick="cambiarPagina(-1)" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                            Anterior
                        </button>

                        <!-- Números de Página Dinámicos -->
                        <div id="pag-numeros" class="flex items-center gap-1"></div>

                        <button id="btn-pag-next" onclick="cambiarPagina(1)" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                            Siguiente
                        </button>

                        <button onclick="cerrarHistorial()" class="ml-3 px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>

            </div>
        </div>


        <!-- MODAL MODIFICAR GESTIÓN -->
        <div id="modal-modificar-gestion" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-5xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">

                <!-- Header del Modal -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                            <!-- Icono Lápiz / Edición -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Gestiones Registradas</h3>
                            <p class="text-[11px] text-slate-500">Seleccione el registro que desea editar o corregir</p>
                        </div>
                    </div>
                    <button onclick="cerrarModalModificar()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Cuerpo: Tabla con estilo Tailwind -->
                <div class="p-6">
                    <div class="max-h-80 overflow-y-auto overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] tracking-wider sticky top-0 border-b border-slate-200">
                                <tr>
                                    <th class="p-3 text-center">Acción</th>
                                    <th class="p-3 text-center">Item</th>
                                    <th class="p-3 whitespace-nowrap">Fecha / Horas</th>
                                    <th class="p-3">Respuesta Gestión</th>
                                    <th class="p-3">Sub Respuesta</th>
                                    <th class="p-3 text-center">Monto PDP</th>
                                    <th class="p-3 text-center">Condición</th>
                                    <th class="p-3">Teléfono</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">

                                <!-- FILA 1 -->
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-3 text-center">
                                        <button type="button" onclick="editarGestion(28)" class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition-colors inline-flex items-center gap-1 font-bold text-[11px]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            Editar
                                        </button>
                                    </td>
                                    <td class="p-3 text-center font-bold text-slate-800">28</td>
                                    <td class="p-3 whitespace-nowrap">
                                        <span class="font-semibold text-slate-700">2025-08-01</span>
                                        <span class="block text-[10px] text-slate-400">16:37:35 - 16:40:06</span>
                                    </td>
                                    <td class="p-3 font-medium text-slate-800">802 - Contestan y cuelgan</td>
                                    <td class="p-3 text-slate-500">9 - Reducción de ingresos</td>
                                    <td class="p-3 text-center font-mono">S/ 0.00</td>
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-bold rounded text-[10px]">GN</span>
                                    </td>
                                    <td class="p-3 font-mono">9*****860</td>
                                </tr>

                                <!-- FILA 2 -->
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-3 text-center">
                                        <button type="button" onclick="editarGestion(14)" class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition-colors inline-flex items-center gap-1 font-bold text-[11px]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            Editar
                                        </button>
                                    </td>
                                    <td class="p-3 text-center font-bold text-slate-800">14</td>
                                    <td class="p-3 whitespace-nowrap">
                                        <span class="font-semibold text-slate-700">2025-07-02</span>
                                        <span class="block text-[10px] text-slate-400">09:58:05 - 10:00:22</span>
                                    </td>
                                    <td class="p-3 font-medium text-slate-800">901 - Buzón de voz</td>
                                    <td class="p-3 text-slate-500">9 - Reducción de ingresos</td>
                                    <td class="p-3 text-center font-mono">S/ 0.00</td>
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-bold rounded text-[10px]">GN</span>
                                    </td>
                                    <td class="p-3 font-mono">9*****860</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 text-right">
                    <button onclick="cerrarModalModificar()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition-colors">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>

    </div>


    <!-- PANEL LATERAL DESLIZANTE (SLIDE-OVER) -->
    <div id="drawer-menu" class="fixed inset-0 z-50 overflow-hidden hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <!-- Fondo oscuro con Blur -->
        <div onclick="cerrarDrawer()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div class="w-screen max-w-md bg-white shadow-2xl border-l border-slate-200 flex flex-col justify-between animate-in slide-in-from-right duration-200">

                <!-- Encabezado y Contenido -->
                <div>
                    <!-- Header del Drawer -->
                    <div class="flex items-center justify-between p-5 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div>
                                <h3 id="drawer-titulo" class="text-sm font-bold text-slate-800 uppercase tracking-wide">Acciones Rápidas</h3>
                                <p id="drawer-subtitulo" class="text-[11px] text-slate-500">Código Cliente: <span class="font-bold text-slate-700">23000002593</span></p>
                            </div>
                        </div>
                        <button onclick="cerrarDrawer()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Tabs de Navegación (SOLO 3 OPCIONES) -->
                    <div class="flex border-b border-slate-100 bg-slate-50/30 px-3 pt-2 gap-1 overflow-x-auto">
                        <button onclick="cambiarTabDrawer('numeros')" id="tab-numeros" class="tab-btn active-tab px-3 py-2 text-xs font-bold rounded-t-lg transition-all">
                            + Números
                        </button>
                        <button onclick="cambiarTabDrawer('correos')" id="tab-correos" class="tab-btn inactive-tab px-3 py-2 text-xs font-bold rounded-t-lg transition-all">
                            + Correos
                        </button>
                        <button onclick="cambiarTabDrawer('menu')" id="tab-menu" class="tab-btn inactive-tab px-3 py-2 text-xs font-bold rounded-t-lg transition-all">
                            Más Opciones
                        </button>
                    </div>

                    <!-- CONTENIDO 1: AGREGAR NÚMERO -->
                    <div id="content-numeros" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Tipo de Teléfono</label>
                            <select id="nuevo_tipo_tel" class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="Celular">Celular</option>
                                <option value="Fijo">Fijo / Trabajo</option>
                                <option value="Referencia">Teléfono Referencia</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Número</label>
                            <input type="text" id="nuevo_numero" placeholder="Ej: 987654321" class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-xs font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <button type="button" onclick="guardarNuevoNumero()" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Guardar Teléfono
                        </button>
                    </div>

                    <!-- CONTENIDO 2: AGREGAR CORREO -->
                    <div id="content-correos" class="p-6 space-y-4 hidden">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Correo Electrónico</label>
                            <input type="email" id="nuevo_correo" placeholder="cliente@correo.com" class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <button type="button" onclick="guardarNuevoCorreo()" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Guardar Correo
                        </button>
                    </div>

                    <!-- CONTENIDO 3: MENÚ DE MÁS OPCIONES -->
                    <div id="content-menu" class="p-6 hidden">
                        <!-- Vista de Lista de Botones -->
                        <div id="sub-menu-lista" class="grid grid-cols-1 gap-2">
                            <button type="button" onclick="abrirModalModificar()" class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50/50 text-slate-700 hover:text-blue-700 text-xs font-bold transition-all group">
                                <span>Modificar Gestión</span>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <button type="button" onclick="mostrarFormularioCondicion()" class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50/50 text-slate-700 hover:text-blue-700 text-xs font-bold transition-all group">
                                <span>Cambiar Condición [5]</span>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                        <!-- Formulario Interno para Condición (Oculto por defecto) -->
                        <div id="sub-menu-condicion" class="space-y-4 hidden">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <span class="text-xs font-bold text-slate-700 uppercase">Cambiar Condición</span>
                                <button type="button" onclick="ocultarFormularioCondicion()" class="text-[11px] font-bold text-blue-600 hover:underline">
                                    ← Volver
                                </button>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Nueva Condición</label>
                                <select id="condicion_drawer" class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-xs font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="GN">GN - GESTION NEGATIVA</option>
                                    <option value="GP">GP - GESTION POSITIVA</option>
                                    <option value="PP">PP - PROMESA DE PAGO</option>
                                    <option value="IL">IL - ILOCALIZABLE</option>
                                </select>
                            </div>

                            <button type="button" onclick="guardarCondicion()" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow-md transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Guardar Cambio
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Footer del Drawer -->
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                    <span>Atajo activo: <kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded text-slate-600 font-mono font-bold">Alt + N</kbd></span>
                    <button onclick="cerrarDrawer()" class="text-slate-600 font-bold hover:underline">Cerrar</button>
                </div>

            </div>
        </div>
    </div>
    <!-- BOTÓN FLOTANTE (FAB) -->
    <div class="fixed bottom-6 right-6 z-40 group">
        <!-- Tooltip al pasar el cursor (Hover) -->
        <div class="absolute bottom-full right-0 mb-2 hidden group-hover:flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 text-white text-[11px] font-medium rounded-lg shadow-xl whitespace-nowrap animate-in fade-in zoom-in-95 duration-100">
            <span>Acciones Rápidas</span>
            <kbd class="px-1.5 py-0.5 bg-slate-700 rounded text-[10px] font-mono text-slate-200">Alt + N</kbd>
        </div>

        <!-- Botón Principal -->
        <button type="button"
            onclick="toggleDrawer()"
            class="flex items-center justify-center w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 hover:scale-105 active:scale-95 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
        </button>
    </div>


    <!-- Modal Historial de SMS / WhatsApp -->
<div id="modalSms" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden transform transition-all">
        
        <!-- Header -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl">
                    <!-- Icono Reloj/Historial -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 tracking-wide uppercase">MENSAJES / WHATSAPP ENVIADOS</h3>
                    <p class="text-xs text-gray-500">Historial de interacciones del cliente</p>
                </div>
            </div>
            <!-- Botón Cerrar (X) -->
            <button type="button" onclick="cerrarModalSms()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body / Tabla -->
        <div class="p-6">
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50/80 text-gray-500 font-semibold uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-center">#</th>
                            <th class="px-4 py-3">FECHA / HORA</th>
                            <th class="px-4 py-3">TELÉFONO</th>
                            <th class="px-4 py-3">TIPO</th>
                            <th class="px-4 py-3">COMENTARIO / DETALLE</th>
                        </tr>
                    </thead>
                    <tbody id="tbodySms" class="divide-y divide-gray-100 bg-white">
                        <!-- Las filas se cargan dinámicamente vía AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer / Paginación -->
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500">
            <!-- Indicador de registros -->
            <div id="infoPaginacionSms">
                Cargando registros...
            </div>

            <div class="flex items-center gap-3">
                <!-- Contenedor de Botones (Anterior - 1 2 3 - Siguiente) -->
                <div id="contenedorPaginacionSms" class="flex items-center gap-1">
                    <!-- Botones generados dinámicamente -->
                </div>

                <!-- Botón Cerrar -->
                <button type="button" onclick="cerrarModalSms()" class="px-4 py-2 bg-slate-800 text-white font-medium rounded-lg hover:bg-slate-700 transition-colors">
                    Cerrar
                </button>
            </div>
        </div>

    </div>
</div>
    

    <script>
        function setComentario(texto) {
            document.getElementById('comentario').value = 'El cliente se encuentra en estado: ' + texto;
        }
    </script>

    <script>
        //let clienteIdGlobal = '23000005834';

        let clienteIdActual = {{ $cliente->cod_deu ?? 0 }};

        //let clienteIdActual = {{ $cliente->id ?? 0 }};
let urlBaseSms = `/crm/gestion/${clienteIdActual}/historial-sms`;

// 1. Abrir y Cerrar Modal
function abrirHistorial(tipo) {
    if (tipo === 'sms' || tipo === 'total_gestiones') {
        document.getElementById('modalSms').classList.remove('hidden');
        cargarPaginaSms(1);
    }
}

function cerrarModalSms() {
    document.getElementById('modalSms').classList.add('hidden');
}

// 2. Carga vía Fetch Nativo
function cargarPaginaSms(pagina) {
    const tbody = document.getElementById('tbodySms');
    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                <span class="inline-block animate-spin mr-2">🌀</span> Cargando historial...
            </td>
        </tr>
    `;

    fetch(`${urlBaseSms}?page=${pagina}`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la red');
            return response.json();
        })
        .then(res => {
            // A. Dibuja Filas
            let htmlFilas = '';
            if (!res.data || res.data.length === 0) {
                htmlFilas = `<tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No se encontraron registros.</td></tr>`;
            } else {
                res.data.forEach(item => {
                    let badgeClass = item.estado === 'ANSWERED' 
                        ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' 
                        : 'bg-purple-50 text-purple-600 border border-purple-100';

                    htmlFilas += `
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3 text-center font-bold text-gray-700">${item.item_num}</td>
                            <td class="px-4 py-3 text-gray-600 font-mono whitespace-nowrap text-nowrap">
    ${item.fecha}
</td>
                            <td class="px-4 py-3 text-gray-600">${item.telefono}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${badgeClass}">
                                    ${item.estado}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">${item.comentario}</td>
                        </tr>
                    `;
                });
            }
            tbody.innerHTML = htmlFilas;

            // B. Leyenda
            document.getElementById('infoPaginacionSms').innerHTML = 
                `Mostrando <b class="text-gray-800">${res.first_item}</b> a <b class="text-gray-800">${res.last_item}</b> de <b class="text-gray-800">${res.total}</b> registros`;

            // C. Paginación
            renderizarBotonesPaginacion(res.current_page, res.last_page);
        })
        .catch(err => {
            tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-red-500">Error al cargar la información.</td></tr>`;
        });
}

// 3. Renderizado de Botones
function renderizarBotonesPaginacion(actual, ultima) {
    let html = '';

    if (actual > 1) {
        html += `<button onclick="cargarPaginaSms(${actual - 1})" class="px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-100 text-gray-600 font-medium transition-colors">Anterior</button>`;
    } else {
        html += `<button disabled class="px-3 py-1.5 border border-gray-100 rounded-lg text-gray-300 cursor-not-allowed">Anterior</button>`;
    }

    for (let i = 1; i <= ultima; i++) {
        if (i === actual) {
            html += `<button class="w-8 h-8 rounded-lg bg-blue-600 text-white font-bold">${i}</button>`;
        } else {
            html += `<button onclick="cargarPaginaSms(${i})" class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-600 font-medium transition-colors">${i}</button>`;
        }
    }

    if (actual < ultima) {
        html += `<button onclick="cargarPaginaSms(${actual + 1})" class="px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-100 text-gray-600 font-medium transition-colors">Siguiente</button>`;
    } else {
        html += `<button disabled class="px-3 py-1.5 border border-gray-100 rounded-lg text-gray-300 cursor-not-allowed">Siguiente</button>`;
    }

    document.getElementById('contenedorPaginacionSms').innerHTML = html;
}


       
    </script>

    <!-- Script para mostrar el div de promesas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectRespuesta = document.getElementById('select-control');
            const seccionPromesa = document.getElementById('seccion-promesa');
            const seccionConfirmacion = document.getElementById('seccion-confirmacion');

            if (!selectRespuesta || !seccionPromesa || !seccionConfirmacion) return;

            // Cargar listas de códigos desde los atributos data-
            const codigosPromesa = JSON.parse(selectRespuesta.getAttribute('data-promesas-x') || '[]');
            const codigosConfirmacion = JSON.parse(selectRespuesta.getAttribute('data-confirmaciones-x') || '[]');

            // Función auxiliar para ocultar y limpiar una sección
            function ocultarYLimpiar(seccion) {
                seccion.classList.add('hidden');
                seccion.querySelectorAll('input, select').forEach(el => {
                    el.required = false;
                    el.value = '';
                });
            }

            // Función auxiliar para mostrar y requerir campos de una sección
            function mostrarYRequerir(seccion) {
                seccion.classList.remove('hidden');
                seccion.querySelectorAll('input, select').forEach(el => {
                    el.required = true;
                });
            }

            selectRespuesta.addEventListener('change', function() {
                const codigoSeleccionado = String(this.value).trim();

                // 1. Validar si es Promesa
                if (codigoSeleccionado && codigosPromesa.includes(codigoSeleccionado)) {
                    mostrarYRequerir(seccionPromesa);
                    ocultarYLimpiar(seccionConfirmacion);
                }
                // 2. Validar si es Confirmación
                else if (codigoSeleccionado && codigosConfirmacion.includes(codigoSeleccionado)) {
                    mostrarYRequerir(seccionConfirmacion);
                    ocultarYLimpiar(seccionPromesa);
                }
                // 3. Ninguna de las dos
                else {
                    ocultarYLimpiar(seccionPromesa);
                    ocultarYLimpiar(seccionConfirmacion);
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectRespuesta = document.getElementById('select-control');
            const selectSubrespuesta = document.getElementById('select-subres');

            if (!selectRespuesta || !selectSubrespuesta) return;

            // Guardamos copia de todas las opciones excepto la vacía por defecto
            const todasLasOpciones = Array.from(selectSubrespuesta.querySelectorAll('option'))
                .filter(opt => opt.value !== '')
                .map(opt => opt.cloneNode(true));

            selectRespuesta.addEventListener('change', function() {
                const valorSeleccionado = String(this.value).trim();
                const primerCaracter = valorSeleccionado.charAt(0);

                // Resetear select de sub-respuestas
                selectSubrespuesta.innerHTML = '<option value="">-- SELECCIONE SUB RESPUESTA --</option>';

                if (!valorSeleccionado) {
                    selectSubrespuesta.disabled = true;
                    selectSubrespuesta.classList.add('bg-slate-100');
                    return;
                }

                // ¿Empieza con 2, 3 o 4?
                const esGrupoEspecial = ['2', '3', '4'].includes(primerCaracter);

                // Aplicamos la lógica invertida/exclusiva sobre el código "12"
                const opcionesFiltradas = todasLasOpciones.filter(option => {
                    const codigoSub = String(option.value).trim();

                    if (esGrupoEspecial) {
                        // Muestra TODAS menos el código 12
                        return codigoSub !== '12';
                    } else {
                        // Muestra SOLO el código 12
                        return codigoSub === '12';
                    }
                });

                // Cargar las opciones filtradas
                if (opcionesFiltradas.length > 0) {
                    opcionesFiltradas.forEach(opt => {
                        selectSubrespuesta.appendChild(opt.cloneNode(true));
                    });
                    selectSubrespuesta.disabled = false;
                    selectSubrespuesta.classList.remove('bg-slate-100');

                    // <-- SI NO EMPIEZA CON 2, 3 O 4, SELECCIONA EL CÓDIGO 12 AUTOMÁTICAMENTE
                    if (!esGrupoEspecial) {
                        selectSubrespuesta.value = '12';
                    }
                } else {
                    selectSubrespuesta.disabled = true;
                    selectSubrespuesta.classList.add('bg-slate-100');
                }
            });
        });
    </script>


    <script>
        document.addEventListener('keydown', function(event) {
            // Ejemplo de combinación: Alt + N (Ajusta a la tuya)
            if (event.altKey && (event.key === 'n' || event.key === 'N')) {
                event.preventDefault();
                toggleDrawer();
            }

            // Tecla ESC para cerrar
            if (event.key === 'Escape') {
                cerrarDrawer();
            }
        });

        function toggleDrawer() {
            const drawer = document.getElementById('drawer-menu');
            drawer.classList.toggle('hidden');
            if (!drawer.classList.contains('hidden')) {
                document.getElementById('nuevo_numero').focus();
            }
        }

        function cerrarDrawer() {
            document.getElementById('drawer-menu').classList.add('hidden');
        }

        function cambiarTabDrawer(tab) {
            const tabs = ['numeros', 'correos', 'menu'];

            tabs.forEach(t => {
                const content = document.getElementById(`content-${t}`);
                const btnTab = document.getElementById(`tab-${t}`);

                if (content) content.classList.add('hidden');
                if (btnTab) {
                    btnTab.classList.remove('active-tab');
                    btnTab.classList.add('inactive-tab');
                }
            });

            const contentActivo = document.getElementById(`content-${tab}`);
            const tabActivo = document.getElementById(`tab-${tab}`);

            if (contentActivo) contentActivo.classList.remove('hidden');
            if (tabActivo) {
                tabActivo.classList.remove('inactive-tab');
                tabActivo.classList.add('active-tab');
            }

            // Resetear formulario interno si sale de Más Opciones
            if (tab !== 'menu') {
                ocultarFormularioCondicion();
            }
        }
    </script>

    <script>
        function abrirModalModificar() {
            // 1. Cerrar el drawer lateral primero
            if (typeof cerrarDrawer === 'function') {
                cerrarDrawer();
            } else {
                document.getElementById('drawer-menu').classList.add('hidden');
            }

            // 2. Mostrar el modal de gestiones
            document.getElementById('modal-modificar-gestion').classList.remove('hidden');
        }

        function cerrarModalModificar() {
            document.getElementById('modal-modificar-gestion').classList.add('hidden');
        }



        // Mostrar el select de condición dentro de Más Opciones
        function mostrarFormularioCondicion() {
            document.getElementById('sub-menu-lista').classList.add('hidden');
            document.getElementById('sub-menu-condicion').classList.remove('hidden');
        }

        // Volver a la lista de opciones
        function ocultarFormularioCondicion() {
            document.getElementById('sub-menu-condicion').classList.add('hidden');
            document.getElementById('sub-menu-lista').classList.remove('hidden');
        }
    </script>
</body>

</html>