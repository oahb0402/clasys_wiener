            <!-- PANEL DE REGISTRO DE GESTIÓN -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                <!-- ENCABEZADO DEL PANEL -->
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Registrar Nueva Gestión
                        </h3>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">CRM Cobranzas</span>
                </div>

                <!-- CONTENEDOR DE PROMESAS VIGENTES -->
                <div id="promesas-gestion"
                    class="{{ !empty($promesaActiva) ? '' : 'hidden' }} bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold rounded-lg p-3 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>
                            <strong>¡Atención!</strong> El cliente tiene una <span
                                class="uppercase underline font-bold">Promesa Activa</span>.
                        </span>
                    </div>

                    <span id="promesa-detalle-texto"
                        class="text-[11px] font-normal text-amber-700 bg-amber-100/80 px-2 py-0.5 rounded border border-amber-200">
                        @if (!empty($promesaActiva))
                            {{ $promesaActiva['fecha'] ?? '' }}
                            {{ isset($promesaActiva['monto']) ? '- S/ ' . $promesaActiva['monto'] : '' }}
                        @endif
                    </span>
                </div>
                <div class="p-6 space-y-6">
                    <!-- BANNER DE RESUMEN DE INTERACCIONES -->
                    <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-slate-100">
                        <span class="text-xs font-bold text-slate-500 mr-1">Historial rápido:</span>

                        <!-- Gestiones Positivas -->
                        <button type="button" onclick="abrirHistorial('gestiones_positivas')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition-colors">
                            <span>Gest. Positivas:</span>
                            <span
                                class="bg-emerald-200 text-emerald-900 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{ $historialRapido['positives'] }}</span>
                        </button>

                        <!-- Total Gestiones -->
                        <button type="button" onclick="abrirHistorial('total_gestiones')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                            <span>Total Gestiones:</span>
                            <span
                                class="bg-blue-200 text-blue-900 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{ $historialRapido['total'] }}</span>
                        </button>

                        <!-- IVR -->
                        <button type="button" onclick="abrirHistorial('ivr')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors">
                            <span>IVR:</span>
                            <span
                                class="bg-indigo-200 text-indigo-900 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{ $historialRapido['ivr'] }}</span>
                        </button>

                        <!-- Mensaje (SMS / WA) -->
                        <button type="button" onclick="abrirHistorial('sms')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-800 hover:bg-sky-200 transition-colors">
                            <span>Sms:</span>
                            <span
                                class="bg-sky-200 text-sky-900 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{ $historialRapido['sms'] }}</span>
                        </button>

                        <!-- Mail -->
                        <button type="button" onclick="abrirHistorial('mail')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-100 text-violet-800 hover:bg-violet-200 transition-colors">
                            <span>Mail:</span>
                            <span
                                class="bg-violet-200 text-violet-900 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{ $historialRapido['mail'] }}</span>
                        </button>

                        <!-- Abonos -->
                        <button type="button" onclick="abrirHistorial('abonos')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 hover:bg-rose-200 transition-colors">
                            <span>Abonos:</span>
                            <span
                                class="bg-rose-200 text-rose-900 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{ $historialRapido['abono'] }}</span>
                        </button>
                    </div>

                    <!-- CHIPS / BOTONES DE RESPUESTA RÁPIDA -->
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Comentarios
                            Rápidos</label>
                        <div class="flex flex-wrap gap-2">

                            <button type="button" onclick="setComentario('No Contestan')"
                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg border border-slate-200 transition-colors flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> No Contestan
                            </button>
                            <button type="button" onclick="setComentario('Contestan y Cuelgan')"
                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg border border-slate-200 transition-colors flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Contestan y Cuelgan
                            </button>
                            <button type="button" onclick="setComentario('Buzon de Voz')"
                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg border border-slate-200 transition-colors flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Buzon de Voz
                            </button>


                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <!-- FORMULARIO PRINCIPAL -->
                    <form id="form-gestion" action="#" method="POST" enctype="multipart/form-data" novalidate
                        class="space-y-6">
                        @csrf
                        <input type="hidden" name="cliente_id" value="{{ $cliente->cod_deu }}">
                        <input type="hidden" id="editar_item_id" name="item_id" value="">
                        <input type="hidden" id="control_grupo" name="control_grupo" value="">
                        <input type="hidden" name="usuario" value="{{ $paramsLlamada['uid'] }}">
                        <input type="hidden" name="telef_ges" value="{{ $paramsLlamada['telf'] }}">
                        <input type="hidden" name="con_cam" value="{{ $paramsLlamada['accion_predictivo'] }}">
                        <input type="hidden" name="comenta2" value="{{ $paramsLlamada['idllamada'] }}">
                        <input type="hidden" name="anexo" value="{{ $paramsLlamada['extension'] }}">
                        <input type="hidden" id="hora_apertura" name="hora_apertura"
                            value="{{ now()->format('H:i:s') }}">



                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- COLUMNA IZQUIERDA: CONFIGURACIÓN DE LA GESTIÓN -->
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-xs font-bold uppercase text-slate-600 mb-1">Gestión</label>
                                    <select id="select-tipcon" name="tipcon"
                                        class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                                        <option value="">-- SELECCIONE GESTION --</option>
                                        @foreach ($tipo_gestiones as $tipo_gestion)
                                            <option value="{{ $tipo_gestion->codigo }}">
                                                {{ $tipo_gestion->codigo }} - {{ $tipo_gestion->descri }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Control /
                                        Respuesta</label>
                                    <select id="select-control" name="control"
                                        data-promesas-x="{{ json_encode($codigosPromesaX) }}"
                                        data-confirmaciones-x="{{ json_encode($codigosConfirmacionX) }}"
                                        class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                                        <option value="">-- SELECCIONE RESPUESTA --</option>
                                        @foreach ($respuestas as $nombreGrupo => $listaRespuestas)
                                            <optgroup label="{{ $nombreGrupo }}"
                                                class="font-bold text-slate-900 bg-slate-100">
                                                @foreach ($listaRespuestas as $item)
                                                    <option value="{{ trim($item->codigo) }}"
                                                        data-promesa="{{ $item->promesa }}"
                                                        class="font-normal text-slate-700 bg-white">
                                                        {{ $item->codigo }} - {{ $item->descrip }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="seccion-promesa"
                                    class="hidden bg-emerald-50/60 border border-emerald-200 rounded-xl p-5 space-y-4 transition-all"
                                    data-disabled-inicial>
                                    <div class="flex items-center gap-2 border-b border-emerald-200/60 pb-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h4 class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Detalle
                                            del Compromiso / Promesa de Pago</h4>
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-3">
                                        <!-- FILA 1 -->
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Fecha
                                                Promesa</label>
                                            <input type="date" id="fecha_promesa_input" name="fecha_promesa"
                                                class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Monto
                                                Promesa</label>
                                            <input type="number" step="0.01" name="monto_promesa"
                                                placeholder="0.00"
                                                class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-900 text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Moneda</label>
                                            <select name="moneda_promesa"
                                                class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                                <option value="S">1 - Soles</option>
                                                <option value="D">2 - Dolares</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div id="seccion-confirmacion"
                                    class="hidden bg-emerald-50/60 border border-emerald-200 rounded-xl p-5 space-y-4 transition-all">

                                    <div class="flex items-center gap-2 border-b border-emerald-200/60 pb-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h4 class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Detalle
                                            de la Confirmacion</h4>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-3">
                                        <!-- FILA 1 -->
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Fecha
                                                Confirmacion</label>
                                            <input type="date" id="fecha_confirmacion_input" name="fecha_promesa"
                                                class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Monto
                                                Confirmacion</label>
                                            <input type="number" step="0.01" name="monto_promesa"
                                                placeholder="0.00"
                                                class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-900 text-xs font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Moneda</label>
                                            <select name="moneda_promesa"
                                                class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                                <option value="S">1 - Soles</option>
                                                <option value="D">2 - Dolares</option>
                                            </select>
                                        </div>

                                        <!-- FILA 2 -->
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Nombre
                                                Titular</label>
                                            <input type="text" name="nombre_titular"
                                                class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">DNI
                                                Titular</label>
                                            <input type="text" name="dni_titular"
                                                class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Datos
                                                Tarjeta</label>
                                            <input type="text" name="datos_tarjeta"
                                                class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        </div>

                                        <!-- FILA 3 -->
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Medio
                                                de Pago</label>
                                            <select name="medio_pago"
                                                class="w-full rounded-lg border border-emerald-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                                <option value="BCP">BCP</option>
                                                <option value="BBVA">BBVA</option>
                                            </select>
                                        </div>

                                        <!-- CAMPO COMPROBANTE CON COL-SPAN-2 (Ocupa 2 columnas) -->
                                        <div class="sm:col-span-2 lg:col-span-2">
                                            <label
                                                class="block text-[11px] font-bold uppercase text-emerald-800 mb-1">Comprobante
                                                de Pago</label>
                                            <input type="file" name="comprobante_confirmacion"
                                                id="comprobante_confirmacion" accept="image/*,.pdf"
                                                class="w-full text-xs text-slate-700 bg-white rounded-lg border border-emerald-300 cursor-pointer
                                                        file:mr-3 file:py-2 file:px-4 file:rounded-l-lg file:border-0
                                                        file:text-xs file:font-bold file:bg-emerald-600 file:text-white
                                                        hover:file:bg-emerald-700 focus:outline-none transition-all">
                                        </div>
                                    </div>
                                </div>

                                <!-- SELECT SUB RESPUESTA -->
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Sub
                                        Respuesta</label>
                                    <select id="select-subres" name="subres" disabled
                                        class="w-full rounded-lg border border-slate-300 p-2.5 bg-slate-100 text-slate-800 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">-- SELECCIONE SUB RESPUESTA --</option>
                                        @foreach ($sub_respuestas as $sub)
                                            <option value="{{ trim($sub->codigo) }}">
                                                {{ $sub->codigo }} - {{ $sub->descrip }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- COLUMNA DERECHA: COMENTARIOS Y CONTACTO -->
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-xs font-bold uppercase text-slate-600 mb-1">Contacto</label>
                                    <select id="select-tipgb" name="tipgb"
                                        class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                                        <option value="">-- SELECCIONE CONTACTO --</option>
                                        @foreach ($tipo_contactos as $tipo_contacto)
                                            <option value="{{ $tipo_contacto->codigo }}">
                                                {{ $tipo_contacto->codigo }} - {{ $tipo_contacto->descri }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Comentario /
                                        Observación</label>
                                    <textarea id="comentario" name="comentario" rows="4" placeholder="Escriba los detalles de la conversación..."
                                        class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all placeholder:text-slate-400"></textarea>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold uppercase text-slate-600 mb-1">Condición</label>
                                    <select name="condicion"
                                        class="w-full rounded-lg border border-slate-300 p-2.5 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                                        @foreach ($condiciones as $cond)
                                            <option value="{{ $cond->codigo }}"
                                                {{ trim($cliente->condicion) == trim($cond->codigo) ? 'selected' : '' }}>
                                                {{ $cond->codigo }} - {{ $cond->descrip }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- SECCIÓN AGENDAR LLAMADA -->
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                                    <!-- Toggle / Checkbox para activar -->
                                    <div class="flex items-center justify-between">
                                        <label for="check-agendar"
                                            class="flex items-center gap-2.5 cursor-pointer select-none">
                                            <input type="checkbox" id="check-agendar" name="agendar" value="1"
                                                class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 transition-all cursor-pointer">
                                            <span
                                                class="text-xs font-bold uppercase text-slate-700 tracking-wide">Agendar
                                                próxima llamada</span>
                                        </label>
                                        <span class="text-[11px] text-slate-500 font-medium">Recordatorio en
                                            agenda</span>
                                    </div>

                                    <!-- Contenedor desplegable (Oculto por defecto) -->
                                    <div id="seccion-agendar"
                                        class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-200/60">
                                        <div>
                                            <label for="fec_agenda"
                                                class="block text-[11px] font-bold uppercase text-slate-600 mb-1">
                                                Fecha Agendado
                                            </label>
                                            <input type="date" id="fec_agenda" name="fec_agenda" disabled
                                                class="w-full rounded-lg border border-slate-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                                        </div>

                                        <div>
                                            <label for="hor_agenda"
                                                class="block text-[11px] font-bold uppercase text-slate-600 mb-1">
                                                Hora Agendado
                                            </label>
                                            <input type="time" id="hor_agenda" name="hor_agenda" disabled
                                                class="w-full rounded-lg border border-slate-300 p-2 bg-white text-slate-800 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <!-- CONTENEDOR DE ERRORES -->
                        <div id="errores-gestion"
                            class="hidden bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-lg p-3 space-y-1">
                        </div>



                        <!-- BOTONES: modo "nueva gestión" -->
                        <div id="botones-nueva-gestion"
                            class="flex flex-col sm:flex-row justify-end items-center gap-3 border-t border-slate-100 pt-5">
                            <button type="submit" name="accion" value="multiple"
                                class="w-full sm:w-auto px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider rounded-lg transition-colors border border-slate-200">
                                Grabar Múltiple
                            </button>
                            <button type="submit" name="accion" value="cerrar"
                                class="w-full sm:w-auto px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs uppercase tracking-wider rounded-lg transition-colors shadow-sm">
                                Grabar y Cerrar
                            </button>
                            <button type="submit" name="accion" value="grabar"
                                class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-sm shadow-blue-200 transition-colors">
                                Grabar
                            </button>
                        </div>

                        <!-- BOTONES: modo "editar gestión" (oculto por defecto) -->
                        <div id="botones-editar-gestion"
                            class="hidden flex-col sm:flex-row justify-end items-center gap-3 border-t border-slate-100 pt-5">
                            <button type="button" onclick="cancelarEdicionGestion()"
                                class="w-full sm:w-auto px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider rounded-lg transition-colors border border-slate-200">
                                Cancelar
                            </button>
                            <button type="submit" name="accion" value="actualizar"
                                class="w-full sm:w-auto px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs uppercase tracking-wider rounded-lg shadow-sm shadow-amber-200 transition-colors">
                                Guardar Cambios
                            </button>
                        </div>

                    </form>
                </div>
            </div>
