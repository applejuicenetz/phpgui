<?php
include("_includes/header.php");

include("_classes/server.php");

$template = new template();
$Servers =new Server();
$subs = new subs();

$language = new language($_ENV['GUI_LANGUAGE']);
$lang = $language->translate();

if(!isset($_GET["show"])) $_GET["show"] = "";
if(empty($_GET["site"])) $_GET["site"] = "start";
echo'<div class="page-heading">
                <h1>'.$subs->get_title($_GET['site']).'</h1>
                <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php?site=start">Home</a></li>
            <li class="breadcrumb-item">'.$subs->get_title($_GET['site']).'</li>

            '.$template->bread($_GET["show"]).'


        </ol>
            </div>

            <div class="page-body">';
        // Wichtige Fehlermeldungen auf allen Seiten anzeigen 
        	//Firewall aktiv    
            if($Servers->netstats['firewalled']==='true'){
				$template->alert("danger", "Firewall", $lang->System->firewall);
            }
            //veraltete Version

if(file_exists("pages/".$_GET['site'].".php")){

include_once("pages/".$_GET['site'].".php");

}else{
   
include_once("pages/404.php");

}

include("_includes/footer.php");
?>