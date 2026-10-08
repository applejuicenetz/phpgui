<?php

declare(strict_types=1);

use appleJuiceNETZ\GUI\Router;

const GUI_ROOT = __DIR__ . '/..';

require_once GUI_ROOT . '/bootstrap.php';

header('Access-Control-Allow-Origin: *');

if (isset($_GET['l'])) {
    header('Referrer-Policy: no-referrer');
    header('Cache-Control: no-store');
}

(new Router())->handle();
