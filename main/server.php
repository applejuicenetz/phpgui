<?php
session_start();
require_once "subs.php";
require_once "classes/class_icons.php";
require_once "classes/class_server.php";
$icon_img = new Icons();
$Servers = new Server();
$lang =& $_SESSION['language']['SERVER'];

echo writehead('Server');
echo $_SESSION['stylesheet'];
echo "<script type=\"text/javascript\">
<!--
function addajserver(){
	var host=document.newserver.serverhost.value;
	var port=document.newserver.serverport.value;
	if(host=='' || port=='') return;
	parent.oben.document.linkform.ajfsp_link.value=
		'ajfsp://server|'+host+'|'+port+'/';
	parent.oben.document.linkform.showlinkpage.value=1;
	parent.oben.document.linkform.submit();
}
//-->
</script>\n";
echo '</head><body>';

//zum connecten + loeschen
$action_echo='';
if(!empty($_GET['action'])){
	if(!empty($_GET['serv_id']) && $_GET['serv_id'] > 0)
		$action_echo = $Servers->action($_GET['action'], $_GET['serv_id']);
	//mehr server von applejuicenet.de holen
	if($_GET['action']=='getservers'){
		$Servers->getmore();
	}
}

echo $_SESSION['language']['GENERAL']['TIME'].": ".$Servers->time();
echo " (<a href=\"javascript: window.location.href='".$_SERVER['PHP_SELF']."?"
	.SID."'\">"
	.$_SESSION['language']['GENERAL']['REFRESH']."</a>)<br />\n";

if($Servers->netstats['firewalled']==='true')
	echo 	"<span style=\"background-color:#FF0000;\"><img src=\"../style/"
		.$_SESSION['server_warning_icon']."\" />"
		.$lang['FIREWALLED']."</span><br />";

echo "<div align=\"center\"><table><tr>";
echo "<td>".$icon_img->serverstatus['verbunden']." "
	.$lang['STATUS_CONNECTED']."</td>";
echo "<td>".$icon_img->serverstatus['verbinde']." "
	.$lang['STATUS_CONNECTING']."</td>";
echo "<td>".$icon_img->serverstatus['alt']." "
	.$lang['STATUS_OLD']."</td>";
echo "<td>".$icon_img->serverstatus['neu']." "
	.$lang['STATUS_NEW']."</td>";
echo "</tr></table></div>";

//tabellenanfang + Ueberschriften
echo "<table width=\"100%\">\n";
echo "<tr><td colspan=\"7\">";
echo "<a href=\"".$_SERVER['PHP_SELF']."?action=getservers&amp;".SID."\">"
	.$lang['MORE']."</a>";
echo "</td></tr>";
echo "<tr>
<th width=\"15\">&nbsp;</th>
<th>".$lang['NAME']."</th>
<th>".$lang['HOST']."</th>
<th>".$lang['PORT']."</th>
<th>".$lang['LASTSEEN']."</th>
<th>&nbsp;</th>
<th>&nbsp;</th></tr>";

//überprüfen, wie lange man schon verbunden ist
$srv_timediff=$Servers->netstats['timeconnected']/60;

//server auflisten
foreach($Servers->ids() as $a){
	$serverinfo=$Servers->serverinfo($a);
	$server_delete_link="<a href=\"".$_SERVER['PHP_SELF']
		."?action=removeserver&amp;serv_id=".$serverinfo['ID']."&amp;"
		.SID."\">".$lang['DELETE']."</a>";

	if($Servers->netstats['connectedwith']<0 || $srv_timediff>=30){
		$server_connect_link="<a href=\"".$_SERVER['PHP_SELF']
			."?action=serverlogin&amp;serv_id="	.$serverinfo['ID']."&amp;"
			.SID."\">".$lang['LOGIN']."</a>";
	}else{
		$server_connect_link=$lang['LOGIN'];
	}
	echo "<tr>\n";
	echo "<td><div class=\"menuicon\">";
	//passendes bildchen raussuchen
	if($Servers->netstats['connectedwith'] == $serverinfo['ID']){
		echo $icon_img->serverstatus['verbunden'];
	}elseif($Servers->netstats['trytoconnectto'] == $serverinfo['ID']){
		echo $icon_img->serverstatus['verbinde'];
	}elseif((($Servers->server_xml['TIME']['VALUES']['CDATA']
			-$serverinfo['LASTSEEN'])/1000) <= 86400){
		echo $icon_img->serverstatus['neu'];
	}else{
		echo $icon_img->serverstatus['alt'];
	}
	echo "<ul class=\"menu\">";
	echo "<li>$server_connect_link</li>";
	echo "<li><a href=\"ajfsp://server|".$serverinfo['HOST']
		."|".$serverinfo['PORT']."/\">[ajfsp-link]</a></li>";
	echo "<li>$server_delete_link</li>";
	echo "</ul></div></td>\n";
	echo "<td>".htmlspecialchars($serverinfo['NAME'])."</td>\n";
	echo "<td>".$serverinfo['HOST']."</td>\n";
	echo "<td class=\"right\">".$serverinfo['PORT']."</td>\n";
	echo "<td class=\"right\">";
	echo ($serverinfo['LASTSEEN']>0) ?
		date("d.m.y - H:i:s",($serverinfo['LASTSEEN'])/1000) : "&nbsp;";
	echo "</td>\n";
	//server aus liste l�schen
	echo "<td>$server_delete_link</td>\n";
	//auf server einloggen
	echo "<td>$server_connect_link (".$serverinfo['CONNECTIONTRY'].")</td>\n";
	echo "</tr>\n\n";
}
echo "</table><br />\n";
echo "<form name=\"newserver\" action=\"\">\n";

echo "<div style=\"float:left;\">\n";
//netstats anzeigen
echo strtr($lang['NETSTATS'],
	array("%usercount"=>number_format($Servers->netstats['users']),
		"%filecount"=>number_format($Servers->netstats['filecount']),
		"%sharesize"=>sizeformat($Servers->netstats['filesize'])))
	."<br /><br />";

//evtl. meldungen anzeigen (s.o.)
echo $action_echo;
echo "</div>\n";

//Neuer Server
echo "<div style=\"position:absolute; right:20px;\">\n";
echo "<table>\n<tr><th colspan=\"3\">".$lang['NEWSERVER']
	."</th></tr>\n";
echo "<tr><td>".$lang['HOST']
	."</td><td><input name=\"serverhost\" /></td>";
echo "<td rowspan=\"2\">"
	."<input type=\"button\" value=\"OK\" onclick=\"addajserver()\" />"
	."</td></tr>\n";
echo "<tr><td>".$lang['PORT']
	."</td><td><input name=\"serverport\" /></td></tr>\n";
echo "</table>\n";
echo "</div>\n";
echo "</form>\n";

echo "</body>
</html>";
