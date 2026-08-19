<!-- TARJETA DE INFORMACIÓN DEL CLIENTE (COMPACTA) -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 space-y-3">
    <!-- ENCABEZADO: DATOS Y ACCIONES -->
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-lg font-bold text-slate-900 leading-tight">{{ $cliente->nom_deu }}</h1>
                <span class="bg-slate-100 text-slate-600 font-mono text-[11px] px-2 py-0.5 rounded border border-slate-200">
                    {{ $cliente->cod_deu }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                RUC/Doc: <span class="font-mono text-slate-700 font-medium">{{ $cliente->nro_doc }}</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button type="button"
                    onclick="abrirModalSolicitudCorreo()"
                    class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-3 py-1.5 rounded-lg transition-colors shadow-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Solicitud Mail
            </button>

            <span class="bg-red-50 text-red-700 font-semibold text-[11px] px-2.5 py-1 rounded-md border border-red-200">
                Camp: {{ $cliente->cod_ban }}-{{ $cliente->grupo }}
            </span>
            <span class="bg-blue-50 text-blue-700 font-semibold text-[11px] px-2.5 py-1 rounded-md border border-blue-200">
                Cond: {{ $cliente->condicion }}
            </span>
        </div>
    </div>

    <!-- MÉTRICAS Y DETALLES -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-xs">
        <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide block">Cód. Estudiante</span>
            <span class="font-bold text-slate-800 truncate block mt-0.5">{{ $cliente->nro_cta }}</span>
        </div>
        <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide block">Estado</span>
            <span class="font-bold font-mono text-slate-800 truncate block mt-0.5" title="ESTADO">{{ $cliente->nom_deu3 ?? '-' }}</span>
        </div>
        <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide block">Carrera</span>
            <span class="font-bold font-mono text-slate-800 truncate block mt-0.5" title="CARRERA">{{ $cliente->dir_fia ?? '-' }}</span>
        </div>
        <div class="bg-amber-50/60 p-2.5 rounded-lg border border-amber-100">
            <span class="text-[10px] font-semibold text-amber-700 uppercase tracking-wide block">Deuda Total</span>
            <span class="font-extrabold text-amber-900 block mt-0.5">S/ {{ number_format((float)($cliente->mon_ini ?? 0), 2) }}</span>
        </div>
        <div class="bg-emerald-50/60 p-2.5 rounded-lg border border-emerald-100">
            <span class="text-[10px] font-semibold text-emerald-700 uppercase tracking-wide block">Total c/ Descuento</span>
            <span class="font-extrabold text-emerald-800 block mt-0.5">S/ {{ number_format((float)($cliente->total_final ?? 0), 2) }}</span>
        </div>
    </div>
</div>
