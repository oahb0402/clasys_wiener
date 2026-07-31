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
