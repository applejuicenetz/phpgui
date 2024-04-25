<?php

use appleJuiceNETZ\GUI\Kernel;

define('PHP_GUI_VERSION', '0.29.1'); // only semver, without BETA or something like that

# preparment for composer
if (file_exists(GUI_ROOT . '/vendor/autoload.php')) {
    require_once GUI_ROOT . '/vendor/autoload.php';
} else {
    spl_autoload_register(function ($class) {
        require_once GUI_ROOT . '/src/' . str_replace('\\', '/', $class . '.php');;
    });
}

Kernel::init();

