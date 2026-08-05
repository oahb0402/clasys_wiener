<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClasysController; // <-- Asegúrate de apuntar a ClasysController
use App\Http\Controllers\CorreoController;
use App\Http\Controllers\TelefonoController;
// La ruta principal de gestión de tu CRM
Route::get('/crm/gestion/{id}', [ClasysController::class, 'index'])->name('crm.principal');
// ruta para el endpoint del historial
Route::get('/crm/gestion/{id}/historial/{tipo}', [ClasysController::class, 'historial'])->name('crm.historial');
// ruta para el endpoint del la gestion de la g220 por item
Route::get('/crm/gestion/{id}/gestion/{item}', [ClasysController::class, 'obtenerGestionParaEditar'])->name('crm.gestion.detalle');
// ruta para el endpoint para guardar la gestion nueva
Route::post('/crm/gestion/{id}/gestion', [ClasysController::class, 'guardarGestion'])->name('crm.gestion.store');
// ruta para el endpoint para editar una gestion por cod_deu e item
Route::put('/crm/gestion/{id}/gestion/{item}', [ClasysController::class, 'actualizarGestion'])->name('crm.gestion.update');

// ruta para el endpoint para agregar numeros y telefonos nuevos
Route::post('/telefonos/guardar', [TelefonoController::class, 'store'])->name('telefonos.store');
Route::post('/correos/guardar', [CorreoController::class, 'store'])->name('correos.store');
