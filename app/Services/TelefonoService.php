<?php
namespace App\Services;

use App\Models\NuevoNumero;
use App\Http\Requests\GuardarTelefonoRequest;

class TelefonoService
{
    /**
     * Registra un nuevo teléfono verificando duplicados para el cliente.
     *
     * @throws \Exception
     */
    public function registrarTelefono(GuardarTelefonoRequest $request): NuevoNumero
    {
        $validated = $request->validated();
        $numeroLimpio = trim($validated['numero']);

        // 1. Comprobar existencia
        $existe = NuevoNumero::where('cod_deu', $validated['cod_deu'])
            ->where('numero', $numeroLimpio)
            ->exists();

        if ($existe) {
            throw new \Exception('El número de teléfono ya se encuentra registrado para este cliente.', 422);
        }

        // 2. Crear registro
        return NuevoNumero::create([
            'cod_ban'       => $validated['cod_ban'],
            'cod_deu'       => $validated['cod_deu'],
            'numero'        => $numeroLimpio,
            'tipo_telefono' => $validated['tipo'],
            'usuario'       => $validated['usuario'],
        ]);
    }
}
