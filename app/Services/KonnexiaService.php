<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class KonnexiaService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.konnexia.base_url');
        $this->apiKey  = config('services.konnexia.api_key');
    }

    /**
     * Finaliza la llamada en Konnexia y registra auditoría
     */
    public function finalizarLlamada(Request $request): array
    {
        $url = "{$this->baseUrl}/finalize";
        $payload = $this->construirPayload($request);

        try {
            $response = Http::withHeaders([
                'Content-Type'       => 'application/json',
                'Accept'             => '*/*',
                'X-Integration-Key' => $this->apiKey,
            ])->post($url, $payload);

            $statusCode   = $response->status();
            $responseBody = $response->body();

            $this->registrarLog($payload, $responseBody, $statusCode);

            return [
                'exito'  => $response->successful(),
                'status' => $statusCode,
                'data'   => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error("Error consumiendo API Konnexia: " . $e->getMessage());

            $this->registrarLog($payload, json_encode(['error' => $e->getMessage()]), 500);

            return [
                'exito'  => false,
                'status' => 500,
                'error'  => $e->getMessage()
            ];
        }
    }

    /**
     * Construye el cuerpo del JSON para Konnexia
     */
    protected function construirPayload(Request $request): array
    {
        $callbackAt = null;
        if ($request->boolean('agendar') && $request->filled('fec_agenda')) {
            $fecAgenda = $request->input('fec_agenda');
            $horAgenda = substr($request->input('hor_agenda', '00:00'), 0, 5);

            $fechaAgendaFormatted = date('d-m-Y', strtotime(str_replace('/', '-', $fecAgenda)));
            $callbackAt = "{$fechaAgendaFormatted} {$horAgenda}";
        }

        $payload = [
            'callId'        => $request->input('comenta2'),
            'metadata'      => (object) [],
            'callbackAt'    => $callbackAt,
            'disposition'   => [
                'level1'  => $request->input('control_grupo'),
                'level2'  => $request->input('control'),
                'level3'  => $request->input('level3', ''),
                'comment' => $request->input('comentario'),
            ],
            'agentUsername' => $request->input('usuario'),
        ];

        if ($request->filled('fecha_promesa')) {
            $fecPromesa = $request->input('fecha_promesa');
            $fechaFormatted = date('d-m-Y', strtotime(str_replace('/', '-', $fecPromesa)));

            $montoRaw = $request->input('monto_promesa');
            $montoFormatted = is_numeric($montoRaw)
                ? number_format((float)$montoRaw, 2, '.', '')
                : "0.00";

            $monedaRaw = strtoupper($request->input('moneda_promesa', 'SOLES'));
            $moneda = ($monedaRaw === 'S' || $monedaRaw === 'SOLES') ? 'SOLES' : 'DOLARES';

            $payload['promise'] = [
                'date'     => $fechaFormatted,
                'amount'   => $montoFormatted,
                'currency' => $moneda,
            ];
        }

        return $payload;
    }

    /**
     * Registra el log en la tabla log_integracion_konnexia
     */
    protected function registrarLog(array $payload, string $responseBody, int $statusCode): void
    {
        DB::table('log_integracion_konnexia')->insert([
            'call_id'       => $payload['callId'] ?? null,
            'agente'        => $payload['agentUsername'] ?? null,
            'categoria'     => $payload['disposition']['level1'] ?? null,
            'subcategoria'  => $payload['disposition']['level2'] ?? null,
            'comentario'    => $payload['disposition']['comment'] ?? null,
            'request_body'  => json_encode($payload),
            'response_body' => $responseBody,
            'status_code'   => $statusCode,
        ]);
    }
}
