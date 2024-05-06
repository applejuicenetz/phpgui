<?php

use appleJuiceNETZ\GUI\Router;

const GUI_ROOT = __DIR__;

require_once GUI_ROOT . '/bootstrap.php';

$main = new Router();

$main->handle();
