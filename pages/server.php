<?php

use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$Servers = new Server();
$template = new template();

//Language
$language = Kernel::getLanguage();
$lang = $language->translate();

//zum connecten + loeschen
$action_echo='';
if(!empty($_GET['action'])){
	if(!empty($_GET['serv_id']) && $_GET['serv_id'] > 0)
		$action_echo = $Servers->action($_GET['action'], $_GET['serv_id']);
	//mehr server von applejuicenet.cc holen
	if($_GET['action']=='getservers'){
		$Servers->getmore();
	}
}

	echo'<div class="row">';
$srv_timediff=$Servers->netstats['timeconnected']/60;

//server auflisten
foreach($Servers->ids() as $a){
	$serverinfo=$Servers->serverinfo($a);
	$server_delete_link="<a href=\"".$_SERVER['PHP_SELF']
		."?site=server&action=removeserver&amp;serv_id=".$serverinfo['ID']."&amp;"
		.SID."\">".$lang->Server->delet."</a>";

	if($Servers->netstats['connectedwith']<0 || $srv_timediff>=30){
		$server_connect_link="<a href=\"".$_SERVER['PHP_SELF']
			."?site=server&action=serverlogin&amp;serv_id="	.$serverinfo['ID']."&amp;"
			.SID."\">".$lang->Server->login."</a>";
	}else{
		$server_connect_link=$lang->Server->login;
	}
	//passendes bildchen raussuchen
	if($Servers->netstats['connectedwith'] == $serverinfo['ID']){
        $status = $lang->Server->connectet.' <i class="fa fa-wifi text-success"></i>';
        
	}elseif($Servers->netstats['trytoconnectto'] == $serverinfo['ID']){
        $status = $lang->Server->try_connect.' <i class="fa fa-wifi text-danger">network_wifi</i>';
	}elseif((($Servers->server_xml['TIME']['VALUES']['CDATA']
			-$serverinfo['LASTSEEN'])/1000) <= 86400){
        $status = $lang->Server->been_connected.' <i class="fa fa-wifi col-warning"></i>';
	}else{
        $status = $lang->Server->no_con.' <i class="fa fa-wifi"></i>';
	}
    if(empty($serverinfo["NAME"])) $serverinfo["NAME"] = "N/A";
    echo'           <div class="col-sm-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="text-end">'.$status.'</div>
<h5 class="bs-bold">'.htmlspecialchars($serverinfo["NAME"]).'</h5>
              <table>
              <tr>
              <td class="fw-bold">Server-URL</td>
              </tr>
              <tr>
              <td>'.$serverinfo["HOST"].'</td>
              </tr>
              <tr>
              <td class="fw-bold">Server-Port</td>
              </tr>
              <tr>
              <td>'.$serverinfo["PORT"].'</td>
              </tr>
              <tr>
              <td class="fw-bold">'.$lang->Server->last_connection.'</td>
              </tr>
              <tr>
              <td>';echo ($serverinfo['LASTSEEN']>0) ?
        date("d.m.y - H:i:s",($serverinfo['LASTSEEN'])/1000) : $lang->Server->not_yet; echo'</td>
              </tr>
              </table>
              </div>
            <div class="card-footer">'.$server_delete_link.'   '.$server_connect_link.' ('.$serverinfo["CONNECTIONTRY"].')<br>
            </div>
          </div></div>';
}
echo "</div>\n";