<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$language = Kernel::getLanguage();
$lang = $language->translate();

//Classes abrufen
$Servers = new Server();
$core = new Core();
$icon_img = new Icons();
$subs = new subs();
$template = new template();
$Uploadlist = new Uploads();
$Sharelist = new Share();

$subs::refresh_cache();

$modified = $core->command("xml", "modified.xml?filter=informations");
$temp = array_keys($modified['INFORMATION']);
$information =& $modified['INFORMATION'][$temp[0]];

if ($information['CREDITS'] <= 0) {
    $creditcolor = " class='text-danger'";
} else {
    $creditcolor = " class='ext-success'";
}

echo "<span" . $creditcolor . " >" . subs::sizeformat($information['CREDITS']) . "</span>";
