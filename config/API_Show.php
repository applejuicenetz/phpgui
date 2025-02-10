<?php

use appleJuiceNETZ\API;

// Überprüfen, ob der Autoloader korrekt eingebunden ist
if (class_exists('appleJuiceNETZ\API')) {
    echo "Uploads-Klasse erfolgreich geladen.";
} else {
    echo "Uploads-Klasse nicht gefunden.";
}

if($_GET['api'] == "UploadBadge")
{
    echo API::UploadBadge();
}

