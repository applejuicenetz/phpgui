<?php

use appleJuiceNETZ\UI\Build;

const GUI_ROOT = __DIR__."/";

require_once GUI_ROOT.'src/bootstrap.php';

session_start();

header('Access-Control-Allow-Origin: *');

$Build = new Build();

if( empty( $_GET['site'] ) ) $_GET['site'] = "Dashboard";

$Build->AppLoaded($_GET['site']);