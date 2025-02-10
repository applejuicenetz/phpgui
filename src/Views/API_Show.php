<?php

use appleJuiceNETZ\GUI\subs;

// Überprüfen, ob der Autoloader korrekt eingebunden ist
if (class_exists('appleJuiceNETZ\GUI\subs')) {
    echo "Uploads-Klasse erfolgreich geladen.";
} else {
    echo "Uploads-Klasse nicht gefunden.";
}

if($_GET['api'] == "UploadBadge")
{
    echo subs::UploadBadge();
}

