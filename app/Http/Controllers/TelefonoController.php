<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarTelefonoRequest;
use App\Services\TelefonoService;
use Illuminate\Http\JsonResponse;

class TelefonoController extends Controller
{
    protected TelefonoService $telefonoService;

    public function __construct(TelefonoService $telefonoService)
    {
        $this->telefonoService = $telefonoService;
    }

    public function store(GuardarTelefonoRequest $request): JsonResponse
    {
        try {
            $nuevoTelefono = $this->telefonoService->registrarTelefono($request);

            return response()->json([
                'success' => true,
                'message' => 'Teléfono registrado correctamente.',
                'data'    => $nuevoTelefono,
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
