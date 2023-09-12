<?php

$pingresult = exec("ping -4 -n -c 1 8.8.8.8", $outcome, $status);
if (0 != $status) {
    exit();
}

// ---------------------------------------------------------------------------

ini_set('language', 'en');
$ruta = __DIR__;
$ds = DIRECTORY_SEPARATOR;
date_default_timezone_set('UTC');
$offset = -3; 
$fecha_actual_utc = time();
$fecha = gmdate("d-My", $fecha_actual_utc + $offset * 60 * 60);

// ---------------------------------------------------------------------------

$kolp = $ruta . $ds . 'procesa' . $ds . '*.*';
foreach (glob($kolp) as $v) {
    unlink($v);
}

// ---------------------------------------------------------------------------

$a = "";
$b = "";
$w = "";
$rss = "";
$tmp = $ruta . $ds . 'tmp.txt';

// ---------------------------------------------------------------------------

function down($url) {
    $ruta = $GLOBALS['ruta'];
    $ds = $GLOBALS['ds'];
    $tmp = $GLOBALS['tmp'];
    $fh = fopen($tmp, "w");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    $crt = realpath($ruta . $ds . 'curl-ca-bundle.crt');
    curl_setopt($ch, CURLOPT_CAINFO, $crt);
    curl_setopt($ch, CURLOPT_FILE, $fh);
    curl_exec($ch);
    curl_close($ch);
}

// ---------------------------------------------------------------------------


function limpia($string)
{
    $string = html_entity_decode($string);
	$string = mb_convert_encoding($string, "HTML-ENTITIES", "UTF-8");
    $string = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|cedil);/', '$1', $string);
    $string = str_replace(array('ñ', 'Ñ'), '#', $string);
	$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    $string = strip_tags($string);
    $string = preg_replace('/\t+/', '', $string);
    $string = preg_replace('/(\n){3,}/', "\n\n", $string);
    $string = preg_replace('/(\n){2,}/', "\n\n", $string);
	$string = html_entity_decode($string);
    $string = wordwrap($string, 78, "\n");
    return $string;
}

// ---------------------------------------------------------------------------

function rss($rss)
{
    $w = "";
    foreach ($rss->channel->item as $item) {
        $title = $item->title;
        $description = $item->description;
        $w .= $title . "\n\n";
        $w .= $description . "\n\n";
        $w .= "=#=#=#=#=#=#=#=#=\n\n";
    }
    $b = limpia($w);
    return $b;
}

// ---------------------------------------------------------------------------

