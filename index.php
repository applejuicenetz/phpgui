<?php
session_start();

include("_classes/env.php");
include("_classes/core.php");
include("_classes/lang.php");
include("_classes/login.php");
include("_classes/template.php");
include("_classes/subs.php");


$core = new core();
$login = new login();
$template = new template();
$subs = new subs();

$language = new language($_ENV['GUI_LANGUAGE']);
$lang = $language->translate();

$login->check_login();


?>
