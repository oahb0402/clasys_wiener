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

        <div class="flex flex-wrap items-center gap-2">
            <!-- BOTÓN PARA ABRIR EL MODAL DE SOLICITUD DE CORREO -->
            <button type="button"
                    onclick="abrirModalSolicitudCorreo()"
                    class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3.5 py-1.5 rounded-full uppercase tracking-wider transition-colors shadow-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Solicitud Mail
            </button>

            <span class="bg-red-100 text-red-700 font-bold text-xs px-3 py-1.5 rounded-full uppercase tracking-wider border border-red-200">
                Campaña: {{ $cliente->cod_ban }}-{{ $cliente->grupo }}
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
                <span class="text-sm font-bold text-slate-800 truncate block">{{ $cliente->nro_cta }}</span>
            </div>
            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Estado</span>
                <span class="text-sm font-bold font-mono text-slate-800" title="ESTADO">{{ $cliente->nom_deu3 ?? '' }}</span>
            </div>
            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Carrera</span>
                <span class="text-sm font-bold font-mono text-slate-800" title="CARRERA">{{ $cliente->dir_fia ?? '' }}</span>
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
