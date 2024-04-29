<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\GUI;
use appleJuiceNETZ\Kernel;

//PLugins auslesen
require_once GUI_ROOT . "/plugins/register.php";

$Plugin =new Plugin();
$Plugin->Find_Plugins();
$gui = new GUI();
$settings_gui = $gui->getDeviceConfig();

//Core Settings auslesen
$core = new Core();
$settings_xml=$core->command("xml","settings.xml");

//Language
$language = Kernel::getLanguage();
$lang = $language->translate();

function active($a){
    if($a == $_GET["site"]){
        $action = "class='active' ";
        echo $action;
    }else{

    }
}
function activee($a){
    if($a != $_GET["site"]){
        $action = "collapse";
        echo $action;
    }
}
if (isset($_GET['site'])) {
    $tab = ucfirst($_GET['site']);
    $active ='';
}else{
    $active ='collapsed';
}

?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>appleJuice - phpGUI</title>
    <!-- Favicon -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="themes/BsbAdmin/assets/plugins/bootstrap/dist/css/bootstrap.css" rel="stylesheet" />

    <!-- Animate.css Css -->
    <link href="themes/BsbAdmin/assets/plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Font Awesome Css -->
    <link href="themes/BsbAdmin/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />

    <!-- iCheck Css -->
    <link href="themes/BsbAdmin/assets/plugins/iCheck/skins/minimal/_all.css" rel="stylesheet" />
    <link href="themes/BsbAdmin/assets/plugins/iCheck/skins/square/_all.css" rel="stylesheet" />
    <link href="themes/BsbAdmin/assets/plugins/iCheck/skins/flat/_all.css" rel="stylesheet" />


    <!-- Switchery Css -->
    <link href="themes/BsbAdmin/assets/plugins/switchery/dist/switchery.css" rel="stylesheet" />

    <!-- Metis Menu Css -->
    <link href="themes/BsbAdmin/assets/plugins/metisMenu/dist/metisMenu.css" rel="stylesheet" />

    <!-- Jquery Datatables Css -->
    <link href="themes/BsbAdmin/assets/plugins/DataTables/media/css/dataTables.bootstrap.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="themes/BsbAdmin/assets/css/style.css" rel="stylesheet" />

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="themes/BsbAdmin/assets/css/themes/allthemes.css" rel="stylesheet" />
</head>

<body>
    <div class="theme-<?php echo $settings_gui->GUI->theme; ?> all-content-wrapper">
        <!-- Top Bar -->
        <header>
            <nav class="navbar navbar-default navbar-fixed">
                <!-- Search Bar -->
                <div class="search-bar">
                    <div class="search-icon">
                        <i class="material-icons">search</i>
                    </div>
                      <?php echo'
