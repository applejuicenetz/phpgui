<?php

use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\UI\Language;

//Templatedaten lesen
$template= new template();

//Language
$lang = Language::getLanguage();

if( empty( $_GET['site'] ) ) $_GET['site'] = "Dashboard";
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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="AppleJuice WebUI - Written in PHP, HTML and Javascript">
    <meta name="author" content="KDDK22">
    <meta name="robots" content="noindex, nofollow">
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
    <!-- manifest -->
    <link rel="manifest" href="manifest.json">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="application-name" content="AppleJuice WebUI">
    <meta name="apple-mobile-web-app-title" content="AppleJuice WebUI">
    <meta name="msapplication-navbutton-color" content="#aa0000">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="msapplication-starturl" content="/">
    <meta name="theme-color" content="#ffffff">
    <!-- Vendors styles-->
    <link rel="stylesheet" href="<?php echo WEBUI_THEME; ?>vendors/simplebar/css/simplebar.css">
    <link rel="stylesheet" href="<?php echo WEBUI_THEME; ?>css/vendors/simplebar.css">
    <!-- Font Awesome Css -->
    <link href="<?php echo WEBUI_THEME; ?>assets/icons/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <!-- Main styles for this application-->
    <link href="<?php echo WEBUI_THEME; ?>css/style.css" rel="stylesheet">
    <style>
        @keyframes blinken {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        .blink-text {
            animation: blinken 1ms linear infinite;
        }
    </style>
    <!-- We use those styles to show code examples, you should remove them in your application.-->
    <link href="<?php echo WEBUI_THEME; ?>css/examples.css" rel="stylesheet">
    <script src="<?php echo WEBUI_THEME; ?>js/config.js"></script>
    <script src="<?php echo WEBUI_THEME; ?>js/color-modes.js"></script>
    <?php template::js_file($_GET['site']); ?>
  </head>
  <body>