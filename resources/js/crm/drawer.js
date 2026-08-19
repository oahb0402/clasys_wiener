import { ocultarFormularioCondicion } from './modal-modificar';

export function toggleDrawer() {
    const drawer = document.getElementById('drawer-menu');
    drawer.classList.toggle('hidden');
    if (!drawer.classList.contains('hidden')) {
        document.getElementById('nuevo_numero')?.focus();
    }
}

export function cerrarDrawer() {
    const drawer = document.getElementById('drawer-menu');
    if (drawer) {
        drawer.classList.add('hidden');
    }
}

export function cambiarTabDrawer(tab) {
    const tabs = ['numeros', 'correos', 'menu'];

    tabs.forEach(t => {
        const content = document.getElementById(`content-${t}`);
        const btnTab = document.getElementById(`tab-${t}`);

        if (content) content.classList.add('hidden');
        if (btnTab) {
            btnTab.classList.remove('active-tab');
            btnTab.classList.add('inactive-tab');
        }
    });

    const contentActivo = document.getElementById(`content-${tab}`);
    const tabActivo = document.getElementById(`tab-${tab}`);

    if (contentActivo) contentActivo.classList.remove('hidden');
    if (tabActivo) {
        tabActivo.classList.remove('inactive-tab');
        tabActivo.classList.add('active-tab');
    }

    // Resetear formulario interno si sale de "Más Opciones"
    if (tab !== 'menu') {
        ocultarFormularioCondicion();
    }
}

export function initDrawerAtajos() {
    document.addEventListener('keydown', function (event) {
        // Alt + N abre el drawer
        if (event.altKey && (event.key === 'n' || event.key === 'N')) {
            event.preventDefault();
            toggleDrawer();
        }

        // ESC lo cierra
        if (event.key === 'Escape') {
            cerrarDrawer();
        }
    });
}

/**
 * Procesa el envío del formulario para guardar el nuevo número vía AJAX
 */
export function guardarTelefono(event) {
    event.preventDefault();

    const form = event.target;
    const btnGuardar = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);

    // Obtener la URL del atributo action del form o usar la ruta por defecto
    const url = form.action || '/telefonos/guardar';

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
            alert('¡Teléfono guardado exitosamente!');
            form.reset();
            cerrarDrawer(); // Reutiliza la función existente para cerrar el drawer

            // Opcional: si tienes un evento para recargar la lista de números:
            // document.dispatchEvent(new CustomEvent('telefono-guardado', { detail: data.data }));
        } else {
            alert(data.message || 'Ocurrió un error al intentar guardar.');
        }
    })
    .catch(error => {
        console.error('Error al guardar el teléfono:', error);
        alert('Ocurrió un error de conexión con el servidor.');
    })
    .finally(() => {
        if (btnGuardar) {
            btnGuardar.disabled = false;
            btnGuardar.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    });
}


/**
 * Procesa el envío del formulario para guardar el nuevo correo vía AJAX
 */
export function guardarCorreo(event) {
    event.preventDefault();

    const form = event.target;
    const btnGuardar = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);

    // Obtener la URL del atributo action del form o usar la ruta por defecto
    const url = form.action || '/correos/guardar';

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
            alert('Correo guardado exitosamente!');
            form.reset();
            cerrarDrawer(); // Reutiliza la función existente para cerrar el drawer

            // Opcional: si tienes un evento para recargar la lista de números:
            // document.dispatchEvent(new CustomEvent('telefono-guardado', { detail: data.data }));
        } else {
            alert(data.message || 'Ocurrió un error al intentar guardar.');
        }
    })
    .catch(error => {
        console.error('Error al guardar el teléfono:', error);
        alert('Ocurrió un error de conexión con el servidor.');
    })
    .finally(() => {
        if (btnGuardar) {
            btnGuardar.disabled = false;
            btnGuardar.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    });
}

/* ==========================================================================
   NUEVO: MENÚ DESPLEGABLE USUARIO DIALER
   ========================================================================== */

/**
 * Alterna la visibilidad del menú desplegable del usuario
 */
export function toggleMenuUsuario(event) {
    if (event) event.stopPropagation();
    const menu = document.getElementById('dropdown-usuario-dialer');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

/**
 * Inicializa los eventos globales para cerrar el menú desplegable al hacer click fuera
 */
export function initMenuUsuarioEvents() {
    document.addEventListener('click', (event) => {
        const menu = document.getElementById('dropdown-usuario-dialer');
        if (menu && !menu.classList.contains('hidden')) {
            // Verifica si el clic no ocurrió dentro del menú
            if (!menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        }
    });
}
