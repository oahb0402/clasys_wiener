<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Envmail;
use Illuminate\Http\JsonResponse;


class EnvMailController extends Controller
{

      public function store(Request $request): JsonResponse
    {

        // 1. Validar los datos recibidos desde la interfaz
        $validated = $request->validate([
            'cod_ban' => 'required|string|max:2',
            'cod_deu' => 'required|string|max:11',
            'fec_pago'    => 'required',
            'porcentaje'        => 'required',
            'monto_campania'        => 'required',
            'usuario'  => 'required|string|min:2',
             ]);



        try {
            // 2. Insertar directamente en t_nuevos_numeros
            $nuevoEnvMail = Envmail::create([
                'cod_ban' => $validated['cod_ban'],
                'cod_deu' => $validated['cod_deu'],

                'fecha_pago' => $validated['fec_pago'],
                'porcentaje' => $validated['porcentaje'],
                'monto_pago' => $validated['monto_campania'],
                'created_by' => $validated['usuario'], // Toma el usuario autenticado o '91' por defecto
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Correo registrado correctamente.',
                'data'    => $nuevoEnvMail,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la Solicitud: ' . $e->getMessage(),
            ], 500);
        }
    }
}
