<?php

namespace App\Http\Controllers;

use App\Models\NuevoCorreo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CorreoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // 1. Validar los datos recibidos desde la interfaz
        $validated = $request->validate([
            'cod_ban' => 'required|string|max:2',
            'cod_deu' => 'required|string|max:11',
            'tipo'    => 'required|string|in:Personal,Secundario,Trabajo,Otro',
            'correo'        => 'required',
            'usuario'  => 'required|string|min:2',
        ], [
            'correo.required'       => 'El correo es obligatorio.',
        ]);

         $correoLimpio = trim($validated['correo']);

        // 2. Comprobar si ya existe el número registrado para este cliente específico
        $existe = NuevoCorreo::where('cod_deu', $validated['cod_deu'])
            ->where('correo', $correoLimpio)
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'El Correo ya se encuentra registrado para este cliente.',
            ], 422); // Status 422 (Unprocessable Entity) o 400
        }

        try {
            // 2. Insertar directamente en t_nuevos_numeros
            $nuevoCorreo = NuevoCorreo::create([
                'cod_ban' => $validated['cod_ban'],
                'cod_deu' => $validated['cod_deu'],
                'correo'  => trim($validated['correo']),
                'tipo_correo'    => $validated['tipo'],
                'usuario' => $validated['usuario'], // Toma el usuario autenticado o '91' por defecto
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Correo registrado correctamente.',
                'data'    => $nuevoCorreo,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el Correo: ' . $e->getMessage(),
            ], 500);
        }
    }

}
