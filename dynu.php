<?php
// Datos de autenticaci�n
$username = 'maestrolinux';
$password = 'qwopaskl1';

// URL de la solicitud
$url = 'http://api.dynu.com/nic/update?username=' . $username . '&password=' . $password;

// Inicializar cURL
$ch = curl_init($url);

// Establecer opci�n para resolver solo IPv6
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V6);

// Establecer opciones de cURL
//curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Realizar la solicitud
$response = curl_exec($ch);

//echo $response;

// Cerrar la conexi�n cURL
curl_close($ch);
?>
