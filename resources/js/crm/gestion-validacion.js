const CAMPOS_SIEMPRE_REQUERIDOS = [
    { selector: '#select-tipcon', etiqueta: 'Gestión' },
    { selector: '#select-tipgb', etiqueta: 'Contacto' },
    { selector: '#select-control', etiqueta: 'Respuesta' },
    { selector: '#select-subres', etiqueta: 'Sub Respuesta' },
    { selector: '#comentario', etiqueta: 'Comentario' },
];

function marcarInvalido(el) {
    el.classList.add('border-red-500', 'ring-2', 'ring-red-200');
    el.classList.remove('border-slate-300');
}

function marcarValido(el) {
    el.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
    el.classList.add('border-slate-300');
}

function mostrarResumenErrores(mensajes) {
    const contenedor = document.getElementById('errores-gestion');
    if (!contenedor) return;

    if (mensajes.length === 0) {
        contenedor.classList.add('hidden');
        contenedor.innerHTML = '';
        return;
    }

    contenedor.classList.remove('hidden');
    contenedor.innerHTML = '<p>Completa los siguientes campos:</p><ul class="list-disc list-inside">'
        + mensajes.map(m => `<li>${m}</li>`).join('')
        + '</ul>';
}

export function validarFormularioGestion(form) {
    const errores = [];
    let primerCampoInvalido = null;

    // 1. Campos siempre obligatorios
    CAMPOS_SIEMPRE_REQUERIDOS.forEach(({ selector, etiqueta }) => {
        const el = form.querySelector(selector);
        if (!el) return;

        const vacio = !el.value || !el.value.trim();
        if (vacio) {
            marcarInvalido(el);
            errores.push(etiqueta);
            if (!primerCampoInvalido) primerCampoInvalido = el;
        } else {
            marcarValido(el);
        }
    });

    // 2. Campos condicionalmente obligatorios (seccion-promesa / seccion-confirmacion)
    // Solo se validan los que están habilitados (la sección visible), gracias
    // al fix de "disabled" que ya aplicamos en el toggle
    form.querySelectorAll('#seccion-promesa [required]:not(:disabled), #seccion-confirmacion [required]:not(:disabled)')
        .forEach(el => {
            const vacio = !el.value || !el.value.trim();
            if (vacio) {
                marcarInvalido(el);
                const etiqueta = el.closest('label')?.textContent?.trim()
                    || el.getAttribute('name')
                    || 'Campo';
                errores.push(etiqueta);
                if (!primerCampoInvalido) primerCampoInvalido = el;
            } else {
                marcarValido(el);
            }
        });

    mostrarResumenErrores(errores);

    if (primerCampoInvalido) {
        primerCampoInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
        primerCampoInvalido.focus();
    }

    return errores.length === 0;
}
