<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidarFirmaMarcador
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Clave Secreta con Fallback directo
        $secret = env('MARCADOR_SECRET_KEY', 'CET+NE3BbVrn+kTyqMv0rTWtZCab6S1CNoyGn0XN5GM=');

        // Extraer y limpiar los mismos valores

        $codDeu     = trim($request->query('cod_deu'));
        $telf       = trim($request->query('telf'));
        $uid        = trim($request->query('uid'));
        $campania  = $request->query('campania');
        $expires   = $request->query('expires');
        $signature = $request->query('signature');

        // 1. Verificar si la firma y expiración existen
        if (!$signature || !$expires) {
            abort(403, 'Acceso Restringido: Debe ingresar únicamente a través del marcador.');
        }

        // 2. Verificar que el enlace no haya expirado
        if (time() > (int)$expires) {
            abort(401, 'El enlace de la llamada ha expirado. Genere una nueva consulta.');
        }

        // 3. Reconstruir la firma con los parámetros recibidos
        $dataToSign = "{$codDeu}|{$telf}|{$uid}|{$campania}|{$expires}";
        $expectedSignature = hash_hmac('sha256', $dataToSign, $secret);

        // 4. Verificar que la firma calculada coincida con la firma recibida
        if (!hash_equals($expectedSignature, $signature)) {
            abort(403, 'Acceso Denegado: La URL ha sido alterada o manipulada sin autorización.');
        }

        return $next($request);
    }
}
