            <!-- TABLA DE DETALLE DE RECIBOS / CUOTAS -->
            @if(isset($otrasCuentas) && $otrasCuentas->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- CABECERA DE LA TABLA -->
                <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex flex-wrap justify-between items-center gap-2">
                    <h3 class="text-xs font-bold uppercase text-blue-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Otras cuentas registradas para el Documento: <span class="underline">{{ $cliente->nro_doc }}</span>
            </h3>
                    <div class="flex items-center gap-2">
                         <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-full">
                {{ $otrasCuentas->count() }} @choice('registro|registros', $otrasCuentas->count())
            </span>
                    </div>
                </div>

                <!-- CUERPO / TABLA (AQUÍ VAN LAS CLASES) -->
                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="w-full text-left text-xs">
                        <!-- Agregamos sticky top-0 al thead para que los títulos de la tabla no se pierdan al hacer scroll -->
                        <thead class="bg-slate-100 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider sticky top-0">
                            <tr>
                                 <th class="py-3 px-3 text-center"></th>
                                <th class="py-3 px-3">Cód. Deudor</th>
                                <th class="py-3 px-3">Grupo</th>
                                <th class="py-3 px-3 text-center">Condición</th>
                                <th class="py-3 px-3">N° Cuenta</th>
                                <th class="py-3 px-3">Periodo</th>
                                <th class="py-3 px-3 text-center">Fec_Ini</th>
                                <th class="py-3 px-3 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                              @foreach($otrasCuentas as $cuenta)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-2.5 px-3 text-center"></td>
                                <td class="py-2.5 px-3 font-mono text-slate-800 font-semibold"> {{ $cuenta->cod_deu }}</td>
                                <td class="py-2.5 px-3 font-mono text-slate-600">{{ $cuenta->grupo ?? '-' }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-slate-700"> {{ $cuenta->condicion ?? 'SIN COND.' }}</td>
                                <td class="py-2.5 px-3 font-mono text-slate-800 font-semibold"> {{ $cuenta->nro_cta ?? '-' }}</td>
                                <td class="py-2.5 px-3 text-slate-700 font-normal"> {{ $cuenta->ult_mov ?? '-' }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-600 font-mono">{{ $cuenta->fec_ini ?? '-' }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    {{-- <a href="{{ route('crm.principal', array_merge(['cod_deu' => $cuenta->cod_deu], array_filter($paramsLlamada))) }}"
                                   class="inline-flex items-center gap-1 text-[11px] bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded transition-colors font-semibold"> --}}
                                   <a href="{{ url('transito.php') }}?{{ http_build_query(array_merge(['cod_deu' => $cuenta->cod_deu], array_filter($paramsLlamada))) }}"
                                    class="inline-flex items-center gap-1 text-[11px] bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded transition-colors font-semibold">
                                    Ver Ficha
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
