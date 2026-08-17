//import { cerrarDrawer } from './drawer';
//import { cargarPaginaGestionesEditar } from './gestiones-editar';

import { cerrarDrawer } from './drawer';
import { cargarPaginaGestionesEditar, getClienteId } from './gestiones-editar';
import { activarModoEdicion } from './gestion-submit';


export function abrirModalModificar() {
    // 1. Cerrar el drawer lateral primero
    if (typeof cerrarDrawer === 'function') {
        cerrarDrawer();
    } else {
        document.getElementById('drawer-menu').classList.add('hidden');
    }

    // 2. Mostrar el modal de gestiones
    document.getElementById('modal-modificar-gestion').classList.remove('hidden');

    // 3. Cargar la primera página de gestiones reales
    cargarPaginaGestionesEditar(1);
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
    //document.getElementById('sub-menu-condicion').classList.add('hidden');
    //document.getElementById('sub-menu-lista').classList.remove('hidden');
}

function rellenarSeccionPromesaOConfirmacion(data) {
    const seccionPromesa = document.getElementById('seccion-promesa');
    const seccionConfirmacion = document.getElementById('seccion-confirmacion');

    const seccionActiva = !seccionPromesa.classList.contains('hidden')
        ? seccionPromesa
        : (!seccionConfirmacion.classList.contains('hidden') ? seccionConfirmacion : null);

    if (!seccionActiva) return;

    const setCampo = (name, value) => {
        const el = seccionActiva.querySelector(`[name="${name}"]`);
        if (el) el.value = value ?? '';
    };

    setCampo('fecha_promesa', data.fecha_promesa);
    setCampo('monto_promesa', data.monto_promesa);
    setCampo('moneda_promesa', data.moneda_promesa);

    if (seccionActiva === seccionConfirmacion) {
        setCampo('nombre_titular', data.nombre_titular);
        setCampo('dni_titular', data.dni_titular);
        setCampo('datos_tarjeta', data.datos_tarjeta);
        setCampo('medio_pago', data.medio_pago);
        // El input type="file" nunca se puede precargar por JS.
        // Si quieres mostrar el comprobante ya subido, habría que agregar
        // un enlace/preview usando data.comprobante_confirmacion_url junto al input.
    }
}

export function editarGestion(id) {
    cerrarModalModificar();

    fetch(`/crm/gestion/${getClienteId()}/gestion/${id}`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la red');
            return response.json();
        })
        .then(data => {
            document.getElementById('select-tipcon').value = data.tipcon ?? '';
            document.getElementById('select-tipgb').value = data.tipgb ?? '';

            const selectControl = document.getElementById('select-control');
            selectControl.value = data.control ?? '';
            // Dispara el change para que panel-gestion.js muestre/oculte
            // seccion-promesa/confirmacion y filtre las sub-respuestas
            selectControl.dispatchEvent(new Event('change'));

            // El change de arriba ya filtró/autoseleccionó subres; ahora forzamos el valor real
            document.getElementById('select-subres').value = data.subres ?? '';

            document.getElementById('comentario').value = data.comentario ?? '';

            const selectCondicion = document.querySelector('select[name="condicion"]');
            if (selectCondicion) selectCondicion.value = data.condicion ?? '';

            rellenarSeccionPromesaOConfirmacion(data);
            activarModoEdicion(id);
        })
        .catch(() => {
            alert('No se pudo cargar la gestión para editar.');
        });
}
