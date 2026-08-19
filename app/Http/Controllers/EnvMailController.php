<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnvMail;
use App\Models\G110;
use Illuminate\Http\JsonResponse;
use Exception;

class EnvMailController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // 1. Validar los datos recibidos PRIMERO
        $validated = $request->validate([
            'cod_ban'        => 'required|string|max:2',
            'cod_deu'        => 'required|string|max:11',
            'fec_pago'       => 'required|date',
            'porcentaje'     => 'required|string',
            'monto_campania' => 'required|numeric|min:0',
            'usuario'        => 'required|string|min:2',
        ]);

        try {
            // 2. Buscar el cliente en la BD tras asegurar la validación
            $cliente = G110::where('cod_deu', $validated['cod_deu'])->firstOrFail();
            // 3. Registrar el correo en el modelo Envmail
            $nuevoEnvMail = Envmail::create([
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

            return response()->json([
                'success' => true,
                'message' => 'Correo registrado correctamente.',
                'data'    => $nuevoEnvMail,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la Solicitud: ' . $e->getMessage(),
            ], 500);
        }
    }
}
