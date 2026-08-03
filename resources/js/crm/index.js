import { toggleDrawer, cerrarDrawer, cambiarTabDrawer, initDrawerAtajos } from './drawer';
import { abrirModalModificar, cerrarModalModificar, mostrarFormularioCondicion, ocultarFormularioCondicion, editarGestion } from './modal-modificar';
import { abrirHistorial, cerrarModalHistorial, cargarPaginaHistorial } from './historial';
import { cargarPaginaGestionesEditar } from './gestiones-editar';
import { setComentario, initPanelGestion } from './panel-gestion';

// Puente hacia el HTML: los onclick="..." en los .blade.php necesitan que
// estas funciones existan en window. Si en el futuro se migran los onclick
// a addEventListener, este bloque completo deja de ser necesario.
window.toggleDrawer = toggleDrawer;
window.cerrarDrawer = cerrarDrawer;
window.cambiarTabDrawer = cambiarTabDrawer;
window.abrirModalModificar = abrirModalModificar;
window.cerrarModalModificar = cerrarModalModificar;
window.mostrarFormularioCondicion = mostrarFormularioCondicion;
window.ocultarFormularioCondicion = ocultarFormularioCondicion;
window.abrirHistorial = abrirHistorial;
window.cerrarModalHistorial = cerrarModalHistorial;
window.cargarPaginaHistorial = cargarPaginaHistorial;
window.cargarPaginaGestionesEditar = cargarPaginaGestionesEditar;
window.setComentario = setComentario;
window.editarGestion = editarGestion;

document.addEventListener('DOMContentLoaded', () => {
    initDrawerAtajos();
    initPanelGestion();
});
