import { renderizarBotonesPaginacion } from './utils/paginacion';

function getClienteId() {
    return window.APP_CLIENTE_ID ?? 0;
}

export function cargarPaginaGestionesEditar(pagina) {
    const tbody = document.getElementById('tbodyGestionesEditar');
    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="p-6 text-center text-slate-400">
                <span class="inline-block animate-spin mr-2">🌀</span> Cargando gestiones...
            </td>
        </tr>
    `;

    fetch(`/crm/gestion/${getClienteId()}/historial/editar_gestiones?page=${pagina}`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la red');
            return response.json();
        })
        .then(res => {
            let htmlFilas = '';

            if (!res.data || res.data.length === 0) {
                htmlFilas = `<tr><td colspan="8" class="p-6 text-center text-slate-400">No hay gestiones registradas.</td></tr>`;
            } else {
                res.data.forEach(item => {
                    htmlFilas += `
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-3 text-center">
                                <button type="button" onclick="editarGestion(${item.id})" class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition-colors inline-flex items-center gap-1 font-bold text-[11px]">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Editar
                                </button>
                            </td>
                            <td class="p-3 text-center font-bold text-slate-800">${item.id}</td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="font-semibold text-slate-700">${item.fecha}</span>
                                <span class="block text-[10px] text-slate-400">${item.hora ?? ''}</span>
                            </td>
                            <td class="p-3 font-medium text-slate-800">${item.respuesta ?? ''}</td>
                            <td class="p-3 text-slate-500">${item.sub_respuesta ?? ''}</td>
                            <td class="p-3 text-center font-mono">S/ ${Number(item.monto_pdp ?? 0).toFixed(2)}</td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-bold rounded text-[10px]">${item.condicion ?? ''}</span>
                            </td>
                            <td class="p-3 font-mono">${item.telefono ?? ''}</td>
                        </tr>
                    `;
                });
            }

            tbody.innerHTML = htmlFilas;
            renderizarBotonesPaginacion(res.current_page, res.last_page, 'contenedorPaginacionGestionesEditar', 'cargarPaginaGestionesEditar');
        })
        .catch(() => {
            tbody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-red-500">Error al cargar las gestiones.</td></tr>`;
        });
}
