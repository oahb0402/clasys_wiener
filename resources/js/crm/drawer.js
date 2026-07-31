import { ocultarFormularioCondicion } from './modal-modificar';

export function toggleDrawer() {
    const drawer = document.getElementById('drawer-menu');
    drawer.classList.toggle('hidden');
    if (!drawer.classList.contains('hidden')) {
        document.getElementById('nuevo_numero').focus();
    }
}

export function cerrarDrawer() {
    document.getElementById('drawer-menu').classList.add('hidden');
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
