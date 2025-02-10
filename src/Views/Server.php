<?php

use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\UI\Language;
use appleJuiceNETZ\Kernel;

$Servers = new Server();
$template = new template();

//Language
$language = Language::getLanguage();

//zum connecten + loeschen
$action_echo='';
if (!empty($_GET['action'])) {
  // Prüfen, ob 'serv_id' gesetzt und größer als 0 ist
  if (!empty($_GET['serv_id']) && $_GET['serv_id'] > 0) {
      template::toast($_GET['action'], "info");
      // Die Aktion für den Server ausführen
      $action_echo = $Servers->action($_GET['action'], $_GET['serv_id']);
  }

  // Wenn die Aktion 'getservers' ist, mehr Server holen
  if ($_GET['action'] == 'getservers') {
      $Servers->getmore();
  }
}

	echo'<div class="row g-4">';
  // Überprüfen, ob 'timeconnected' gesetzt ist und ob der Wert gleich '?' ist
if (isset($Servers->netstats['timeconnected']) && $Servers->netstats['timeconnected'] === '?') {

} else {
  // Der Inhalt von 'timeconnected' ist nicht gleich '?'
  $srv_timediff = (int)($Servers->netstats['timeconnected'] / 60);
}


//server auflisten
foreach($Servers->ids() as $a){
	$serverinfo=$Servers->serverinfo($a);
	$server_delete_link=$_SERVER['PHP_SELF']
		."?site=Server&action=removeserver&amp;serv_id=".$serverinfo['ID'];

	if($Servers->netstats['connectedwith']<0 || $srv_timediff>=30){
		$server_connect_link = '<button type="button" onclick="location.href=\'?site=Server&action=serverlogin&amp;serv_id=' . $serverinfo['ID'] . '\'" class="btn btn-success">
                ' . $language->translate('Server.login') . '</button>';
	}else{
		$server_connect_link = '<button type="button" onclick="location.href=\'site=Server&action=serverlogin&amp;serv_id=' . $serverinfo['ID'] . '\'" class="btn btn-primary" disable>
                ' . $language->translate('Server.login') . '</button>';
	}
	//passendes bildchen raussuchen
	if($Servers->netstats['connectedwith'] == $serverinfo['ID']){
        $status = $language->translate('Server.connectet') . ' <svg class="icon icon-xxl text-success">
                                <use xlink:href="' . WEBUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-wifi-signal-4"></use>
                              </svg>';
        
	}elseif($Servers->netstats['trytoconnectto'] == $serverinfo['ID']){
        $status = $language->translate('Server.try_connect').' <svg class="icon icon-xxl text-danger">
                                <use xlink:href="' . WEBUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-wifi-signal-0"></use>
                              </svg>';
	}elseif((($Servers->server_xml['TIME']['VALUES']['CDATA']
			-$serverinfo['LASTSEEN'])/1000) <= 86400){
        $status = $language->translate('Server.been_connected').' <svg class="icon icon-xxl text-warning">
                                <use xlink:href="' . WEBUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-wifi-signal-1"></use>
                              </svg>';
	}else{
        $status = $language->translate('Server.no_con') . ' <svg class="icon icon-xxl">
                                <use xlink:href="' . WEBUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-wifi-signal-off"></use>
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
              <td class="fw-bold">'.$language->translate('Server.last_connection').'</td>
              </tr>
              <tr>
              <td>';
              $lastSeen = (int)($serverinfo['LASTSEEN'] / 1000);

if ($lastSeen > 0) {
    echo date("d.m.y - H:i:s", $lastSeen);
} else {
    echo $language->translate('Server.not_yet');
} echo' ' . $connections . '</td>
              </tr>
              </table>
                            </div>
                            <div class="mt-3 mb-0 d-md-flex justify-content-md-end">
                              <div class="btn-group" role="group" aria-label="Basic mixed styles example">
  <button type="button" onclick="location.href=\'' . $server_delete_link . '\'" class="btn btn-danger">' . $language->translate('Server.delet') . '</button>
  ' . $server_connect_link . '
</div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- /.col-->';
}
echo "</div>\n";