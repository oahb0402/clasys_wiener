// Un solo modal + una sola función, parametrizados por "tipo".
// Para agregar un nuevo tipo de historial en el futuro, solo hay que
// agregar una entrada aquí — no se duplica lógica de fetch/paginación.
const TIPOS_HISTORIAL = {
    gestiones_positivas: {
        endpoint: 'positivas',
        //params: { solo_positivas: 1 },
        titulo: 'Gestiones Positivas',
    },
    total_gestiones: {
        endpoint: 'gestiones',
        titulo: 'Historial de Gestiones',
    },
    ivr: {
        endpoint: 'ivr',
        titulo: 'Historial de Llamadas (IVR)',
    },
    sms: {
        endpoint: 'sms',
        titulo: 'Historial de SMS',
    },
    mail: {
        endpoint: 'mail',
        titulo: 'Historial de Correos',
    },
    abonos: {
        endpoint: 'abonos',
        titulo: 'Historial de Abonos',
    },
};

let tipoActual = null;

function getClienteId() {
    return window.APP_CLIENTE_ID ?? 0;
}

function construirUrl(pagina) {
    const url = new URL(
        `/crm/gestion/${getClienteId()}/historial/${tipoActual.endpoint}`,
        window.location.origin
    );
    url.searchParams.set('page', pagina);

    Object.entries(tipoActual.params || {}).forEach(([clave, valor]) => {
        url.searchParams.set(clave, valor);
    });

    return url;
}

export function abrirHistorial(tipo) {
    const config = TIPOS_HISTORIAL[tipo];

    if (!config) {
        console.warn(`abrirHistorial: tipo desconocido "${tipo}"`);
        return;
    }

    tipoActual = config;
    document.getElementById('tituloModalHistorial').textContent = config.titulo;
    document.getElementById('modalHistorial').classList.remove('hidden');
    cargarPaginaHistorial(1);
}

export function cerrarModalHistorial() {
    document.getElementById('modalHistorial').classList.add('hidden');
}

export function cargarPaginaHistorial(pagina) {
    if (!tipoActual) return;

    const tbody = document.getElementById('tbodyHistorial');
    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                <span class="inline-block animate-spin mr-2">🌀</span> Cargando historial...
            </td>
        </tr>
    `;

    fetch(construirUrl(pagina))
        .then(response => {
            if (!response.ok) throw new Error('Error en la red');
            return response.json();
        })
        .then(res => {
            let htmlFilas = '';
            if (!res.data || res.data.length === 0) {
                htmlFilas = `<tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No se encontraron registros.</td></tr>`;
            } else {
                res.data.forEach(item => {
                    const badgeClass = item.estado === 'ANSWERED'
                        ? 'bg-indigo-50 text-indigo-600 border border-indigo-100'
                        : 'bg-purple-50 text-purple-600 border border-purple-100';

                    htmlFilas += `
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3 text-center font-bold text-gray-700">${item.item_num}</td>
                            <td class="px-4 py-3 text-gray-600 font-mono whitespace-nowrap text-nowrap">${item.fecha}</td>
                            <td class="px-4 py-3 text-gray-600">${item.contacto ?? ''}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${badgeClass}">
                                    ${item.estado ?? ''}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">${item.comentario ?? ''}</td>
                        </tr>
                    `;
                });
            }
            tbody.innerHTML = htmlFilas;

            document.getElementById('infoPaginacionHistorial').innerHTML =
                `Mostrando <b class="text-gray-800">${res.first_item}</b> a <b class="text-gray-800">${res.last_item}</b> de <b class="text-gray-800">${res.total}</b> registros`;

            renderizarBotonesPaginacion(res.current_page, res.last_page);
        })
        .catch(() => {
            tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-red-500">Error al cargar la información.</td></tr>`;
        });
}

function renderizarBotonesPaginacion(actual, ultima) {
    let html = '';

    if (actual > 1) {
        html += `<button onclick="cargarPaginaHistorial(${actual - 1})" class="px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-100 text-gray-600 font-medium transition-colors">Anterior</button>`;
    } else {
        html += `<button disabled class="px-3 py-1.5 border border-gray-100 rounded-lg text-gray-300 cursor-not-allowed">Anterior</button>`;
    }

    for (let i = 1; i <= ultima; i++) {
        if (i === actual) {
            html += `<button class="w-8 h-8 rounded-lg bg-blue-600 text-white font-bold">${i}</button>`;
        } else {
            html += `<button onclick="cargarPaginaHistorial(${i})" class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-600 font-medium transition-colors">${i}</button>`;
        }
    }

    if (actual < ultima) {
        html += `<button onclick="cargarPaginaHistorial(${actual + 1})" class="px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-100 text-gray-600 font-medium transition-colors">Siguiente</button>`;
    } else {
        html += `<button disabled class="px-3 py-1.5 border border-gray-100 rounded-lg text-gray-300 cursor-not-allowed">Siguiente</button>`;
    }

    document.getElementById('contenedorPaginacionHistorial').innerHTML = html;
}
