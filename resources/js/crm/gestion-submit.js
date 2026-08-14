import { getClienteId } from './gestiones-editar';
import { validarFormularioGestion } from './gestion-validacion';
import { resetSeccionAgendar,actualizarAlertaPromesaActiva } from './panel-gestion'; // <-- Importado

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

    // Resetear la sección de agendado
    resetSeccionAgendar(); // <-- Se añade para desmarcar y ocultar al cancelar

    // reset() no dispara 'change', así que las secciones condicionales
    // hay que ocultarlas/deshabilitarlas a mano
    document.getElementById('seccion-promesa').classList.add('hidden');
    document.getElementById('seccion-confirmacion').classList.add('hidden');
    const selectSubres = document.getElementById('select-subres');
    if (selectSubres) {
        selectSubres.disabled = true;
        selectSubres.classList.add('bg-slate-100');
    }
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
.then(async response => {
    const res = await response.json();

    // Si el servidor responde con error (como 422 por validación)
    if (!response.ok) {
        if (response.status === 422 && res.errors) {
            // Extraer y unir todos los mensajes de error de validación
            const mensajes = Object.values(res.errors).flat().join('\n');
            alert(mensajes);
        } else {
            alert(res.message || res.mensaje || 'Error al procesar la solicitud.');
        }
        throw new Error('Error de validación o servidor');
    }

    return res;
})
.then(res => {
    if (itemId) desactivarModoEdicion();
    form.reset();
    resetSeccionAgendar();

    // Actualiza la alerta de promesa si la respuesta la contiene
    if (res.promesa_activa) {
        actualizarAlertaPromesaActiva(res.promesa_activa);
    }

    document.getElementById('errores-gestion')?.classList.add('hidden');
    alert(res.mensaje ?? 'Gestión guardada correctamente.');
})
.catch(error => {
    // Este catch solo atrapará errores de conexión/red o el throw de arriba
    console.error('Detalle del error:', error);
});
    });
}
