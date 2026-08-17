<!-- Modal Solicitud de Envío de Correo -->
<div id="modalSolicitudCorreo"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden transition-opacity">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden transform transition-all p-6">


        <form id="formSolicitudCorreo" action="{{ route('envMails.store') }}" onsubmit="guardarSolicitudCorreo(event)"
            class="space-y-5">
            @csrf
            <!-- Campos Ocultos del Cliente y Sesión -->
            <input type="hidden" id="sol_cod_ban" name="cod_ban" value="{{ $cliente->cod_ban }}">
            <input type="hidden" id="sol_cod_deu" name="cod_deu" value="{{ $cliente->cod_deu ?? '' }}">

            <!-- Fila 1: Usuario y Fecha Solicitud -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Usuario:</label>
                    <input type="text" id="sol_usuario" name="usuario" value="{{ $paramsLlamada['uid'] ?? 'ADs' }}"
                        readonly
                        class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 font-medium focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fecha Solicitud</label>
                    <input type="date" id="sol_fec_solicitud" name="fec_solicitud" value="{{ date('Y-m-d') }}"
                        readonly
                        class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 font-medium focus:outline-none">
                </div>
            </div>

            <!-- Fila 2: Porcentaje, Monto de Campaña y Fecha de Pago -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="sol_porcentaje"
                        class="block text-xs font-bold text-gray-700 uppercase mb-1">Porcentaje:</label>
                    <select id="sol_porcentaje" name="porcentaje" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none">
                        @if (!is_null($porcentajeEnvMail))
                            <option value="{{ $porcentajeEnvMail }}" selected>
                                {{ $porcentajeEnvMail }}%
                            </option>
                        @else
                            <option value="0" selected>-- SIN PORCENTAJE --</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label for="sol_monto_campania" class="block text-xs font-bold text-gray-700 uppercase mb-1">Monto
                        de Campaña:</label>
                    <input type="number" step="0.01" id="sol_monto_campania" name="monto_campania"
                        placeholder="Ej: 123.45" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label for="sol_fec_pago" class="block text-xs font-bold text-gray-700 uppercase mb-1">Fecha de
                        pago:</label>
                    <input type="date" id="sol_fec_pago" name="fec_pago" value="{{ date('Y-m-d') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none">
                </div>
            </div>

            <hr class="border-gray-200 my-2">

            <!-- Botones de Acción (Cerrar / Enviar) -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="cerrarModalSolicitudCorreo()"
                    class="px-5 py-2 text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                    Cerrar
                </button>
                <button type="submit" id="btnEnviarSolicitud"
                    class="px-6 py-2 bg-emerald-600 text-white font-semibold rounded-lg shadow-sm hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-400 transition-all">
                    Enviar
                </button>
            </div>
        </form>

    </div>
</div>
