// Abrir y Cerrar Modal
export function abrirModalSolicitudCorreo() {
    const modal = document.getElementById('modalSolicitudCorreo');
    if (modal) modal.classList.remove('hidden');
}

export function cerrarModalSolicitudCorreo() {
    const modal = document.getElementById('modalSolicitudCorreo');
    if (modal) modal.classList.add('hidden');
}

 // Procesa el envío del formulario para guardar la solicitud vía AJAX
export function guardarSolicitudCorreo(event) {
    event.preventDefault();

    const form = event.target;
    const btnGuardar = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);

    // Obtener la URL del atributo action del form o usar la ruta por defecto
    const url = form.action || '/envMail/guardar';

    // Obtener el token CSRF desde la etiqueta meta o del input _token del formulario
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || formData.get('_token');

    // Deshabilitar botón durante el proceso
    if (btnGuardar) {
        btnGuardar.disabled = true;
        btnGuardar.classList.add('opacity-75', 'cursor-not-allowed');
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Solicitud creada exitosamente!');
            form.reset();
            cerrarModalSolicitudCorreo(); // Reutiliza la función existente para cerrar el drawer

            // Opcional: si tienes un evento para recargar la lista de números:
            // document.dispatchEvent(new CustomEvent('telefono-guardado', { detail: data.data }));
        } else {
            alert(data.message || 'Ocurrió un error al intentar guardar.');
        }
    })
    .catch(error => {
        console.error('Error al guardar la solicitud:', error);
        alert('Ocurrió un error de conexión con el servidor.');
    })
    .finally(() => {
        if (btnGuardar) {
            btnGuardar.disabled = false;
            btnGuardar.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    });
}


