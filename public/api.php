<?php

declare(strict_types=1);

const GUI_ROOT = __DIR__ . '/..';
require_once GUI_ROOT . '/bootstrap.php';
(new \appleJuiceNETZ\Api\Router())->handle();
