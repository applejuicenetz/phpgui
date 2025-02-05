<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\GUI;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\UI\Language;
use appleJuiceNETZ\Kernel;

$gui = new GUI();
$gui::refresh();

//Templatedaten lesen
$template= new template();
$subs = new subs();
//Core Settings auslesen
$core = new Core();
$settings_xml=$core->command("xml","settings.xml");

//Language
$lang = Language::getLanguage();


if( empty( $_GET['site'] ) ) $_GET['site'] = "start";  

?>
<!DOCTYPE html><!--
* CoreUI - Free Bootstrap Admin Template
* @version v5.1.1
* @link https://coreui.io/product/free-bootstrap-admin-template/
* Copyright (c) 2024 creativeLabs Łukasz Holeczek
* Licensed under MIT (https://github.com/coreui/coreui-free-bootstrap-admin-template/blob/main/LICENSE)
-->
<html lang="de">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
    <title><?php echo WEBUI_TITLE; ?></title>
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo WEBUI_THEME; ?>assets/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo WEBUI_THEME; ?>assets/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo WEBUI_THEME; ?>assets/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo WEBUI_THEME; ?>assets/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo WEBUI_THEME; ?>assets/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo WEBUI_THEME; ?>assets/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo WEBUI_THEME; ?>assets/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo WEBUI_THEME; ?>assets/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo WEBUI_THEME; ?>assets/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo WEBUI_THEME; ?>assets/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo WEBUI_THEME; ?>assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo WEBUI_THEME; ?>assets/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo WEBUI_THEME; ?>assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo WEBUI_THEME; ?>assets/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?php echo WEBUI_THEME; ?>assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Vendors styles-->
    <link rel="stylesheet" href="<?php echo WEBUI_THEME; ?>vendors/simplebar/css/simplebar.css">
    <link rel="stylesheet" href="<?php echo WEBUI_THEME; ?>css/vendors/simplebar.css">
    <!-- Font Awesome Css -->
    <link href="<?php echo WEBUI_THEME; ?>assets/icons/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <!-- Main styles for this application-->
    <link href="<?php echo WEBUI_THEME; ?>css/style.css" rel="stylesheet">
    <!-- We use those styles to show code examples, you should remove them in your application.-->
    <link href="<?php echo WEBUI_THEME; ?>css/examples.css" rel="stylesheet">
    <script src="<?php echo WEBUI_THEME; ?>js/config.js"></script>
    <script src="<?php echo WEBUI_THEME; ?>js/color-modes.js"></script>
    <?php template::js_file($_GET['site']); ?>
  </head>
  <body>