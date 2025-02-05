<?php

declare(strict_types=1);

use appleJuiceNETZ\Kernel;

const PHP_GUI_VERSION = '0.29.8'; // only semver, without BETA or something like that

// prepare for composer
if (file_exists(GUI_ROOT . '/vendor/autoload.php')) {
    require_once GUI_ROOT . '/vendor/autoload.php';
} else {
    spl_autoload_register(function ($class): void {
        require_once GUI_ROOT . '/src/' . str_replace(['appleJuiceNETZ\\', '\\'], ['', '/'], $class . '.php');
    });
}

Kernel::init();