<form method="post" action="" name="linkform">
<input name="showlinkpage" type="hidden" value="1" />
<input name="'.session_name().'" type="hidden" value="'.session_id().'" />
        <input type="text" id="link"  name="ajfsp_link" placeholder="Link einfuegen" title="Enter search keyword">
      </form>'; ?>
                    <div class="close-search js-close-search">
                        <i class="material-icons">close</i>
                    </div>
                </div>
                <!-- #END# Search Bar -->

                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                            <i class="material-icons">swap_vert</i>
                        </button>
                        <a href="javascript:void(0);" class="left-toggle-left-sidebar js-left-toggle-left-sidebar">
                            <i class="material-icons">menu</i>
                        </a>
                        <!-- Logo -->
                        <a class="navbar-brand" href="index.html">
                            <span class="logo-minimized">AJ</span>
                            <span class="logo">appleJuice - phpGUI</span>
                        </a>
                        <!-- #END# Logo -->
                    </div>
                    <div class="navbar-collapse" id="navbar-collapse">
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="javascript:void(0);" class="toggle-left-sidebar js-toggle-left-sidebar">
                                    <i class="material-icons">menu</i>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <!-- Call Search -->
                            <li>
                                <a href="javascript:void(0);" class="js-search" data-close="true">
                                    <i class="material-icons">search</i>
                                </a>
                            </li>
                            <!-- #END# Call Search -->
                            <!-- Fullscreen Request -->
                            <li>
                                <a href="javascript:void(0);" onClick="window.location.reload()">
                                    <i class="material-icons">refresh</i>
                                </a>
                            </li>
                            <!-- #END# Fullscreen Request -->
                            <!-- User Menu -->
                            <li class="dropdown user-menu">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="https://cdn.pixabay.com/photo/2021/05/20/19/59/apple-6269548_1280.png" alt="Profile">
            						<span class="hidden-xs"><?php echo $settings_xml['NICK']['VALUES']['CDATA']; ?></span>
        						</a>
                                <ul class="dropdown-menu">
                                    <li class="header">
                                        <img src="https://cdn.pixabay.com/photo/2021/05/20/19/59/apple-6269548_1280.png" alt="User Avatar" />
                                        <div class="user">
                                            <?php echo $settings_xml['NICK']['VALUES']['CDATA']; ?>
                                            
                                        </div>
                                    </li>
                                    <li class="body">
                                        <ul>
                                            <li>
                                                <a href="index.php?site=user_settings">
                                                    <i class="material-icons">settings</i> <?php echo $lang->Navigation->user_settings; ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="footer">
                                        <div class="row clearfix">
                                            <div class="col-xs-5">
                                                <a href="index.php?site=logout" class="btn btn-default btn-sm btn-block">Logout</a>
                                            </div>
                                            <div class="col-xs-2"></div>
                                            <div class="col-xs-5">
                                             <a class="btn btn-sm btn-default" data-toggle="modal" data-target="#defaultModal"><?php echo $lang->Navigation->kick_core; ?></a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- #END# User Menu -->
                            
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <!-- #END# Top Bar -->
        <!-- Left Menu -->
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <ul class="metismenu">
                    <li class="title">
                        
                    </li>
                    <li <?php active("start"); ?>>
                    	<a href="index.php?site=start">
                    		<i class="material-icons">dashboard</i>
                    		<span class="nav-label"><?php echo $lang->Navigation->dashboard; ?></span>
                    	</a>
                    </li>
                    <li <?php active("downloads"); ?>>
                    	<a href="index.php?site=downloads">
                    		<i class="material-icons">download</i>
                    		<span class="nav-label"><?php echo $lang->Navigation->downloads; ?></span>
                    	</a>
                    </li>
                    <li <?php active("uploads"); ?>>
                    	<a href="index.php?site=uploads">
                    		<i class="material-icons">upload</i>
                    		<span class="nav-label"><?php echo $lang->Navigation->uploads; ?></span>
                    	</a>
                    </li>
                    <li  <?php active("search"); ?>>
                    	<a href="index.php?site=search">
                    		<i class="material-icons">search</i>
                    		<span class="nav-label"><?php echo $lang->Navigation->search; ?></span>
                    	</a>
                    </li>
                    <li  <?php active("shares"); ?>>
                    	<a href="index.php?site=shares">
                    		<i class="material-icons">share</i>
                    		<span class="nav-label"><?php echo $lang->Navigation->shares; ?></span>
                    	</a>
                    </li>
                    <li  <?php active("server"); ?>>
                    	<a href="index.php?site=server">
                    		<i class="material-icons">dns</i>
                    		<span class="nav-label"><?php echo $lang->Navigation->server_list; ?></span>
                    	</a>
                    </li>
                    <li  <?php active("settings"); ?>>
                    	<a href="index.php?site=settings">
                    		<i class="material-icons">settings</i>
                    		<span class="nav-label"><?php echo $lang->Navigation->settings; ?></span>
                    	</a>
                    </li>
                    <li  <?php active("extras"); ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">extension</i>
                            <span class="nav-label"><?php echo $lang->Navigation->addons; ?></span>
                        </a>
                        <ul>
                        <?php
                    $phpaj_pluginurllist=array();

                    // Links zu den plugins zeigen

                  // Links zu den plugins zeigen

                    foreach($Plugin->liste as $a){
                        echo '<li>
                        <a href="index.php?site=extras&show='.$a[2].'">
                           '.$a[0].'
                        </a>
                    </li>';
                    }

                    ?>
                        </ul>
                    </li>
                    
                </ul>
            </nav>
        </aside>
        <!-- #END# Left Menu -->
        <section class="content <?php echo $_GET["site"]; ?>">
            <!-- Dashboard Heading -->
            				<!-- Search Ausgabe -->
