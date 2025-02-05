<?php

use appleJuiceNETZ\UI\Build;

const GUI_ROOT = __DIR__."/";

require_once GUI_ROOT.'src/bootstrap.php';

session_start();

header('Access-Control-Allow-Origin: *');

$Build = new Build();

$page = $_GET['site'];

$Build->AppLoaded($page);