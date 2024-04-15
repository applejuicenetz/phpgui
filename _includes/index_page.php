<?php
include("_includes/header.php");

include("_classes/server.php");

$template = new template();
$Servers =new Server();

if(!isset($_GET["show"])) $_GET["show"] = "";
if(empty($_GET["site"])) $_GET["site"] = "start";
echo'<div class="page-heading">
                <h1>'.$tab.'</h1>
                <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php?site=start">Home</a></li>
            <li class="breadcrumb-item">'.$tab.'</li>

            '.$template->bread($_GET["show"]).'


        </ol>
            </div>

            <div class="page-body">';
        // Wichtige Fehlermeldungen auf allen Seiten anzeigen 
        	//Firewall aktiv    
            if($Servers->netstats['firewalled']==='true'){
				$template->alert("danger", "Firewall", $_SESSION['language']['SERVER']['FIREWALLED']);
            }
            //veraltete Version

if(file_exists("pages/".$_GET['site'].".php")){

include_once("pages/".$_GET['site'].".php");

}else{
   
include_once("pages/404.php");

}

include("_includes/footer.php");
?>