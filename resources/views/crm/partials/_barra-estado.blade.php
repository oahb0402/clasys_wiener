<!-- 1. BARRA SUPERIOR DE ESTADO / DIALER (CON DROPDOWN EN USUARIO) -->
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

        <!-- MENÚ DESPLEGABLE CON JS VANILLA -->
<div class="relative inline-block text-left">
    <div class="bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700/60 flex items-center gap-1">
        <span class="text-slate-400">Usuario:</span>
        <button onclick="toggleMenuUsuario(event)"
                type="button"
                class="inline-flex items-center gap-1 font-semibold text-amber-100 font-mono hover:text-amber-300 transition-colors focus:outline-none">
            <span>{{ $paramsLlamada['uid'] }}</span>
            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    <div id="dropdown-usuario-dialer"
         class="hidden absolute right-0 mt-1.5 w-48 rounded-xl bg-slate-800 border border-slate-700 shadow-xl z-50 py-1 text-slate-200">
        <a href="https://webmail.clasaperu.com/"
        target="_blank"
        class="block px-3 py-1.5 hover:bg-slate-700/60 hover:text-amber-300">WebMail</a>
        {{-- <a href="#" class="block px-3 py-1.5 hover:bg-slate-700/60 hover:text-amber-300">Enlace 2</a> --}}
    </div>
</div>
    </div>
</div>

