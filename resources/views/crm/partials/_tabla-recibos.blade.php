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
