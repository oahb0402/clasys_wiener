<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClasysController; // <-- Asegúrate de apuntar a ClasysController

// La ruta principal de gestión de tu CRM
Route::get('/crm/gestion/{id}', [ClasysController::class, 'index'])->name('crm.principal');

Route::get('/crm/gestion/{id}/historial-sms', [ClasysController::class, 'getHistorialSms'])->name('crm.principal');