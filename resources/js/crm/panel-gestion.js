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
    document.getElementById('comentario').value =  texto;

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
        } else if (codigoSeleccionado && codigosConfirmacion.includes(codigoSeleccionado)) {
            mostrarYRequerir(seccionConfirmacion);
            ocultarYLimpiar(seccionPromesa);
        } else {
            ocultarYLimpiar(seccionPromesa);
            ocultarYLimpiar(seccionConfirmacion);
        }
    });
    // Estado inicial: ambas ocultas y deshabilitadas
    ocultarYLimpiar(seccionPromesa);
    ocultarYLimpiar(seccionConfirmacion);
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

export function initPanelGestion() {
    initPromesaConfirmacionToggle();
    initSubrespuestaFiltro();
    initControlGrupoHidden();
}
