<?php

use appleJuiceNETZ\Kernel;
use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\GUI\GUI;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\Template\Dashboard;
use appleJuiceNETZ\Template\Alert;


$language = Kernel::getLanguage();
$lang = $language->translate();

//Classes abrufen
$Servers = new Server();
$core = new Core();
$icon_img = new Icons();
$subs = new subs();
$Uploadlist = new Uploads();
$Sharelist = new Share();
$Alert = new Alert();
$Gui = new GUI();

//Cache laden
$subs::refresh_cache();
//Info holen
$modified = $core->command("xml", "modified.xml?filter=informations");
$temp = array_keys($modified['INFORMATION']);
$information =& $modified['INFORMATION'][$temp[0]];
$temp2 = array_keys($modified['NETWORKINFO']);
$netinfo =& $modified['NETWORKINFO'][$temp2[0]];

//Core Infos
$coreinfo = $Servers->core->getcoreversion();
$coresubversions = explode(".", $_SESSION['cache']['STATUSBAR']['VERSION']);
$info = $Servers->info();


$_SESSION['phpaj']['core_source_ip'] = $netinfo['IP'];
if (empty($_SESSION['phpaj']['core_source_port']))
{
    $settings = $core->command("xml", "settings.xml");
    $_SESSION['phpaj']['core_source_port'] = $settings['PORT']['VALUES']['CDATA'];
}

if (isset($information['MAXUPLOADPOSITIONS']))
{
    $_SESSION['cache']['UPLOADS']['phpaj_MAXUPLOADPOSITIONS'] =
        $information['MAXUPLOADPOSITIONS'];
}

//Warnungen
if ($Servers->netstats['firewalled'] === 'true')
{
    $Alert->Notification("danger", $lang->System->warnung, $lang->System->firewall);
}
if($_GET['add_credits'])
{
    $credit_add = $core->command("function","setinformations?credits=991671594");
    return $credit_add;
}

?>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <div class="tab-content rounded-bottom mb-3">
                  <div class="tab-pane active preview">
                    <div class="row g-4" id="output">
                        <!-- Downloads -->    
                            <?php Dashboard::Downloads(); ?>
                        <!-- Uploads -->
                            <?php Dashboard::Uploads(); ?>
                        <!-- Shared Files-->
                            <?php Dashboard::Shared(); ?>
                        <!-- Credits-->
                            <?php Dashboard::Credits(); ?>
                        <!-- Server -->    
                        <?php Dashboard::Server(); ?>
                    </div>
                    
                  </div>
        </div>
        <div class="tab-content rounded-bottom mb-3">
                  <div class="tab-pane active preview">
                    <div class="row g-1">
                        
                    </div>
                    
                  </div>
        </div>
        <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="card mb-3">
            <div class="card-body p-3">
            <?php Dashboard::Traffic(); ?>

            </div>
        </div>
    </div>
    </div>    </div>
    <!-- Rightside -->
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        
    <div class="card mb-3">
            <div class="card-body p-3">
            <?php Dashboard::Informations(); ?>

            </div>
        </div>
       
    </div>

	
		<!-- Statistik -->
		
        <!-- end-->
        </div>
     

	</div>

	<!--  div class="row mt-4 mb-4"      
    ?php
    $coreinfo = $Servers->core->getcoreversion();
    $coresubversions = explode(".", $_SESSION['cache']['STATUSBAR']['VERSION']);
    $info = $Servers->info();
    //AJ News
    $subs->appleJuiceNews(90, $coreinfo['VERSION']);


    ?
	/div -->	

	
