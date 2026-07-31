        <!-- MODAL DE HISTORIAL DETALLADO -->
        <div id="modal-historial" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-all">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-4xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">

                <!-- Header del Modal -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 id="modal-titulo" class="text-sm font-bold text-slate-800 uppercase tracking-wide">Detalle de Gestiones</h3>
                            <p class="text-[11px] text-slate-500">Historial de interacciones del cliente</p>
                        </div>
                    </div>
                    <button onclick="cerrarHistorial()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Tabla de Datos -->
                <div class="p-6">
                    <div class="min-h-[260px] max-h-[320px] overflow-y-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] tracking-wider sticky top-0 border-b border-slate-200">
                                <tr>
                                    <th class="p-3 w-12 text-center">#</th>
                                    <th class="p-3 whitespace-nowrap">Fecha / Hora</th>
                                    <th class="p-3">Teléfono</th>
                                    <th class="p-3">Control / Tipo</th>
                                    <th class="p-3">Comentario / Detalle</th>
                                </tr>
                            </thead>
                            <tbody id="modal-tabla-body" class="divide-y divide-slate-100">
                                <!-- Las filas dinámicas con paginación se renderizan aquí -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer con Controles de Paginación -->
                <div class="px-6 py-3.5 bg-slate-50/80 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <!-- Texto informativo de la página -->
                    <div class="text-xs text-slate-500">
                        Mostrando <span id="pag-inicio" class="font-bold text-slate-700">0</span> a
                        <span id="pag-fin" class="font-bold text-slate-700">0</span> de
                        <span id="pag-total" class="font-bold text-slate-700">0</span> registros
                    </div>

                    <!-- Botones de Navegación -->
                    <div class="flex items-center gap-1.5">
                        <button id="btn-pag-prev" onclick="cambiarPagina(-1)" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                            Anterior
                        </button>

                        <!-- Números de Página Dinámicos -->
                        <div id="pag-numeros" class="flex items-center gap-1"></div>

                        <button id="btn-pag-next" onclick="cambiarPagina(1)" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                            Siguiente
                        </button>

                        <button onclick="cerrarHistorial()" class="ml-3 px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>

            </div>
        </div>
