<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClasysController;
use App\Http\Controllers\CorreoController;
use App\Http\Controllers\TelefonoController;

// 1. La ruta principal de gestión AHORA recibe el cliente mediante ?cod_deu=... en la query string
Route::get('/crm/gestion', [ClasysController::class, 'index'])->name('crm.principal');

// 2. Rutas secundarias de API/AJAX (se mantienen con {id} en la URL para identificar al cliente fácilmente)
Route::get('/crm/gestion/{id}/historial/{tipo}', [ClasysController::class, 'historial'])->name('crm.historial');
Route::get('/crm/gestion/{id}/gestion/{item}', [ClasysController::class, 'obtenerGestionParaEditar'])->name('crm.gestion.detalle');
Route::post('/crm/gestion/{id}/gestion', [ClasysController::class, 'guardarGestion'])->name('crm.gestion.store');
Route::put('/crm/gestion/{id}/gestion/{item}', [ClasysController::class, 'actualizarGestion'])->name('crm.gestion.update');

// 3. Rutas para agregar números y correos
Route::post('/telefonos/guardar', [TelefonoController::class, 'store'])->name('telefonos.store');
Route::post('/correos/guardar', [CorreoController::class, 'store'])->name('correos.store');
