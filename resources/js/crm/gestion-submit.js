import { getClienteId } from './gestiones-editar';
import { validarFormularioGestion } from './gestion-validacion';

export function activarModoEdicion(itemId) {
    document.getElementById('editar_item_id').value = itemId;
    document.getElementById('botones-nueva-gestion').classList.add('hidden');
    document.getElementById('botones-editar-gestion').classList.remove('hidden');
    document.getElementById('botones-editar-gestion').classList.add('flex');
}

function desactivarModoEdicion() {
    document.getElementById('editar_item_id').value = '';
    document.getElementById('botones-nueva-gestion').classList.remove('hidden');
    document.getElementById('botones-editar-gestion').classList.add('hidden');
}

export function cancelarEdicionGestion() {
    document.getElementById('form-gestion').reset();
    desactivarModoEdicion();

    // reset() no dispara 'change', así que las secciones condicionales
    // hay que ocultarlas/deshabilitarlas a mano
    document.getElementById('seccion-promesa').classList.add('hidden');
    document.getElementById('seccion-confirmacion').classList.add('hidden');
    const selectSubres = document.getElementById('select-subres');
    selectSubres.disabled = true;
    selectSubres.classList.add('bg-slate-100');
}

function construirUrlAccion(itemId) {
    const clienteId = getClienteId();
    return itemId
        ? `/crm/gestion/${clienteId}/gestion/${itemId}`
        : `/crm/gestion/${clienteId}/gestion`;
}

export function initFormGestionSubmit() {
    const form = document.getElementById('form-gestion');
    if (!form) return;

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        if (!validarFormularioGestion(form)) {
            return; // corta acá: no se hace fetch si falta algo
        }

        const itemId = document.getElementById('editar_item_id').value;
        const accion = event.submitter ? event.submitter.value : '';
        const formData = new FormData(form);
        formData.set('accion', accion);
        if (itemId) formData.set('_method', 'PUT');

        fetch(construirUrlAccion(itemId), {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
            .then(response => {
                if (!response.ok) throw new Error('Error al guardar');
                return response.json();
            })
            .then(res => {
                if (itemId) desactivarModoEdicion();
                form.reset();
                document.getElementById('errores-gestion').classList.add('hidden');
                alert(res.mensaje ?? 'Gestión guardada correctamente.');
            })
            .catch(() => {
                alert('Ocurrió un error al guardar la gestión.');
            });
    });
}
