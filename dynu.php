<?php
// Datos de autenticación
$username = 'maestrolinux';
$password = 'qwopaskl1';

// URL de la solicitud
$url = 'http://api.dynu.com/nic/update?username=' . $username . '&password=' . $password;

// Inicializar cURL
$ch = curl_init($url);

// Establecer opción para resolver solo IPv6
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V6);

// Establecer opciones de cURL
//curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Realizar la solicitud
$response = curl_exec($ch);

//echo $response;

// Cerrar la conexión cURL
curl_close($ch);
?>
