<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarCorreoRequest;
use App\Services\CorreoService;
use Illuminate\Http\JsonResponse;

class CorreoController extends Controller
{

     protected CorreoService $correoService;

     public function __construct(CorreoService $correoService)
    {
        $this->correoService = $correoService;
    }


    public function store(GuardarCorreoRequest $request): JsonResponse
    {
        try {
            $nuevoCorreo = $this->correoService->registrarCorreo($request);

            return response()->json([
                'success' => true,
                'message' => 'Correo registrado correctamente.',
                'data'    => $nuevoCorreo,
            ], 201);

        } catch (\Exception $e) {
            $code = $e->getCode() === 422 ? 422 : 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $code);
        }
    }

}
