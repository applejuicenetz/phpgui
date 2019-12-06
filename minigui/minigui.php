<?php
session_start();


if (isset($_POST['host']) && !empty($_POST['host'])) {
    $host = explode(':', $_POST['host'], 2);
    $_SESSION['core_host'] = $_POST['host'];
    $_SESSION['core_ip'] = $host[0];
    $_SESSION['core_port'] = $host[1];
}

if (empty($_SESSION['core_host'])) {
    header('Location: ../index.php');
    die;
}

if (empty($_SESSION['core_pass'])) {
    if (32 === strlen($_POST['cpass'])) $_SESSION['core_pass'] = ($_POST['cpass']);
    else $_SESSION['core_pass'] = md5($_POST['cpass']);
}

include_once "../main/subs.php";
include_once "../main/classes/class_core.php";
$core = new Core;

echo writehead('phpaj-minigui',1);
echo "<meta http-equiv=\"refresh\" content=\"60\" />";	//jede min neu laden
echo $_SESSION['stylesheet'];
echo "</head><body>";

if(!empty($_POST['upslot'])){
	$_POST['maxup']=$_POST['maxup']*1024;	//von kb in bytes umrechnen
	$_POST['maxdown']=$_POST['maxdown']*1024;	//von kb in bytes umrechnen
	$core->command("function","setsettings?MaxUpload=".$_POST['maxup']
	."&Speedperslot=".$_POST['upslot']."&MaxDownload=".$_POST['maxdown']);
}

//Info holen
$statusbar_xml=$core->command("xml","modified.xml?filter=informations;down");
$settings_xml=$core->command("xml","settings.xml");
$dl_fertig=0;
$dl_gesamt=0;
if(!empty($statusbar_xml['DOWNLOAD'])){
	foreach(array_keys($statusbar_xml['DOWNLOAD']) as $a){
		if($statusbar_xml['DOWNLOAD'][$a]['STATUS'] === "14") $dl_fertig++;
		$dl_gesamt++;
	}
}

echo "<div>";

// zeit
echo $_SESSION['language']['GENERAL']['TIME'].": "
	.date("j.n.y - H:i:s",($statusbar_xml['TIME']['VALUES']['CDATA'])/1000);
$temp=array_keys($statusbar_xml['INFORMATION']);

// infos
echo "<ul class=\"mini\">";
echo "<li>";
if($dl_gesamt) echo "<a href=\"downloads.php?".SID."\" title=\"more infos\">";
echo strtr($_SESSION['language']['MINIGUI']['DL_FINISHED_TEXT'],
	array('%finished'=>$dl_fertig,'%count'=>$dl_gesamt));
if($dl_gesamt) echo "</a>";
echo "</li>";

echo "<li>".$_SESSION['language']['STATUSBAR']['DOWN'].": "
	.sizeformat($statusbar_xml['INFORMATION'][$temp[0]]['DOWNLOADSPEED'])
	."/s</li>\n";

echo "<li>".$_SESSION['language']['STATUSBAR']['UP'].": "
	.sizeformat($statusbar_xml['INFORMATION'][$temp[0]]['UPLOADSPEED'])
	."/s</li>\n";

echo "<li>".$_SESSION['language']['STATUSBAR']['CREDITS']
	.": ".sizeformat($statusbar_xml['INFORMATION'][$temp[0]]['CREDITS'])
	."</li>";

echo "<li>".$_SESSION['language']['STATUSBAR']['TRAFFIC']
	.": "
	.sizeformat($statusbar_xml['INFORMATION'][$temp[0]]['SESSIONDOWNLOAD'])
	." in, "
	.sizeformat($statusbar_xml['INFORMATION'][$temp[0]]['SESSIONUPLOAD'])
	." out</li>";

echo "</ul>";

// optionen

echo "<form action=\"minigui.php?".SID."\" method=\"post\">";
echo "<ul class=\"mini\">";
echo "<li><label for=\"maxdown\">max dl</label>: "
	."<input id=\"maxdown\" size=\"3\" name=\"maxdown\" value=\""
	.($settings_xml['MAXDOWNLOAD']['VALUES']['CDATA'] / 1024)."\" /></li>";
echo "<li><label for=\"maxup\">max ul</label>: "
	."<input id=\"maxup\" size=\"2\" name=\"maxup\" value=\""
	.($settings_xml['MAXUPLOAD']['VALUES']['CDATA'] / 1024)
	."\" /></li>";
echo "<li><label for=\"upslot\">ul/slot</label>: "
	."<input id=\"upslot\" size=\"2\" name=\"upslot\" value=\""
	.$settings_xml['SPEEDPERSLOT']['VALUES']['CDATA']."\" /></li>";
echo "<li><input type=\"submit\" value=\"OK\" /></li>";
echo "</ul>";
echo "</form>";

echo "<a href=\"index.php?".SID."\">logout</a>";
echo "</div>";
echo "</body>
</html>";
