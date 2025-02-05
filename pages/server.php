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

	echo'<div class="row g-4">';
$srv_timediff=$Servers->netstats['timeconnected']/60;

//server auflisten
foreach($Servers->ids() as $a){
	$serverinfo=$Servers->serverinfo($a);
	$server_delete_link=$_SERVER['PHP_SELF']
		."?site=server&action=removeserver&amp;serv_id=".$serverinfo['ID'];

	if($Servers->netstats['connectedwith']<0 || $srv_timediff>=30){
		$server_connect_link = '<button type="button" onclick="location.href=\'' . $_SERVER['PHP_SELF'] . '?site=server&action=serverlogin&amp;serv_id=' . $serverinfo['ID'] . '\'" class="btn btn-success">
                ' . $lang->Server->login . '</button>';
	}else{
		$server_connect_link = '<button type="button" onclick="location.href=\'' . $_SERVER['PHP_SELF'] . '?site=server&action=serverlogin&amp;serv_id=' . $serverinfo['ID'] . '\'" class="btn btn-primary" disable>
                ' . $lang->Server->login . '</button>';
	}
	//passendes bildchen raussuchen
	if($Servers->netstats['connectedwith'] == $serverinfo['ID']){
        $status = $lang->Server->connectet . ' <svg class="icon icon-xxl text-success">
                                <use xlink:href="' . GUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-wifi-signal-4"></use>
                              </svg>';
        
	}elseif($Servers->netstats['trytoconnectto'] == $serverinfo['ID']){
        $status = $lang->Server->try_connect.' <svg class="icon icon-xxl text-danger">
                                <use xlink:href="' . GUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-wifi-signal-0"></use>
                              </svg>';
	}elseif((($Servers->server_xml['TIME']['VALUES']['CDATA']
			-$serverinfo['LASTSEEN'])/1000) <= 86400){
        $status = $lang->Server->been_connected.' <svg class="icon icon-xxl text-warning">
                                <use xlink:href="' . GUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-wifi-signal-1"></use>
                              </svg>';
	}else{
        $status = $lang->Server->no_con . ' <svg class="icon icon-xxl">
                                <use xlink:href="' . GUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-wifi-signal-off"></use>
                              </svg>';
	}
        
        if($serverinfo["CONNECTIONTRY"] >= 1)
        {
                $connections = "(" . $serverinfo["CONNECTIONTRY"] . ")";
        }else{
                $connections = "";
        }
    if(empty($serverinfo["NAME"])) $serverinfo["NAME"] = "N/A";
    echo'
                      <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="text-body-secondary text-end">
                            ' . $status . '
                              
                            </div>
                            <div class="fs-4 fw-semibold">'.htmlspecialchars($serverinfo["NAME"]).'</div>
                            <div class="small text-body-secondary text-uppercase fw-semibold text-truncate">
                                ' . $serverinfo["HOST"] . '
                                <table>
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
        date("d.m.y - H:i:s",($serverinfo['LASTSEEN'])/1000) : $lang->Server->not_yet; echo' ' . $connections . '</td>
              </tr>
              </table>
                            </div>
                            <div class="mt-3 mb-0 d-md-flex justify-content-md-end">
                              <div class="btn-group" role="group" aria-label="Basic mixed styles example">
  <button type="button" onclick="location.href=\'' . $server_delete_link . '\'" class="btn btn-danger">' . $lang->Server->delet . '</button>
  ' . $server_connect_link . '
</div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- /.col-->';
}
echo "</div>\n";