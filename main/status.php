<?php
session_start();
require_once "subs.php";
require_once "classes/class_core.php";
$core = new Core;

echo writehead('Status');
//neu laden
echo "<meta http-equiv=\"refresh\" content=\""
	.$_ENV['GUI_REFRESH_STATUS']."; URL=".$_SERVER['PHP_SELF']."?".SID."\" />";

echo $_SESSION['stylesheet'];
echo "</head><body class=\"status\">
<table width=\"100%\"><tr>";

//Info holen
$statusbar_xml=$core->command("xml","modified.xml?filter=informations");
$temp=array_keys($statusbar_xml['INFORMATION']);
$information=&$statusbar_xml['INFORMATION'][$temp[0]];
$temp2=array_keys($statusbar_xml['NETWORKINFO']);
$netinfo=&$statusbar_xml['NETWORKINFO'][$temp2[0]];

//Serververbindung?
$serverconnection=$_SESSION['language']['SERVER']['STATUS_NOT_CONNECTED'];
if($netinfo['CONNECTEDWITHSERVERID']>=0){
	$serverconnection=$_SESSION['language']['SERVER']['STATUS_CONNECTED'];
}else{
	if($netinfo['TRYCONNECTTOSERVER']>=0)
		$serverconnection=$_SESSION['language']['SERVER']['STATUS_CONNECTING'];
}

$_SESSION['phpaj']['core_source_ip']=$netinfo['IP'];
if(empty($_SESSION['phpaj']['core_source_port'])){
	$settings=$core->command("xml","settings.xml");
	$_SESSION['phpaj']['core_source_port']=$settings['PORT']['VALUES']['CDATA'];
}

if(isset($information['MAXUPLOADPOSITIONS'])){
	$_SESSION['cache']['UPLOADS']['phpaj_MAXUPLOADPOSITIONS']=
		$information['MAXUPLOADPOSITIONS'];
}

echo "<td>".$serverconnection."</td>";
echo "<td>".$_SESSION['language']['STATUSBAR']['CONNECTIONS'].": "
	.$information['OPENCONNECTIONS']."</td>";
echo "<td>".$_SESSION['language']['STATUSBAR']['TRAFFIC'].": "
	.sizeformat($information['SESSIONDOWNLOAD'])
	." in, "
	.sizeformat($information['SESSIONUPLOAD'])
	." out</td>";
echo "<td>".$_SESSION['language']['STATUSBAR']['DOWN'].": "
	.sizeformat($information['DOWNLOADSPEED'])
	."/s</td>";
echo "<td>".$_SESSION['language']['STATUSBAR']['UP'].": "
	.sizeformat($information['UPLOADSPEED'])
	."/s</td>";
echo "<td>".$_SESSION['language']['STATUSBAR']['CREDITS'].": "
	.sizeformat($information['CREDITS'])
	."</td>";


echo "</tr></table>
</body>
</html>";
