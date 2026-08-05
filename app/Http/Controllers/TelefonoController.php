<?php

namespace App\Http\Controllers;

use App\Models\NuevoNumero;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TelefonoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // 1. Validar los datos recibidos desde la interfaz
        $validated = $request->validate([
            'cod_ban' => 'required|string|max:2',
            'cod_deu' => 'required|string|max:11',
            'tipo'    => 'required|string|in:Celular,Oficina,Casa,Otro',
            'numero'        => 'required|digits_between:7,15', // Solo dígitos numéricos (entre 7 y 15 de largo)
            'usuario'  => 'required|string|min:2',
        ], [
            'numero.required'       => 'El número de teléfono es obligatorio.',
            'numero.digits_between' => 'El teléfono debe contener solo números (entre 7 y 15 dígitos).',
        ]);

        $numeroLimpio = trim($validated['numero']);

        // 2. Comprobar si ya existe el número registrado para este cliente específico
        $existe = NuevoNumero::where('cod_deu', $validated['cod_deu'])
            ->where('numero', $numeroLimpio)
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'El número de teléfono ya se encuentra registrado para este cliente.',
            ], 422); // Status 422 (Unprocessable Entity) o 400
        }

        try {
            // 2. Insertar directamente en t_nuevos_numeros
            $nuevoTelefono = NuevoNumero::create([
                'cod_ban' => $validated['cod_ban'],
                'cod_deu' => $validated['cod_deu'],
                'numero'  => trim($validated['numero']),
                'tipo_telefono'    => $validated['tipo'],
                'usuario' => $validated['usuario'], // Toma el usuario autenticado o '91' por defecto
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Teléfono registrado correctamente.',
                'data'    => $nuevoTelefono,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el teléfono: ' . $e->getMessage(),
            ], 500);
        }
    }
}
