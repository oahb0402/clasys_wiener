<div id="modalHistorial" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all">

        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 id="tituloModalHistorial" class="text-base font-bold text-gray-800 tracking-wide uppercase">
                        HISTORIAL
                    </h3>
                    <p class="text-xs text-gray-500">Historial de interacciones del cliente</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalHistorial()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto flex-1">
            <div class="border border-gray-100 rounded-xl overflow-x-auto shadow-sm">
                <table class="w-full min-w-full text-left text-xs">
                    <thead class="bg-gray-50/80 text-gray-500 font-semibold uppercase tracking-wider border-b border-gray-100 whitespace-nowrap">
                        <tr>
                            {{-- <th class="px-4 py-3 text-center">#</th> --}}
                            <th class="px-4 py-3">ITEM</th>
                            <th class="px-4 py-3">FECHA / HORA</th>
                            <th class="px-4 py-3">TIPO</th>
                            <th class="px-4 py-3">TELÉFONO</th>
                            <th class="px-4 py-3">ESTADO</th>
                            <th class="px-4 py-3">FECHA PDP</th>
                            <th class="px-4 py-3 text-right">MONTO PDP</th>
                            <th class="px-4 py-3 min-w-[250px]">COMENTARIO / DETALLE</th>
                            <th class="px-4 py-3">GESTOR</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyHistorial" class="divide-y divide-gray-100 bg-white">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500 flex-shrink-0">
            <div id="infoPaginacionHistorial">
                Cargando registros...
            </div>

            <div class="flex items-center gap-3">
                <div id="contenedorPaginacionHistorial" class="flex items-center gap-1">
                    </div>

                <button type="button" onclick="cerrarModalHistorial()" class="px-4 py-2 bg-slate-800 text-white font-medium rounded-lg hover:bg-slate-700 transition-colors">
                    Cerrar
                </button>
            </div>
        </div>

    </div>
</div>
