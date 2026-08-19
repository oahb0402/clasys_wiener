import { toggleDrawer, cerrarDrawer, cambiarTabDrawer, initDrawerAtajos, guardarTelefono, guardarCorreo,toggleMenuUsuario, initMenuUsuarioEvents } from './drawer';
import { abrirModalModificar, cerrarModalModificar, mostrarFormularioCondicion, ocultarFormularioCondicion, editarGestion } from './modal-modificar';
import { abrirHistorial, cerrarModalHistorial, cargarPaginaHistorial } from './historial';
import { cargarPaginaGestionesEditar } from './gestiones-editar';
import { setComentario, resetSeccionAgendar,initPanelGestion,actualizarAlertaPromesaActiva,configurarLimiteFechas, actualizarAlertaConfirmacionActiva } from './panel-gestion';
import { initFormGestionSubmit, cancelarEdicionGestion } from './gestion-submit';
import { abrirModalSolicitudCorreo, cerrarModalSolicitudCorreo,guardarSolicitudCorreo } from './modal-envMail';

window.cancelarEdicionGestion = cancelarEdicionGestion;

// Puente hacia el HTML: los onclick="..." y onsubmit="..." en los .blade.php necesitan que
// estas funciones existan en window. Si en el futuro se migran a addEventListener,
// este bloque completo deja de ser necesario.
window.toggleDrawer = toggleDrawer;
window.cerrarDrawer = cerrarDrawer;
window.cambiarTabDrawer = cambiarTabDrawer;
window.guardarTelefono = guardarTelefono; // <-- Agregado para el formulario del drawer
window.guardarCorreo = guardarCorreo; // <-- Agregado para el formulario del drawer
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
window.resetSeccionAgendar = resetSeccionAgendar; // <-- Expuesto a window
window.actualizarAlertaPromesaActiva = actualizarAlertaPromesaActiva; // <-- Expuesto a window
window.actualizarAlertaPromesaActiva = actualizarAlertaConfirmacionActiva; // <-- Expuesto a window
window.abrirModalSolicitudCorreo = abrirModalSolicitudCorreo;
window.cerrarModalSolicitudCorreo = cerrarModalSolicitudCorreo;
window.guardarSolicitudCorreo = guardarSolicitudCorreo; // <-- Agregado para el formulario del drawer
window.toggleMenuUsuario = toggleMenuUsuario;

document.addEventListener('DOMContentLoaded', () => {
    initDrawerAtajos();
    initPanelGestion();
    initFormGestionSubmit();
    configurarLimiteFechas();
    initMenuUsuarioEvents();
});
