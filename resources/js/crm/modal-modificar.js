import { cerrarDrawer } from './drawer';

export function abrirModalModificar() {
    // 1. Cerrar el drawer lateral primero
    if (typeof cerrarDrawer === 'function') {
        cerrarDrawer();
    } else {
        document.getElementById('drawer-menu').classList.add('hidden');
    }

    // 2. Mostrar el modal de gestiones
    document.getElementById('modal-modificar-gestion').classList.remove('hidden');
}

export function cerrarModalModificar() {
    document.getElementById('modal-modificar-gestion').classList.add('hidden');
}

// Mostrar el select de condición dentro de "Más Opciones"
export function mostrarFormularioCondicion() {
    document.getElementById('sub-menu-lista').classList.add('hidden');
    document.getElementById('sub-menu-condicion').classList.remove('hidden');
}

// Volver a la lista de opciones
export function ocultarFormularioCondicion() {
    document.getElementById('sub-menu-condicion').classList.add('hidden');
    document.getElementById('sub-menu-lista').classList.remove('hidden');
}
