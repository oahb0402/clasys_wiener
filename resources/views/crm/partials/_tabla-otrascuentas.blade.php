<!-- SECCIÓN: OTRAS CUENTAS EN G110 CON EL MISMO NRO_DOC -->
@if(isset($otrasCuentas) && $otrasCuentas->count() > 0)
    <div class="mt-4 bg-white rounded-lg border border-blue-200 shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-xs font-bold uppercase text-blue-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Otras cuentas registradas para el Documento: <span class="underline">{{ $cliente->nro_doc }}</span>
            </h3>
            <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-full">
                {{ $otrasCuentas->count() }} @choice('registro|registros', $otrasCuentas->count())
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-bold">
                        <th class="p-2">Cód. Deudor</th>
                        <th class="p-2">Grupo</th>
                        <th class="p-2">Condición</th>
                        <th class="p-2">N° Cuenta</th>
                        <th class="p-2">Periodo</th>
                        <th class="p-2">Fec_Ini</th>
                        <th class="p-2">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($otrasCuentas as $cuenta)
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="p-2 font-bold text-slate-800">
                                {{ $cuenta->cod_deu }}
                            </td>
                            <td class="p-2 text-slate-600">
                                {{ $cuenta->grupo ?? '-' }}
                            </td>
                            <td class="p-2 text-slate-600">
                                <span class="px-2 py-0.5 bg-slate-200 text-slate-700 font-semibold rounded text-[10px]">
                                    {{ $cuenta->condicion ?? 'SIN COND.' }}
                                </span>
                            </td>

                            <td class="p-2 font-medium text-slate-700">
                                {{ $cuenta->nro_cta ?? '-' }}
                            </td>
                            <td class="p-2 font-medium text-slate-600">
                                {{ $cuenta->ult_mov ?? '-' }}
                            </td>
                            <td class="p-2 font-medium text-slate-600">
                                {{ $cuenta->fec_ini ?? '-' }}
                            </td>

                            <td class="p-2 text-center">
                                <a href="{{ route('crm.principal', array_merge(['id' => $cuenta->cod_deu], array_filter($paramsLlamada))) }}"
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
