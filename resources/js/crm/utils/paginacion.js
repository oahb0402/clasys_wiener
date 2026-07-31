/**
 * Renderiza botones de paginación (Anterior / números / Siguiente) genéricos.
 * Se usa desde cualquier módulo que pagine una lista vía AJAX.
 *
 * @param {number} actual              Página actual
 * @param {number} ultima              Última página disponible
 * @param {string} contenedorId        ID del elemento donde se inyectan los botones
 * @param {string} nombreFuncionGlobal Nombre de la función global (window.x) a llamar al hacer click en un número/flecha
 */
export function renderizarBotonesPaginacion(actual, ultima, contenedorId, nombreFuncionGlobal) {
    let html = '';

    if (actual > 1) {
        html += `<button onclick="${nombreFuncionGlobal}(${actual - 1})" class="px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-100 text-gray-600 font-medium transition-colors">Anterior</button>`;
    } else {
        html += `<button disabled class="px-3 py-1.5 border border-gray-100 rounded-lg text-gray-300 cursor-not-allowed">Anterior</button>`;
    }

    for (let i = 1; i <= ultima; i++) {
        if (i === actual) {
            html += `<button class="w-8 h-8 rounded-lg bg-blue-600 text-white font-bold">${i}</button>`;
        } else {
            html += `<button onclick="${nombreFuncionGlobal}(${i})" class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-600 font-medium transition-colors">${i}</button>`;
        }
    }

    if (actual < ultima) {
        html += `<button onclick="${nombreFuncionGlobal}(${actual + 1})" class="px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-100 text-gray-600 font-medium transition-colors">Siguiente</button>`;
    } else {
        html += `<button disabled class="px-3 py-1.5 border border-gray-100 rounded-lg text-gray-300 cursor-not-allowed">Siguiente</button>`;
    }

    document.getElementById(contenedorId).innerHTML = html;
}
