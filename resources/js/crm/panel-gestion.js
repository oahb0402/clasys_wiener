export function setComentario(texto) {
    document.getElementById('comentario').value = 'El cliente se encuentra en estado: ' + texto;
}

function initPromesaConfirmacionToggle() {
    const selectRespuesta = document.getElementById('select-control');
    const seccionPromesa = document.getElementById('seccion-promesa');
    const seccionConfirmacion = document.getElementById('seccion-confirmacion');

    if (!selectRespuesta || !seccionPromesa || !seccionConfirmacion) return;

    // Cargar listas de códigos desde los atributos data-
    const codigosPromesa = JSON.parse(selectRespuesta.getAttribute('data-promesas-x') || '[]');
    const codigosConfirmacion = JSON.parse(selectRespuesta.getAttribute('data-confirmaciones-x') || '[]');

    function ocultarYLimpiar(seccion) {
        seccion.classList.add('hidden');
        seccion.querySelectorAll('input, select').forEach(el => {
            el.required = false;
            el.value = '';
        });
    }

    function mostrarYRequerir(seccion) {
        seccion.classList.remove('hidden');
        seccion.querySelectorAll('input, select').forEach(el => {
            el.required = true;
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

export function initPanelGestion() {
    initPromesaConfirmacionToggle();
    initSubrespuestaFiltro();
}
