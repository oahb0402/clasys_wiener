<?php

namespace App\Services;

use App\Models\NuevoCorreo;
use App\Http\Requests\GuardarCorreoRequest;

class CorreoService
{
    /**
     * Registra un nuevo Correo verificando duplicados para el cliente.
     *
     * @throws \Exception
     */
    public function registrarCorreo(GuardarCorreoRequest $request): NuevoCorreo
    {
        $validated = $request->validated();
        $correoLimpio = trim($validated['correo']);

        // 1. Comprobar existencia
        $existe = NuevoCorreo::where('cod_deu', $validated['cod_deu'])
            ->where('correo', $correoLimpio)
            ->exists();

        if ($existe) {
            throw new \Exception('El Correo ya se encuentra registrado para este cliente.', 422);
        }

        // 2. Crear registro
        return NuevoCorreo::create([
            'cod_ban'       => $validated['cod_ban'],
            'cod_deu'       => $validated['cod_deu'],
            'correo'        => $correoLimpio,
            'tipo_correo' => $validated['tipo'],
            'usuario'       => $validated['usuario'],
        ]);
    }
}
