            <!-- 1. BARRA SUPERIOR DE ESTADO / DIALER -->
            <div class="bg-slate-900 text-white rounded-xl p-4 shadow-md flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span class="text-sm font-medium text-slate-300">Marcando a:</span>
                    <span class="text-xl font-bold tracking-wide text-emerald-400">{{$paramsLlamada['telf']}}</span>
                </div>



                <div class="flex items-center gap-6 text-sm">
                    <div class="bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                        <span class="text-slate-400">Tipo:</span> <span class="font-semibold text-amber-400">{{$paramsLlamada['accionPredictivo']}}</span>
                    </div>
                    <div class="bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                        <span class="text-slate-400">Cierre de campaña:</span>
                        <span class="font-semibold text-white">{{ $diasParaCierre }} días</span>
                        <span class="text-xs text-slate-400">(2026-12-31)</span>
                    </div>
                    <div class="bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                        <span class="text-slate-400"</span> <span class="font-semibold text-amber-100">{{$paramsLlamada['uid']}}</span>
                    </div>
                </div>
            </div>
