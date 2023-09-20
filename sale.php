<?php
/*
 * CREADO POR LU9DCE
 * Copyright 2023 Eduardo Castillo
 * Contacto: castilloeduardo@outlook.com.ar
 * Licencia: Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International
 */
$ds = DIRECTORY_SEPARATOR;
$boletines_dir = realpath(dirname(__DIR__) . $ds . 'boletines');
$procesa_dir = realpath(dirname(__DIR__) . $ds . 'boletines' . $ds . 'procesa');
if (!file_exists($boletines_dir . $ds . 'mail.in')) {
    $files = scandir($procesa_dir);
    if (count($files) > 2) {
        rename($procesa_dir . $ds . $files[2], $boletines_dir . $ds . 'mail.in');
    }
}
