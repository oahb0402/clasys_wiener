<?php
// Clave secreta compartida (debe ser idéntica a la del .env de Laravel)
$secret = 'CET+NE3BbVrn+kTyqMv0rTWtZCab6S1CNoyGn0XN5GM=';

// Capturamos todos los parámetros query string enviados por el marcador
$cod_deu          = $_GET['cod_deu'] ?? '';
$telf             = $_GET['telf'] ?? '';
$uid              = $_GET['uid'] ?? '';
$campania         = $_GET['campania'] ?? '';
$idllamada        = $_GET['idllamada'] ?? '';
$extension        = $_GET['extension'] ?? '';
$accion_predictivo= $_GET['accion_predictivo'] ?? '';


// Definimos la expiración del enlace (60 segundos desde este momento)
$expires = time() + 60;

// Concatenamos las variables críticas que deseamos proteger contra manipulaciones
$dataToSign = "{$cod_deu}|{$telf}|{$uid}|{$campania}|{$expires}";

// Generamos el Hash HMAC SHA256
$signature = hash_hmac('sha256', $dataToSign, $secret);

// Reconstruimos el Query String añadiendo $expires y $signature
$params = $_GET;
$params['expires']   = $expires;
$params['signature'] = $signature;

$queryString = http_build_query($params);

// Redirigimos al CRM en Laravel con la URL firmada
header("Location: https://upcu.clasperu.com/crm/gestion?" . $queryString);
exit();
