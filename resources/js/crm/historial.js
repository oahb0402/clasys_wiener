import { renderizarBotonesPaginacion } from "./utils/paginacion";

// Un solo modal + una sola función, parametrizados por "tipo".
// Los "endpoint" deben coincidir EXACTO con los casos del match() en
// ClasysController::configHistorial() (sms, ivr, mail, gestiones, positivas).
// Las claves del objeto (gestiones_positivas, total_gestiones...) son las
// que ya usan los botones en el HTML vía onclick="abrirHistorial('...')".
const TIPOS_HISTORIAL = {
    gestiones_positivas: {
        endpoint: "positivas",
        titulo: "Gestiones Positivas",
    },
    total_gestiones: {
        endpoint: "gestiones",
        titulo: "Historial de Gestiones",
    },
    ivr: {
        endpoint: "ivr",
        titulo: "Historial de Llamadas (IVR)",
    },
    sms: {
        endpoint: "sms",
        titulo: "Historial de SMS / WhatsApp",
    },
    mail: {
        endpoint: "mail",
        titulo: "Historial de Correos",
    },
    abonos: {
        endpoint: "abonos", // pendiente: aún no existe este caso en configHistorial()
        titulo: "Historial de Abonos",
    },
};

let tipoActual = null;

function getClienteId() {
    return window.APP_CLIENTE_ID ?? 0;
}

function construirUrl(pagina) {
    const url = new URL(
        `/crm/gestion/${getClienteId()}/historial/${tipoActual.endpoint}`,
        window.location.origin,
    );
    url.searchParams.set("page", pagina);

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
    document.getElementById("tituloModalHistorial").textContent = config.titulo;
    document.getElementById("modalHistorial").classList.remove("hidden");
    cargarPaginaHistorial(1);
}

export function cerrarModalHistorial() {
    document.getElementById("modalHistorial").classList.add("hidden");
}

export function cargarPaginaHistorial(pagina) {
    if (!tipoActual) return;

    const tbody = document.getElementById("tbodyHistorial");
    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                <span class="inline-block animate-spin mr-2">🌀</span> Cargando historial...
            </td>
        </tr>
    `;

    fetch(construirUrl(pagina))
        .then((response) => {
            if (!response.ok) throw new Error("Error en la red");
            return response.json();
        })
        .then((res) => {
            let htmlFilas = "";
            if (!res.data || res.data.length === 0) {
                htmlFilas = `<tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">No se encontraron registros.</td></tr>`;
            } else {
                res.data.forEach((item) => {
                    const badgeClass =
                        item.estado === "ANSWERED"
                            ? "bg-indigo-50 text-indigo-600 border border-indigo-100"
                            : "bg-purple-50 text-purple-600 border border-purple-100";

                    // Filas de tipo "gestiones"/"positivas" traen es_promesa desde el backend.
                    // En sms/ivr/mail viene undefined, así que nunca se resaltan.
                    const filaClase = item.es_promesa
                        ? "bg-amber-50/70 hover:bg-amber-100/70 border-l-4 border-l-amber-400"
                        : item.es_confirmacion
                          ? "bg-emerald-50/50 hover:bg-emerald-100/50 border-l-4 border-l-emerald-300" // Verde/Confirmación más claro
                          : "hover:bg-gray-50/50";

                    // Determinar las clases según la condición
                    // 1. Clases ajustadas con inline-flex y whitespace-nowrap
                    const estilosBadge = item.es_promesa
                        ? "inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold whitespace-nowrap bg-amber-100 text-amber-700 border border-amber-200"
                        : item.es_confirmacion
                          ? "inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold whitespace-nowrap bg-emerald-100 text-emerald-700 border border-emerald-200"
                          : `inline-flex items-center whitespace-nowrap ${badgeClass}`;

                    // 2. Concatenamos el código (estado) con la etiqueta correspondiente
                    const textoBadge = item.es_promesa
                        ? `${item.estado ?? ""} - Promesa`
                        : item.es_confirmacion
                          ? `${item.estado ?? ""} - Confirmación`
                          : (item.estado ?? "");

                    const formatoMonto = new Intl.NumberFormat("es-PE", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    });

                    // 2. Formatear el valor asegurando que sea un número válido
                    const montoFormateado = item.mon_pro
                        ? formatoMonto.format(Number(item.mon_pro))
                        : "-";

                    //<td class="px-4 py-3 text-center font-bold text-gray-700">${item.item_num}</td>

                    htmlFilas += `
                        <tr class="${filaClase} transition-colors">

                            <td class="px-4 py-3 text-center font-bold text-gray-700">${item.item}</td>
                            <td class="px-4 py-3 text-gray-600 font-mono whitespace-nowrap text-nowrap">${item.fecha}</td>
                            <td class="px-4 py-3 text-gray-600">${item.con_cam ?? ""}</td>
                            <td class="px-4 py-3 text-gray-600">${item.telef_ges ?? ""}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${estilosBadge}">
            ${textoBadge}
        </span>
    </td>
     <td class="px-4 py-3 text-gray-600 whitespace-nowrap">${item.fec_reg ?? ""}</td>
     <td class="px-4 py-3 text-gray-600 whitespace-nowrap font-medium text-right">
        ${montoFormateado !== "-" ? "S/ " + montoFormateado : "-"}
    </td>
                            <td class="px-4 py-3 text-gray-600">${item.comentario ?? ""}</td>
                            <td class="px-4 py-3 text-gray-600">${item.usuario ?? ""}</td>
                        </tr>
                    `;
                });
            }
            tbody.innerHTML = htmlFilas;

            document.getElementById("infoPaginacionHistorial").innerHTML =
                `Mostrando <b class="text-gray-800">${res.first_item}</b> a <b class="text-gray-800">${res.last_item}</b> de <b class="text-gray-800">${res.total}</b> registros`;

            renderizarBotonesPaginacion(
                res.current_page,
                res.last_page,
                "contenedorPaginacionHistorial",
                "cargarPaginaHistorial",
            );
        })
        .catch(() => {
            tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-red-500">Error al cargar la información.</td></tr>`;
        });
}
