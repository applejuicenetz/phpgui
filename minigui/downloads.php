<?php
session_start();
include_once "../main/subs.php";
include_once "../main/classes/class_core.php";
$core =& new Core;

echo writehead('aj-minigui',1);
echo "<meta http-equiv=\"refresh\" content=\"60\" />";	//jede min neu laden
echo $_SESSION['stylesheet'];
echo "</head><body>";

if(!empty($_GET['action'])){
	if($_GET['action']=="setpowerdownload"){
		if($_GET['powerdownload']==11) $_GET['powerdownload']=0;
		if($_GET['powerdownload']==1) $_GET['powerdownload']=12;
		$_GET['action'].="?id=".$_GET['id']."&Powerdownload="
			.$_GET['powerdownload'];
	}
	$core->command("function",$_GET['action']);
}

$downloadinfo=$core->command("xml","modified.xml?filter=down");
echo "<div>";
echo $_SESSION['language']['GENERAL']['TIME'].": "
	.date("j.n.y - H:i:s",($downloadinfo['TIME']['VALUES']['CDATA'])/1000);

echo "<ul class=\"mini\">";
if(!empty($downloadinfo['DOWNLOAD'])){
	foreach(array_keys($downloadinfo['DOWNLOAD']) as $id){
		$download=$downloadinfo['DOWNLOAD'][$id];
		echo "<li>";
		echo "<h3 class=\"mini\">".htmlspecialchars($download['FILENAME'])."</h3>";
		echo "<ul>";
		echo "<li>".round(($download['READY']/$download['SIZE'])*100)."% ";
		echo $_SESSION['language']['MINIGUI']['OF']." "
			.sizeformat($download['SIZE'])."</li>";
		echo "<li>Pdl: ".sprintf("%.1f",(($download['POWERDOWNLOAD']+10)/10));
		echo " <a href=\"downloads.php?action=setpowerdownload&amp;id="
			.$download['ID']."&amp;powerdownload="
			.($download['POWERDOWNLOAD']+1)."&amp;".SID."\">[+]</a>";
		echo " <a href=\"downloads.php?action=setpowerdownload&amp;id="
			.$download['ID']."&amp;powerdownload="
			.($download['POWERDOWNLOAD']-1)."&amp;".SID."\">[-]</a></li>";
		echo "</ul></li>";
	}
}
echo "</ul>";
echo "<a href=\"downloads.php?action=cleandownloadlist&amp;"
	.SID."\">[".$_SESSION['language']['DOWNLOADS']['CLEANDOWNLOADLIST']
	."]</a><br />";
echo "<a href=\"minigui.php?".SID."\">"
	.$_SESSION['language']['MINIGUI']['BACK']."</a>";

echo "</div></body>
</html>";
