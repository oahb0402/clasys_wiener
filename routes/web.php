<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClasysController; // <-- Asegúrate de apuntar a ClasysController

// La ruta principal de gestión de tu CRM
Route::get('/crm/gestion/{id}', [ClasysController::class, 'index'])->name('crm.principal');

// La ruta actual de historial Y DEL MODIFICAR
Route::get('/crm/gestion/{id}/historial/{tipo}', [ClasysController::class, 'historial'])->name('crm.historial');


Route::get('/crm/gestion/{id}/gestion/{item}', [ClasysController::class, 'obtenerGestionParaEditar'])
    ->name('crm.gestion.detalle');
