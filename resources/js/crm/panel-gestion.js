// Mapeo de cada botón de "Comentarios Rápidos" a los valores que debe
// autocompletar en el formulario. La clave debe ser EXACTA al texto que
// se le pasa a setComentario('...') desde el onclick del botón en el HTML.
const COMENTARIOS_RAPIDOS = {
    'No Contestan': { gestion: 'TM', contacto: 'C', control: '803', subRespuesta: '12' },
    'Contestan y Cuelgan': { gestion: 'TM', contacto: 'C', control: '802', subRespuesta: '12' },
    'Buzon de Voz': { gestion: 'TM', contacto: 'C', control: '901', subRespuesta: '12' },
};

function aplicarValorSelect(id, valor) {
    const el = document.getElementById(id);
    if (el) el.value = valor;
}

function dispararEventoChange(id) {
    const el = document.getElementById(id);
    if (el) el.dispatchEvent(new Event('change'));
}

export function setComentario(texto) {
    document.getElementById('comentario').value = texto;

    const valores = COMENTARIOS_RAPIDOS[texto];
    if (!valores) return; // el botón no tiene mapeo de autocompletado, solo llena el comentario

    aplicarValorSelect('select-tipcon', valores.gestion);
    aplicarValorSelect('select-tipgb', valores.contacto);
    aplicarValorSelect('select-control', valores.control);

    // Disparamos "change" en Control/Respuesta para que se recalculen
    // automáticamente las secciones de Promesa/Confirmación y las
    // opciones de Sub Respuesta (initPromesaConfirmacionToggle /
    // initSubrespuestaFiltro, ambas más abajo en este mismo archivo).
    dispararEventoChange('select-control');

    // La reconstrucción de las opciones de Sub Respuesta ocurre de forma
    // síncrona dentro del listener de "change" de arriba, así que para
    // cuando llegamos aquí el <select> ya tiene sus opciones nuevas.
    aplicarValorSelect('select-subres', valores.subRespuesta);
}

function initPromesaConfirmacionToggle() {
    const selectRespuesta = document.getElementById('select-control');
    const seccionPromesa = document.getElementById('seccion-promesa');
    const seccionConfirmacion = document.getElementById('seccion-confirmacion');

    if (!selectRespuesta || !seccionPromesa || !seccionConfirmacion) return;

    const codigosPromesa = JSON.parse(selectRespuesta.getAttribute('data-promesas-x') || '[]');
    const codigosConfirmacion = JSON.parse(selectRespuesta.getAttribute('data-confirmaciones-x') || '[]');

    function ocultarYLimpiar(seccion) {
        seccion.classList.add('hidden');
        seccion.querySelectorAll('input, select').forEach(el => {
            el.required = false;
            el.value = '';
            el.disabled = true; // <-- clave: excluye estos campos del FormData al submit
        });
    }

    function mostrarYRequerir(seccion) {
        seccion.classList.remove('hidden');
        seccion.querySelectorAll('input, select').forEach(el => {
            el.required = true;
            el.disabled = false; // <-- vuelve a habilitar para que sí se envíe
        });
    }

    selectRespuesta.addEventListener('change', function () {
        const codigoSeleccionado = String(this.value).trim();

        if (codigoSeleccionado && codigosPromesa.includes(codigoSeleccionado)) {
            mostrarYRequerir(seccionPromesa);
            ocultarYLimpiar(seccionConfirmacion);
            configurarLimiteFechas();
        } else if (codigoSeleccionado && codigosConfirmacion.includes(codigoSeleccionado)) {
            mostrarYRequerir(seccionConfirmacion);
            ocultarYLimpiar(seccionPromesa);
            configurarLimiteFechas();
        } else {
            ocultarYLimpiar(seccionPromesa);
            ocultarYLimpiar(seccionConfirmacion);
            configurarLimiteFechas();
        }
    });
    // Estado inicial: ambas ocultas y deshabilitadas
    ocultarYLimpiar(seccionPromesa);
    ocultarYLimpiar(seccionConfirmacion);
    configurarLimiteFechas();
}

