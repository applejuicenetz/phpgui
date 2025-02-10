<?php

use appleJuiceNETZ\Kernel;

// Fehleranzeige aktivieren (nur in der Entwicklungsumgebung)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Autoloader laden (z.B. Composer Autoloader)
if (file_exists(GUI_ROOT . 'vendor/autoload.php')) {
    require_once GUI_ROOT . 'vendor/autoload.php';
} else {
    spl_autoload_register(function ($class): void {
        require_once GUI_ROOT . 'src/Controller/' . str_replace(['appleJuiceWebUI\\', '\\'], ['', '/'], $class . '.php');
    });
}
// Die Konfigurationsdateien laden
require_once GUI_ROOT . '/config/config.php';       // Weitere allgemeine Konfigurationen

//Prüfe auf die Richtige PHP Version
if (!version_compare(PHP_VERSION, '8.2')) {
    die('PHP 8.2 required, used: -> ' . PHP_VERSION);
}

Kernel::init();

