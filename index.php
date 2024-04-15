<?php
session_start();

include("_classes/env.php");
include("_classes/core.php");
include("_classes/lang.php");
include("_classes/login.php");
include("_classes/template.php");

$core = new core();
$lang = new lang();
$login = new login();
$template = new template();

$lang->ermitteln();
$login->check_login();

?>