function initSubrespuestaFiltro() {
    const selectRespuesta = document.getElementById('select-control');
    const selectSubrespuesta = document.getElementById('select-subres');

    if (!selectRespuesta || !selectSubrespuesta) return;

    // Guardamos copia de todas las opciones excepto la vacía por defecto
    const todasLasOpciones = Array.from(selectSubrespuesta.querySelectorAll('option'))
        .filter(opt => opt.value !== '')
        .map(opt => opt.cloneNode(true));

    selectRespuesta.addEventListener('change', function () {
        const valorSeleccionado = String(this.value).trim();
        const primerCaracter = valorSeleccionado.charAt(0);

        // Resetear select de sub-respuestas
        selectSubrespuesta.innerHTML = '<option value="">-- SELECCIONE SUB RESPUESTA --</option>';

        if (!valorSeleccionado) {
            selectSubrespuesta.disabled = true;
            selectSubrespuesta.classList.add('bg-slate-100');
            return;
        }

        // Regla de negocio: los códigos que empiezan con 2, 3 o 4 muestran
        // todas las sub-respuestas EXCEPTO la "12"; cualquier otro código
        // solo permite la sub-respuesta "12" y la autoselecciona.
        const esGrupoEspecial = ['2', '3', '4'].includes(primerCaracter);

        const opcionesFiltradas = todasLasOpciones.filter(option => {
            const codigoSub = String(option.value).trim();
            return esGrupoEspecial ? codigoSub !== '12' : codigoSub === '12';
        });

        if (opcionesFiltradas.length > 0) {
            opcionesFiltradas.forEach(opt => {
                selectSubrespuesta.appendChild(opt.cloneNode(true));
            });
            selectSubrespuesta.disabled = false;
            selectSubrespuesta.classList.remove('bg-slate-100');

            if (!esGrupoEspecial) {
                selectSubrespuesta.value = '12';
            }
        } else {
            selectSubrespuesta.disabled = true;
            selectSubrespuesta.classList.add('bg-slate-100');
        }
    });
}

function initControlGrupoHidden() {
    const selectControl = document.getElementById('select-control');
    const inputGrupo = document.getElementById('control_grupo');
    if (!selectControl || !inputGrupo) return;

    function actualizarGrupo() {
        const opcionSeleccionada = selectControl.options[selectControl.selectedIndex];
        const optgroup = opcionSeleccionada ? opcionSeleccionada.closest('optgroup') : null;
        inputGrupo.value = optgroup ? optgroup.label : '';
    }

    selectControl.addEventListener('change', actualizarGrupo);
}

function initAgendadoToggle() {
    const checkAgendar = document.getElementById('check-agendar');
    const seccionAgendar = document.getElementById('seccion-agendar');
    const inputFecha = document.getElementById('fec_agenda');
    const inputHora = document.getElementById('hor_agenda');

    if (!checkAgendar || !seccionAgendar || !inputFecha || !inputHora) return;

    checkAgendar.addEventListener('change', function () {
        if (this.checked) {
            seccionAgendar.classList.remove('hidden');
            inputFecha.disabled = false;
            inputHora.disabled = false;
            inputFecha.required = true;
            inputHora.required = true;

            const ahora = new Date();

            // 1. Asigna la fecha actual por defecto en hora local si está vacía
            if (!inputFecha.value) {
                const year = ahora.getFullYear();
                const month = String(ahora.getMonth() + 1).padStart(2, '0');
                const day = String(ahora.getDate()).padStart(2, '0');
                inputFecha.value = `${year}-${month}-${day}`;
            }

            // 2. Asigna la HORA ACTUAL + 1 si está vacía
            if (!inputHora.value) {
                ahora.setHours(ahora.getHours() + 1); // Suma 1 hora (maneja automáticamente el cambio de día/medianoche)

                const horas = String(ahora.getHours()).padStart(2, '0');
                const minutos = String(ahora.getMinutes()).padStart(2, '0');

                inputHora.value = `${horas}:${minutos}`;
            }

        } else {
            seccionAgendar.classList.add('hidden');
            inputFecha.disabled = true;
            inputHora.disabled = true;
            inputFecha.required = false;
            inputHora.required = false;
            inputFecha.value = '';
            inputHora.value = '';
        }
    });

    // Estado inicial: oculto y deshabilitado
    seccionAgendar.classList.add('hidden');
    inputFecha.disabled = true;
    inputHora.disabled = true;
    inputFecha.required = false;
    inputHora.required = false;
}
export function initPanelGestion() {
    initPromesaConfirmacionToggle();
    initSubrespuestaFiltro();
    initControlGrupoHidden();
    initAgendadoToggle();
}


