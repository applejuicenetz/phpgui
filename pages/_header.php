<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\GUI;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\GUI\Plugins;
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
$language = Kernel::getLanguage();
$lang = $language->translate();

if( empty( $_GET['site'] ) ) $_GET['site'] = "start";   

?>
<!DOCTYPE html><!--
* CoreUI - Free Bootstrap Admin Template
* @version v5.0.0
* @link https://coreui.io/product/free-bootstrap-admin-template/
* Copyright (c) 2024 creativeLabs ukasz Holeczek
* Licensed under MIT (https://github.com/coreui/coreui-free-bootstrap-admin-template/blob/main/LICENSE)
-->
<html lang="de">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>appleJuice - phpGUI</title>
    <link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Vendors styles-->
    <link rel="stylesheet" href="themes/CoreUI/vendors/simplebar/css/simplebar.css">
    <link rel="stylesheet" href="themes/CoreUI/css/vendors/simplebar.css">
	<!-- Font Awesome Css -->
    <link href="themes/CoreUI/assets/icons/font-awesome/css/font-awesome.min.css" rel="stylesheet" />

    <!-- Main styles for this application-->
    <link href="themes/CoreUI/css/style.css" rel="stylesheet">
    <script src="themes/CoreUI/js/config.js"></script>
    <script src="themes/CoreUI/js/color-modes.js"></script>
    <?php template::js_file($_GET['site']); ?>
  </head>
  <body>
    <div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
      <div class="sidebar-header border-bottom">
        <div class="sidebar-brand">
        	<div class="sidebar-brand-full">
<<<<<<< Updated upstream
    		<img class="sidebar-brand-full" src="themes/CoreUI/assets/brand/applejuice.svg" width="176" height="64">
=======
    		<img class="sidebar-brand-full" src="themes/CoreUI/assets/brand/applejuice.svg" width="250" height="90">
>>>>>>> Stashed changes
    		<br>
    		<span class="sidebar-brand-full">Flux</span>
    		</div>
    		<img class="sidebar-brand-narrow" src="themes/CoreUI/assets/brand/Apple_Icon.svg" width="32" height="32">
    		<br>
    		<span class="sidebar-brand-narrow">Flux</span>
        </div>
        <button class="btn-close d-lg-none" type="button" data-coreui-dismiss="offcanvas" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector(&quot;#sidebar&quot;)).toggle()"></button>
      </div>
      <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item">
          <a class="nav-link<?php template::active("start"); ?>" href="index.php?site=start">
            <svg class="nav-icon">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-speedometer"></use>
            </svg> <?php echo $lang->Navigation->dashboard; ?>
           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link<?php template::active("downloads"); ?>" href="index.php?site=downloads">
            <svg class="nav-icon">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-cloud-download"></use>
            </svg> <?php echo $lang->Navigation->downloads; ?>
           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link<?php template::active("uploads"); ?>" href="index.php?site=uploads">
            <svg class="nav-icon">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-cloud-upload"></use>
            </svg> <?php echo $lang->Navigation->uploads; template::uploads(); ?>
           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link<?php template::active("search"); ?>" href="index.php?site=search">
            <svg class="nav-icon">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-search"></use>
            </svg> <?php echo $lang->Navigation->search; ?>
           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link<?php template::active("shares"); ?>" href="index.php?site=shares">
            <svg class="nav-icon">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-share-alt"></use>
            </svg> <?php echo $lang->Navigation->shares; ?>
           </a>
         </li>
         <li class="nav-item">
          <a class="nav-link <?php template::active("server"); ?>" href="index.php?site=server">
            <svg class="nav-icon">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-storage"></use>
            </svg> <?php echo $lang->Navigation->server_list; ?>
           </a>
         </li>
         <li class="nav-item active">
          <a class="nav-link<?php template::active("settings"); ?>" href="index.php?site=settings">
            <svg class="nav-icon">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
            </svg> <?php echo $lang->Navigation->settings; ?>
           </a>
         </li>
        <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-puzzle"></use>
            </svg> <?php echo $lang->Navigation->addons; ?></a>
          <ul class="nav-group-items compact">
         <?php $Plugin = new Plugins();
		$Plugin->Find_Plugins();
		
		foreach($Plugin->liste as $a)
		{
            echo '<li class="nav-item">
            		<a class="nav-link" href="index.php?site=extras&show=' . $a[2] . '">
            		<span class="nav-icon"><span class="nav-icon-bullet"></span></span> 
            		' . $a[0] . '</a></li>
            ';
        }
 ?>
         
          </ul>
        </li>
        <li class="nav-item active">
          <a class="nav-link<?php template::active("help"); ?>" href="index.php?site=help">
            <svg class="nav-icon">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-info"></use>
            </svg> <?php echo $lang->Navigation->help; ?>
           </a>
         </li>
      </ul>
      <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
      </div>
    </div>
    <div class="wrapper d-flex flex-column min-vh-100">
      <header class="header header-sticky p-0 mb-4">
        <div class="container-fluid border-bottom px-4">
          <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -14px;">
            <svg class="icon icon-lg">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
            </svg>
          </button>
          <ul class="header-nav ms-auto">
            <li class="nav-item"><a class="nav-link" onClick="window.location.reload()">
                <svg class="icon icon-lg">
                  <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-reload"></use>
                </svg></a></li>
            <li class="nav-item"><a class="nav-link" href="" data-coreui-toggle="modal" data-coreui-target="#search">
                <svg class="icon icon-lg">
                  <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-search"></use>
                </svg></a></li>
           </ul>
          <ul class="header-nav">
            <li class="nav-item py-1">
              <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
           </li>
            <li class="nav-item dropdown">
              <button class="btn btn-link nav-link py-2 px-2 d-flex align-items-center" type="button" aria-expanded="false" data-coreui-toggle="dropdown">
                <svg class="icon icon-lg theme-icon-active">
                  <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-contrast"></use>
                </svg>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" style="--cui-dropdown-min-width: 8rem;">
                <li>
                  <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="light">
                    <svg class="icon icon-lg me-3">
                      <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-sun"></use>
                    </svg>Light
                  </button>
                </li>
                <li>
                  <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="dark">
                    <svg class="icon icon-lg me-3">
                      <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-moon"></use>
                    </svg>Dark
                  </button>
                </li>
                <li>
                  <button class="dropdown-item d-flex align-items-center active" type="button" data-coreui-theme-value="auto">
                    <svg class="icon icon-lg me-3">
                      <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-contrast"></use>
                    </svg>Auto
                  </button>
                </li>
              </ul>
            </li>
            <li class="nav-item py-1">
              <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>
            <li class="nav-item dropdown"><a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <div class="avatar avatar-md"><img class="avatar-img" src="themes/CoreUI/assets/img/avatars/3.jpg"></div>
              </a>
              <div class="dropdown-menu dropdown-menu-end pt-0">
                <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2"><?php echo $settings_xml['NICK']['VALUES']['CDATA']; ?></div>
                <a href="index.php?site=logout" class="dropdown-item">Logout</a>  <a class="dropdown-item" href="#">
                <a class="dropdown-item"  data-coreui-toggle="modal" data-coreui-target="#coreexit"><?php echo $lang->Navigation->kick_core; ?></a>
                    
                   </li>
                   </ul>
        </div>
        <div class="container-fluid px-4">
        <?php template::bread($_GET['site'], subs::get_title($_GET['site'])); ?>
        </div>
      </header>
      <div class="body flex-grow-1">
        <div class="container-lg px-4">
