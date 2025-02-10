<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Downloads;

define("GUI_ROOT", dirname(__DIR__));

require_once GUI_ROOT . '/bootstrap.php';

$core = new Core();

Header("Cache-Control: no-cache");
Header("Content-type: image/png");

$partliste = null;

if (!empty($_GET['dl_id'])) {
    $partliste = $core->command("xml", "downloadpartlist.xml?id=" . $_GET['dl_id']);
} elseif (!empty($_GET['usr_id'])) {
    $partliste = $core->command("xml", "userpartlist.xml?id=" . $_GET['usr_id']);
} else {
    // Fehler: Weder dl_id noch usr_id angegeben
    die("Fehler: Keine gültige ID angegeben");
}

if (!$partliste) {
    die("Fehler: Keine Daten gefunden");
}

$filesize = array_keys($partliste['FILEINFORMATION']);
$filesize = $filesize[0];

$zeilen = 14;
$breite = 500;
$zeilenhoehe = 14;
$hoehe = $zeilen * $zeilenhoehe;

$pixelsize = ($zeilen * $breite) / $filesize;

$image = imagecreate($zeilen * $breite, $zeilenhoehe);

// Farben definieren
$rot = imagecolorallocate($image, 255, 0, 0);
$fertig = imagecolorallocate($image, 0, 0, 0);
$ok = imagecolorallocate($image, 0, 255, 0);

// Blau- und Gelbtöne für die Darstellung
$blau = array();
$gelb = array();
for ($h = 1; $h <= 10; $h++) {
    $blau[$h] = imagecolorallocate($image, 250 - (25 * $h), 250 - (25 * $h), 255);
    $gelb[$h] = imagecolorallocate($image, 255 - (12 * $h), 255 - (12 * $h), 0);
}

$obenlinks_y = 0;
$untenrechts_y = $zeilenhoehe;
$fertig_seit = -1;
$nextpart = array_keys($partliste['PART']);
$x = 0;
$c = 0;

foreach (array_keys($partliste['PART']) as $a) {
    $c++;
    $obenlinks_x = ($pixelsize * $a);

    if ($a != $filesize && !empty($nextpart[$c])) {
        $untenrechts_x = $obenlinks_x + ($pixelsize * ($nextpart[$c] - $a));
    } else {
        $untenrechts_x = $zeilen * $breite;
    }

    $farbe = getPartColor($partliste, $a, $fertig_seit);
    
    imagefilledrectangle($image, $obenlinks_x, $obenlinks_y, $untenrechts_x, $untenrechts_y, $farbe);
}

// Hilfsfunktion zur Farbbestimmung der Teile
function getPartColor($partliste, $a, &$fertig_seit) {
    switch ($partliste['PART'][$a]['TYPE']) {
        case "-1":
            if ($fertig_seit == -1) $fertig_seit = $a;
            return imagecolorallocate($image, 0, 0, 0); // Schwarz
        case "0":
            return imagecolorallocate($image, 255, 0, 0); // Rot
        default:
            $anzahl = $partliste['PART'][$a]['TYPE'];
            return imagecolorallocate($image, 0, 255, 0); // Grün (default)
    }
}

// Bild ausgeben
imagepng($image);
imagedestroy($image);