function boletin($a, $b, $eee)
{
    $ruta = $GLOBALS['ruta'];
    $ds = $GLOBALS['ds'];
    $tmp = $GLOBALS['tmp'];
    $opop = "$a
            __    _  _  ___  ____   ___  ____    ____  ____  ____
           (  )  / )( \/ _ \(    \ / __)(  __)  (  _ \(  _ \/ ___)
           / (_/\) \/ (\__  )) D (( (__  ) _)    ) _ ( ) _ (\___ \
           \____/\____/(___/(____/ \___)(____)  (____/(____/(____/
           
                PACKET RADIO STATION - BUENOS AIRES (GF05OM)
                       PHP SCHEDULED NEWSLETTERS (PSN)
                     COPYRIGHT 2023 - EDUARDO A. CASTILLO
+----------------------------------------------------------------------------+

$b
+----------------------------------------------------------------------------+
          HYPERTEXT PREPROCESSOR - DEVELOPED BY LU9DCE - VERSION 3
/EX
";
    $opop = strtoupper($opop);
    file_put_contents($ruta . $ds . 'procesa' . $ds . $eee . ".txt", $opop);
    unlink ($tmp);
}

// ---------------------------------------------------------------------------

$pro = $ruta . $ds . "manu";
$arrFiles = scandir($pro);
if (isset($arrFiles[2])) {
    $boli = pathinfo($arrFiles[2], PATHINFO_FILENAME);
    $a = "SB TODOS @ LATNET < LU9DCE\n$boli $fecha";
    rename($pro . $ds . $arrFiles[2], $tmp);
    $b = file_get_contents($tmp);
    $b .= "
        
##############################################
# Edicion especial - Boletin es hecho a mano #
##############################################
        
";
    $b = limpia($b);
    boletin($a, $b, "0");
}

// ---------------------------------------------------------------------------

$a = "SB EQUAKE @ WW < LU9DCE\nEARTHQUAKE $fecha";
down("https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/4.5_day.csv");
$f = fopen($tmp, 'r');
for ($z = 0; $z < 10; $z ++) {
    $x = fgetcsv($f);
    if (is_array($x)) {
        if (is_numeric($x[4])) {
            $num_flotante = floatval($x[4]);
            $num_cadena = number_format($num_flotante, 1, '.', '');
            $fechax = $x[0];
            $fecha_formateada = date("Y-m-d", strtotime(substr($fechax, 0, 10)));
            $hora_formateada = date("H:i", strtotime(substr($fechax, 11, 5)));
            $fecnot = $fecha_formateada . " " . $hora_formateada;
        } else {
            $num_cadena = $x[4];
            $fecnot = $x[0];
        }
        $b .= "$fecnot - $num_cadena - $x[13]\n";
    }
}

fclose($f);
$qq = "time - mag - place";
$ww = "       time        mag                  place\n";
$ww .= "-----------------------------------------------------------------------------";
$b = str_replace($qq, $ww, $b);
boletin($a, $b, "1");

// ---------------------------------------------------------------------------

$a = "SB LINUX @ WW < LU9DCE\nLINUX TODAY $fecha";
down("http://feeds.feedburner.com/linuxtoday/linux");
$rss = simplexml_load_file($tmp);
$b = rss($rss);
boletin($a, $b, "2");

// ---------------------------------------------------------------------------

$a = "SB NEWS @ LATNET < LU9DCE\nBBC NOTICIAS $fecha";
down("http://feeds.bbci.co.uk/mundo/rss.xml");
$rss = simplexml_load_file($tmp);
$b = rss($rss);
boletin($a, $b, "3");

// ---------------------------------------------------------------------------

$a = "SB ALERT @ WW < LU9DCE\nSTORM PREDICTION CENTER $fecha";
down("https://www.spc.noaa.gov/products/spcrss.xml");
$rss = simplexml_load_file($tmp);
$b = rss($rss);
boletin($a, $b, "4");

// ---------------------------------------------------------------------------

$a = "SB TODOS @ LATNET < LU9DCE\nUN DIA COMO HOY $fecha";
down("https://www.hoyenlahistoria.com/efemerides.php");
$aa = file_get_contents($tmp);
$array = explode("\n", $aa);
$c = count($array);
$fl_array = preg_grep("/a href=\"\/efemerides\/fecha/", $array);
$new = array_values(array_unique($fl_array));
$b = implode("\n\n", $new);
$b = limpia($b);
boletin($a, $b, "5");

// ---------------------------------------------------------------------------

$a = "SB DX @ WW < LU9DCE\nDX OPERATION $fecha";
down("https://www.ng3k.com/Misc/adxoplain.html");
$srt = file_get_contents($tmp);
$array = explode("\n", $srt);
$c = count($array);
for ($y = 0; $y < 21; $y ++) {
    unset($array[$y]);
}
$new = array_values(array_unique($array));
$c = count($new);
$v = $c - 28;
for ($y = $v; $y < $c; $y ++) {
    unset($new[$y]);
}
$str = implode("\n\n", $new);
$str = preg_replace('/<br>/', "\n", $str);
$b = limpia($str);
$b = str_replace("\n\n\n", "\n", $b);
boletin($a, $b, "6");

// ---------------------------------------------------------------------------

$a = "SB ALL @ WW < LU9DCE\nWTR SPACE INDICES $fecha";
down("https://spawx.nwra.com/spawx/env_latest.html");
$aa = file_get_contents($tmp);
$array = explode("PRE>", $aa);
$b = preg_replace('/<\//', "", $array[1]);
boletin($a, $b, "7");

// ---------------------------------------------------------------------------

$a = "SB DXNEWS @ WW < LU9DCE\nDX NEWS $fecha";
down("https://dxnews.com/rss.xml");
$rss = simplexml_load_file($tmp);
$b = rss($rss);
boletin($a, $b, "8");

// ---------------------------------------------------------------------------

$a = "SB HUMOR @ WW < LU9DCE\nFORTUNE $fecha";
down("https://api.justyy.workers.dev/api/fortune");
$b = file_get_contents ($tmp);
$replacements = array(
    '\\n' => "\n\r",
    '\\r' => "\n\r",
    '\\t' => "\t",
    '\\v' => "\v",
    '\\"' => "\"",
);
$b = str_replace(array_keys($replacements), array_values($replacements), $b);
$b = substr($b, 1, -1);
$b = limpia($b);
boletin($a, $b, "9");

// ---------------------------------------------------------------------------

$a = "SB HUMOR @ LUNET < LU9DCE\nCHISTE $fecha";
down("http://www.holasoyramon.com/chistes/aleatorio/");
$content = file_get_contents ($tmp);
$doc = new DOMDocument();
libxml_use_internal_errors(true);
$doc->loadHTML($content);
libxml_clear_errors();
    $divElements = $doc->getElementsByTagName('div');
    foreach ($divElements as $divElement) {
        if ($divElement->getAttribute('class') === 'grande1') {
            $divContent = $doc->saveHTML($divElement);
            $plaintext = strip_tags($divContent);
            $lines = explode("\n", $plaintext);
            $filteredLines = array_filter($lines, function($line) {
                return strpos($line, "Chiste Aleatorio") === false;
            });
            $filteredText = implode("\n", $filteredLines);
            $b = $filteredText;
            break;
        }
    }
$b = limpia($b);
boletin($a, $b, "10");

// ---------------------------------------------------------------------------

$a = "SB NEWS @ WW < LU9DCE\nNEW FLATHUB $fecha";
down("https://flathub.org/api/v2/feed/new");
$rss = simplexml_load_file($tmp);
$b = rss($rss);
boletin($a, $b, "11");

// ---------------------------------------------------------------------------

$a = "SB URE @ LATNET < LU9DCE\nHAM ESPA#A $fecha";
down("https://www.ure.es/feed/");
$rss = simplexml_load_file($tmp);
$b = rss($rss);
boletin($a, $b, "12");

// ---------------------------------------------------------------------------

$clarinq = "Ultimas Noticias,https://www.telam.com.ar/rss2/ultimasnoticias.xml,";
$clarinq .= "Politica,https://www.telam.com.ar/rss2/politica.xml,";
$clarinq .= "Economia,https://www.telam.com.ar/rss2/economia.xml,";
$clarinq .= "Sociedad,https://www.telam.com.ar/rss2/sociedad.xml,";
$clarinq .= "Deportes,https://www.telam.com.ar/rss2/deportes.xml,";
$clarinq .= "Policiales,https://www.telam.com.ar/rss2/policiales.xml,";
$clarinq .= "Internacional,https://www.telam.com.ar/rss2/internacional.xml,";
$clarinq .= "Latinoamerica,https://www.telam.com.ar/rss2/latinoamerica.xml,";
$clarinq .= "Conosur,https://www.telam.com.ar/rss2/conosur.xml,";
$clarinq .= "Provincias,https://www.telam.com.ar/rss2/provincias.xml,";
$clarinq .= "Agropecuario,https://www.telam.com.ar/rss2/agropecuario.xml,";
$clarinq .= "Tecnologia,https://www.telam.com.ar/rss2/tecnologia.xml,";
$clarinq .= "Cultura,https://www.telam.com.ar/rss2/cultura.xml,";
$clarinq .= "Espectaculos,https://www.telam.com.ar/rss2/espectaculos.xml,";
$clarinq .= "Turismo,https://www.telam.com.ar/rss2/turismo.xml,";
$clarinq .= "Salud,https://www.telam.com.ar/rss2/salud.xml,";
$clarinq .= "Educacion,https://www.telam.com.ar/rss2/educacion.xml,";
$clarinq .= "Redes,https://www.telam.com.ar/rss2/redes.xml";

$array = explode(",", $clarinq);

for ($ee = 0; $ee < 36; $ee += 2) {
    $er = $ee + 1;
    $a = "SB TELAM @ LUNET < LU9DCE\n$array[$ee] $fecha";
    down($array[$er]);
    $rss = simplexml_load_file($tmp);
    $b = rss($rss);
    boletin($a, $b, $array[$ee]);
}

// ---------------------------------------------------------------------------

