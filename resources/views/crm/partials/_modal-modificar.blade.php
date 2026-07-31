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
                            <tbody id="tbodyGestionesEditar" class="divide-y divide-slate-100">
                                <!-- Las filas se cargan dinámicamente vía AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer con Controles de Paginación -->
                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div id="contenedorPaginacionGestionesEditar" class="flex items-center gap-1.5">
                        <!-- Botones de paginación generados dinámicamente -->
                    </div>
                    <button onclick="cerrarModalModificar()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition-colors">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
