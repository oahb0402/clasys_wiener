<?php
// Clave secreta compartida (debe ser idéntica a la del .env de Laravel)
$secret = 'CET+NE3BbVrn+kTyqMv0rTWtZCab6S1CNoyGn0XN5GM=';

// Capturamos todos los parámetros query string enviados por el marcador
$cod_deu          = trim($_GET['cod_deu'] ?? '');
$telf             = trim($_GET['telf'] ?? '');
$uid              = trim($_GET['uid'] ?? '');
$idllamada        = $_GET['idllamada'] ?? '';
$extension        = $_GET['extension'] ?? '';
$accion_predictivo= $_GET['accion_predictivo'] ?? '';


// Definimos la expiración del enlace (60 segundos desde este momento)
$expires = time() + 10060;

// Concatenamos las variables críticas que deseamos proteger contra manipulaciones
$dataToSign = "{$cod_deu}|{$telf}|{$uid}|{$expires}";

// Generamos el Hash HMAC SHA256
$signature = hash_hmac('sha256', $dataToSign, $secret);

// Reconstruimos el Query String añadiendo $expires y $signature
$params = $_GET;
$params['expires']   = $expires;
$params['signature'] = $signature;

$queryString = http_build_query($params);

// Redirigimos al CRM en Laravel con la URL firmada
#header("Location: https://upcu.clasperu.com/crm/gestion?" . $queryString);
header("Location: http://10.48.0.16:8000/crm/gestion?" . $queryString);
exit();
