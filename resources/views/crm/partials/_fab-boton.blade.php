    <!-- BOTÓN FLOTANTE (FAB) -->
    <div class="fixed bottom-6 right-6 z-40 group">
        <!-- Tooltip al pasar el cursor (Hover) -->
        <div class="absolute bottom-full right-0 mb-2 hidden group-hover:flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 text-white text-[11px] font-medium rounded-lg shadow-xl whitespace-nowrap animate-in fade-in zoom-in-95 duration-100">
            <span>Acciones Rápidas</span>
            <kbd class="px-1.5 py-0.5 bg-slate-700 rounded text-[10px] font-mono text-slate-200">Alt + N</kbd>
        </div>

        <!-- Botón Principal -->
        <button type="button"
            onclick="toggleDrawer()"
            class="flex items-center justify-center w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 hover:scale-105 active:scale-95 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
        </button>
    </div>
