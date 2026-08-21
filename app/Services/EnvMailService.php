<?php

namespace App\Services;
use App\Models\EnvMail;
use App\Models\G110;
use App\Http\Requests\GuardarEnvMailRequest;

class EnvMailService
{
     /**
     * Registra un nuevo teléfono verificando duplicados para el cliente.
     *
     * @throws \Exception
     */
    public function registrarEnvMail(GuardarEnvMailRequest $request): EnvMail
    {
        $validated = $request->validated();
        // 1.  Buscar el cliente en la BD tras asegurar la validación
        $cliente = G110::where('cod_deu', $validated['cod_deu'])->firstOrFail();

        if (!$cliente) {
            throw new \Exception('El cliente no se encuentra registrado.', 422);
        }

        // 2. Crear registro
        return Envmail::create([
                'cod_ban'    => $validated['cod_ban'],
                'cod_deu'    => $validated['cod_deu'],
                'grupo'      => $cliente->grupo,
                'nom_deu'    => $cliente->nom_deu,
                'nro_doc'    => $cliente->nro_doc,
                'fecha_pago' => $validated['fec_pago'],
                'porcentaje' => $validated['porcentaje'],
                'monto_pago' => $validated['monto_campania'],
                'created_by' => $validated['usuario'],
            ]);
    }
}
