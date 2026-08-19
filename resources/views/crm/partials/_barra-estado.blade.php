<!-- 1. BARRA SUPERIOR DE ESTADO / DIALER (COMPACTA) -->
<div class="bg-slate-900 text-white rounded-xl px-4 py-2.5 shadow-md flex flex-wrap items-center justify-between gap-3 text-xs">
    <!-- TELÉFONO DE MARCADO -->
    <div class="flex items-center gap-2">
        <span class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
        </span>
        <span class="text-slate-400 font-medium">Marcando:</span>
        <span class="text-base font-bold tracking-wide text-emerald-400 font-mono">{{ $paramsLlamada['telf'] }}</span>
    </div>

    <!-- DATOS DE CAMPAÑA Y AGENTE -->
    <div class="flex flex-wrap items-center gap-2">
        <div class="bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700/60">
            <span class="text-slate-400">Tipo:</span>
            <span class="font-semibold text-amber-400 ml-1">{{ $paramsLlamada['accion_predictivo'] }}</span>
        </div>

        <div class="bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700/60">
            <span class="text-slate-400">Cierre:</span>
            <span class="font-semibold text-white ml-1">{{ $diasParaCierre }}d</span>
            <span class="text-[10px] text-slate-400 font-mono">(2026-12-31)</span>
        </div>

        <div class="bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700/60">
            <span class="text-slate-400">Usuario:</span>
            <span class="font-semibold text-amber-100 ml-1 font-mono">{{ $paramsLlamada['uid'] }}</span>
        </div>
    </div>
</div>
