<?php
header('Cache-Control: no-cache');
header('Content-type: image/png');

$schriftgroesse = 5;
$breite = 100;
$hoehe = 12;

$r_fg = hexdec(substr($_GET['fg'], 1, 2));
$g_fg = hexdec(substr($_GET['fg'], 3, 2));
$b_fg = hexdec(substr($_GET['fg'], 5, 2));
$r_bg = hexdec(substr($_GET['bg'], 1, 2));
$g_bg = hexdec(substr($_GET['bg'], 3, 2));
$b_bg = hexdec(substr($_GET['bg'], 5, 2));

$text = $_GET['ready'] . " (" . $_GET['progress'] . "%)";

$image = imagecreate($breite, $hoehe);
$hintergrund = imagecolorallocate($image, $r_bg, $g_bg, $b_bg);
$fertig = imagecolorallocate($image, $r_fg, $g_fg, $b_fg);

$image2 = imagecreate($breite, $hoehe);
$hintergrund2 = imagecolorallocate($image2, $r_fg, $g_fg, $b_fg);
$fertig2 = imagecolorallocate($image2, $r_bg, $g_bg, $b_bg);

//breite pruefen, damit text zentriert werden kann
$getbreite = imagefontwidth($schriftgroesse) * strlen($text);

//hoehe pruefen
$gethoehe = imagefontheight($schriftgroesse);

//sicher stellen, dass text passt
while (($gethoehe > $hoehe || $getbreite > $breite) && $schriftgroesse > 1) {
    $schriftgroesse--;
    $getbreite = imagefontwidth($schriftgroesse) * strlen($text);
    $gethoehe = imagefontheight($schriftgroesse);
}

$oben = 0.5 * $hoehe - 0.5 * $gethoehe;
$links = 0.5 * $breite - 0.5 * $getbreite;

imagestring($image, $schriftgroesse, $links, $oben, $text, $fertig);
imagestring($image2, $schriftgroesse, $links, $oben, $text, $fertig2);

imagecopy($image, $image2, 0, 0, 0, 0, floor($_GET['progress'] + 0.5), $hoehe);

imagepng($image);

imagedestroy($image);
imagedestroy($image2);
