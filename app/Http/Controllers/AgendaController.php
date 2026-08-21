<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AgendaController extends Controller
{
    public function store(Request $request): JsonResponse
    {

        // 1. Validar los datos recibidos desde la interfaz
        $validated = $request->validate([
            'cod_deu' => 'required|string|max:11',
            'fec_agenda' => 'required_if:agendar,1|nullable|date',
            'hor_agenda' => 'required_if:agendar,1|nullable|date_format:H:i',
            'usuario'  => 'required|string|min:2',
            'comentario' => 'nullable|string',
            'cod_ban' => 'required|string|max:2',

        ]);

        try {
            // 3. Registrar en la tabla `agendas` solo si se marcó el checkbox
            if ($request->boolean('agendar')) {
                Agenda::create([
                    'cod_deu'    => $validated['cod_deu'],
                    'fecha'     => $validated['fec_agenda'],
                    'hora'     => $validated['hor_agenda'],
                    'usuario'     => $validated['usuario'],
                    'obs'     => $validated['comentario'],
                    'cartera'     => $validated['cod_ban'],
                    'cod_ban'     => $validated['cod_ban'],
                    'usuario_creador'  => $validated['usuario'],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar Agenda: ' . $e->getMessage(),
            ], 500);
        }
    }
}
