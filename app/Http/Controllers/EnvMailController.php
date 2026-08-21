<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarEnvMailRequest;
use App\Services\EnvMailService;

use Illuminate\Http\JsonResponse;

class EnvMailController extends Controller
{
    protected EnvMailService $envMailService;

    public function __construct(EnvMailService $envMailService)
    {
        $this->envMailService = $envMailService;
    }
        public function store(GuardarEnvMailRequest $request): JsonResponse
    {
        try {

            $nuevoEnvMail = $this->envMailService->registrarEnvMail($request);

            return response()->json([
                'success' => true,
                'message' => 'Solicitud registrada correctamente.',
                'data'    => $nuevoEnvMail,
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
