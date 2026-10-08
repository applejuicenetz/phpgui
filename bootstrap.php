<?php

declare(strict_types=1);

use appleJuiceNETZ\Kernel;

const PHP_GUI_VERSION = '0.33.0';


// prepare for composer
if (file_exists(GUI_ROOT . '/vendor/autoload.php')) {
    require_once GUI_ROOT . '/vendor/autoload.php';
} else {
    spl_autoload_register(function ($class): void {
        $file = GUI_ROOT . '/src/' . str_replace(['appleJuiceNETZ\\', '\\'], ['', '/'], $class . '.php');
        if (is_file($file)) {
            require_once $file;
        }
    });
}

Kernel::init();