export function resetSeccionAgendar() {
    const checkAgendar = document.getElementById('check-agendar');
    const seccionAgendar = document.getElementById('seccion-agendar');
    const inputFecha = document.getElementById('fec_agenda');
    const inputHora = document.getElementById('hor_agenda');

    if (checkAgendar) checkAgendar.checked = false;

    if (seccionAgendar) seccionAgendar.classList.add('hidden');

    if (inputFecha) {
        inputFecha.value = '';
        inputFecha.disabled = true;
        inputFecha.required = false;
    }

    if (inputHora) {
        inputHora.value = '';
        inputHora.disabled = true;
        inputHora.required = false;
    }
}


export function actualizarAlertaPromesaActiva(promesa) {
    const contenedor = document.getElementById('promesas-gestion');
    const detalleTexto = document.getElementById('promesa-detalle-texto');

    if (!contenedor) return;

    if (promesa && (promesa.existe || typeof promesa === 'object')) {
        // Remueve la clase hidden para hacer visible la alerta
        contenedor.classList.remove('hidden');

        if (detalleTexto) {
            const fecha = promesa.fecha ?? promesa.fecha_promesa ?? '';
            const rawMonto = promesa.monto ?? promesa.monto_promesa;

            let montoFormateado = '';
            if (rawMonto !== null && rawMonto !== undefined && rawMonto !== '') {
                const num = Number(String(rawMonto).replace(/,/g, ''));
                montoFormateado = !isNaN(num)
                    ? num.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                    : rawMonto;
            }

            detalleTexto.textContent = `${fecha} ${montoFormateado ? '- S/ ' + montoFormateado : ''}`.trim();
        }
    } else {
        // Oculta el contenedor si no hay promesa activa
        contenedor.classList.add('hidden');
        if (detalleTexto) detalleTexto.textContent = '';
    }
}

export function actualizarAlertaConfirmacionActiva(confirmacion) {
    const contenedor = document.getElementById('confirmaciones-gestion');
    const detalleTexto = document.getElementById('confirmacion-detalle-texto');

    if (!contenedor) return;

    if (confirmacion && (confirmacion.existe || typeof confirmacion === 'object')) {
        contenedor.classList.remove('hidden');

        if (detalleTexto) {
            const fecha = confirmacion.fecha_confirmacion ?? confirmacion.fecha ?? '';
            const rawMonto = confirmacion.monto_confirmacion ?? confirmacion.monto;
            const monto = rawMonto ? `S/ ${rawMonto}` : '';

            detalleTexto.textContent = `${fecha} ${monto ? '- ' + monto : ''}`.trim();
        }
    } else {
        contenedor.classList.add('hidden');
        if (detalleTexto) detalleTexto.textContent = '';
    }
}


export function configurarLimiteFechas() {
    const inputPromesa = document.getElementById('fecha_promesa_input');
    const inputConfirmacion = document.getElementById('fecha_confirmacion_input');

    const formatearFecha = (fecha) => {
        const year = fecha.getFullYear();
        const month = String(fecha.getMonth() + 1).padStart(2, '0');
        const day = String(fecha.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    const hoy = new Date();
    const fechaHoyStr = formatearFecha(hoy);

    // 1. RESTRICCIÓN Y VALOR PROMESA
    if (inputPromesa) {
        const manana = new Date();
        manana.setDate(hoy.getDate() + 1);

        inputPromesa.min = fechaHoyStr;
        inputPromesa.max = formatearFecha(manana);

        // Si está vacío, le asignamos HOY inmediatamente
        if (!inputPromesa.value) {
            inputPromesa.value = fechaHoyStr;
        }
    }

    // 2. RESTRICCIÓN Y VALOR CONFIRMACIÓN
    if (inputConfirmacion) {
        const primerDiaMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);

        inputConfirmacion.min = formatearFecha(primerDiaMes);
        inputConfirmacion.max = fechaHoyStr;

        // Si está vacío, le asignamos HOY inmediatamente
        if (!inputConfirmacion.value) {
            inputConfirmacion.value = fechaHoyStr;
        }
    }
}
