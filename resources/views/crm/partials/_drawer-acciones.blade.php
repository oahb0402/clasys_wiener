    <!-- PANEL LATERAL DESLIZANTE (SLIDE-OVER) -->
    <div id="drawer-menu" class="fixed inset-0 z-50 overflow-hidden hidden" aria-labelledby="slide-over-title"
        role="dialog" aria-modal="true">
        <!-- Fondo oscuro con Blur -->
        <div onclick="cerrarDrawer()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div
                class="w-screen max-w-md bg-white shadow-2xl border-l border-slate-200 flex flex-col justify-between animate-in slide-in-from-right duration-200">

                <!-- Encabezado y Contenido -->
                <div>
                    <!-- Header del Drawer -->
                    <div class="flex items-center justify-between p-5 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div>
                                <h3 id="drawer-titulo" class="text-sm font-bold text-slate-800 uppercase tracking-wide">
                                    Acciones Rápidas</h3>
                                <p id="drawer-subtitulo" class="text-[11px] text-slate-500">Código Cliente: <span
                                        class="font-bold text-slate-700">{{ $cliente->cod_deu ?? '' }}</span></p>
                            </div>
                        </div>
                        <button onclick="cerrarDrawer()"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Tabs de Navegación (SOLO 3 OPCIONES) -->
                    <div class="flex border-b border-slate-100 bg-slate-50/30 px-3 pt-2 gap-1 overflow-x-auto">
                        <button onclick="cambiarTabDrawer('numeros')" id="tab-numeros"
                            class="tab-btn active-tab px-3 py-2 text-xs font-bold rounded-t-lg transition-all">
                            + Números
                        </button>
                        <button onclick="cambiarTabDrawer('correos')" id="tab-correos"
                            class="tab-btn inactive-tab px-3 py-2 text-xs font-bold rounded-t-lg transition-all">
                            + Correos
                        </button>
                        <button onclick="cambiarTabDrawer('menu')" id="tab-menu"
                            class="tab-btn inactive-tab px-3 py-2 text-xs font-bold rounded-t-lg transition-all">
                            Más Opciones
                        </button>
                    </div>

                    <div id="content-numeros">
                        <form id="formGuardarTelefono" action="{{ route('telefonos.store') }}"
                            onsubmit="guardarTelefono(event)" class="p-6 space-y-4">
                            @csrf

                            <input type="hidden" id="input_tel_cod_ban" name="cod_ban" value="{{ $cliente->cod_ban }}">
                            <input type="hidden" id="input_tel_cod_deu" name="cod_deu"
                                value="{{ $cliente->cod_deu ?? '' }}">
                            <input type="hidden" id="input_tel_usuario" name="usuario" value="{{ $paramsLlamada['uid'] }}">

                            <div>
                                <label for="nuevo_tipo_tel"
                                    class="block text-xs font-bold uppercase text-slate-600 mb-1">
                                    Tipo de Teléfono
                                </label>
                                <select id="nuevo_tipo_tel" name="tipo" required
                                    class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="Celular" selected>Celular</option>
                                    <option value="Oficina">Oficina</option>
                                    <option value="Casa">Casa</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <div>
                                <label for="nuevo_numero" class="block text-xs font-bold uppercase text-slate-600 mb-1">
                                    Número
                                </label>
                                <input type="text" id="nuevo_numero" name="numero" placeholder="Ej: 987654321"
                                    required inputmode="numeric" pattern="[0-9]*"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-xs font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <button type="submit"
                                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Guardar Teléfono</span>
                            </button>
                        </form>
                    </div>

                    <div id="content-correos" class="p-6 space-y-4 hidden">
                        <form id="formGuardarCorreo" action="{{ route('correos.store') }}"
                            onsubmit="guardarCorreo(event)" class="space-y-4">
                            @csrf

                            <input type="hidden" id="input_correo_cod_ban" name="cod_ban"
                                value="{{ $cliente->cod_ban }}">
                            <input type="hidden" id="input_correo_cod_deu" name="cod_deu"
                                value="{{ $cliente->cod_deu ?? '' }}">
                            <input type="hidden" id="input_correo_usuario" name="usuario"
                                value="{{ $paramsLlamada['uid'] }}">

                            <div>
                                <label for="nuevo_tipo_correo"
                                    class="block text-xs font-bold uppercase text-slate-600 mb-1">
                                    Tipo de Correo
                                </label>
                                <select id="nuevo_tipo_correo" name="tipo" required
                                    class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="Personal" selected>Personal</option>
                                    <option value="Secundario">Secundario</option>
                                    <option value="Trabajo">Trabajo</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <div>
                                <label for="nuevo_correo"
                                    class="block text-xs font-bold uppercase text-slate-600 mb-1">
                                    Correo Electrónico
                                </label>
                                <input type="email" id="nuevo_correo" name="correo"
                                    placeholder="cliente@correo.com" required
                                    class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <button type="submit"
                                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Guardar Correo</span>
                            </button>
                        </form>
                    </div>

                    <!-- CONTENIDO 3: MENÚ DE MÁS OPCIONES -->
                    <div id="content-menu" class="p-6 hidden">
                        <!-- Vista de Lista de Botones -->
                        <div id="sub-menu-lista" class="grid grid-cols-1 gap-2">
                            <button type="button" onclick="abrirModalModificar()"
                                class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50/50 text-slate-700 hover:text-blue-700 text-xs font-bold transition-all group">
                                <span>Modificar Gestión</span>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            {{-- <button type="button" onclick="mostrarFormularioCondicion()"
                                class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50/50 text-slate-700 hover:text-blue-700 text-xs font-bold transition-all group">
                                <span>Cambiar Condición [5]</span>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button> --}}
                        </div>

                        <!-- Formulario Interno para Condición (Oculto por defecto) -->
                        {{-- <div id="sub-menu-condicion" class="space-y-4 hidden">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <span class="text-xs font-bold text-slate-700 uppercase">Cambiar Condición</span>
                                <button type="button" onclick="ocultarFormularioCondicion()"
                                    class="text-[11px] font-bold text-blue-600 hover:underline">
                                    ← Volver
                                </button>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Nueva
                                    Condición</label>
                                <select id="condicion_drawer"
                                    class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-xs font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="GN">GN - GESTION NEGATIVA</option>
                                    <option value="GP">GP - GESTION POSITIVA</option>
                                    <option value="PP">PP - PROMESA DE PAGO</option>
                                    <option value="IL">IL - ILOCALIZABLE</option>
                                </select>
                            </div>

                            <button type="button" onclick="guardarCondicion()"
                                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow-md transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Guardar Cambio
                            </button>
                        </div> --}}
                    </div>

                </div>

                <!-- Footer del Drawer -->
                <div
                    class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                    <span>Atajo activo: <kbd
                            class="px-1.5 py-0.5 bg-white border border-slate-200 rounded text-slate-600 font-mono font-bold">Alt
                            + N</kbd></span>
                    <button onclick="cerrarDrawer()" class="text-slate-600 font-bold hover:underline">Cerrar</button>
                </div>

            </div>
        </div>
    </div>
