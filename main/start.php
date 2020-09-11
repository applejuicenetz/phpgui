<?php
session_start();

require_once "subs.php";
require_once "classes/class_share.php";
require_once "classes/class_server.php";

$lang = $_SESSION['language']['START'];

echo writehead('Start');
echo $_SESSION['stylesheet'];
echo '</head><body>';

$Servers = new Server();

echo $_SESSION['language']['GENERAL']['TIME'].": ".$Servers->time();
echo " (<a href=\"javascript: window.location.href='".$_SERVER['PHP_SELF']."?"
	.SID."'\">"
	.$_SESSION['language']['GENERAL']['REFRESH']."</a>)<br />\n";

//server welcome msg
if(!empty($Servers->netstats['welcome'])){
	echo "<div style=\"float:right; text-align:right; width:50%; margin-right:0.5cm;\">";
	echo "<h2>".$lang['SERVER_MSG']."</h2>";
		echo "<div style=\"float:right;\">";
		echo "<table><tr><td>";
		echo $Servers->netstats['welcome'];
		echo "</td></tr></table>";
		echo "</div>";
	echo "</div>";
}

//Client
echo "<h2>".$lang['CLIENT']."</h2>";
	echo "<div style=\"margin-left:0.5cm;\"><table>";
	echo "<tr><td>".$lang['GUIVERSION'].":</td><td>".PHP_GUI_VERSION."</td></tr>";
	$coreinfo=$Servers->core->getcoreversion();
	$coresubversions=explode(".",$_SESSION['cache']['STATUSBAR']['VERSION']);
	echo "<tr><td>".$lang['COREVERSION'].":</td><td>"
		.$coreinfo['VERSION']."</td></tr>";
	echo "<tr><td>".$lang['COREOS'].":</td><td>"
		.$coreinfo['SYSTEM']."</td></tr>";
	echo "</table>
	<a href=\"http://wiki.applejuicenet.de/\" target=\"_blank\">FAQ</a>";
	echo "</div>";

//Warnungen
	$warnungen=array();
	if($Servers->netstats['firewalled']==='true'){
		$warnungen[] = $_SESSION['language']['SERVER']['FIREWALLED'];
	}

	if(!empty($warnungen)){
		echo "<h2>".$lang['WARNINGS']."</h2>";
		echo "<div style=\"margin-left:0.5cm;background-color:#FF0000;\">";
		foreach($warnungen as $a) {
		    echo "<img src=\"../style/" .$_SESSION['server_warning_icon']."\" alt=\"[!]\" />".$a."<br />";
        }
		echo "</div>";
	}

//News
	echo "<h2>".$lang['NEWS']."</h2>";
	echo "<div style=\"margin-left:0.5cm;\">";
	if($_ENV['GUI_SHOW_NEWS']) {
	    getnews(0,$coreinfo['VERSION']);
    }
	if(!empty($_SESSION['cache']['NEWS']['ITEMS'])){
		getnews(90,$coreinfo['VERSION']);
		echo "(<a href=\"".$_SERVER['PHP_SELF']."?".SID."&amp;reloadnews=1\">"
			.$_SESSION['language']['GENERAL']['REFRESH']."</a>)<br />";
		echo "<table><tr><td>";
		echo $_SESSION['cache']['NEWS']['ITEMS'];
		echo "</td></tr></table>";
	}else{
		echo $lang['NEWS_MISSING']
			." (<a href=\"".$_SERVER['PHP_SELF']."?".SID."&amp;reloadnews=1\">"
			.$_SESSION['language']['GENERAL']['REFRESH']."</a>)<br />";
	}
	echo "</div>";

//Share
	echo "<h2>".$lang['SHARE']."</h2>";
	echo "<div style=\"margin-left:0.5cm;\">";
	$Sharelist = new Share();
	if($_ENV['GUI_SHOW_SHARE']) {
	    $Sharelist->refresh_cache(30);
    }
	if(!empty($_SESSION['phpaj']['share_LASTTIMESTAMP'])){
		$share_anzahl=0;
		$share_groesse=0;
		foreach(array_keys($Sharelist->cache['SHARES']
				['VALUES']['SHARE']) as $a){
			$share_anzahl++;
			$share_groesse+=$Sharelist->cache['SHARES']
				['VALUES']['SHARE'][$a]['SIZE'];
		}
		echo "<table><tr><td>";
		echo strtr($lang['SHARE_TEXT'],
			array("%sharecount"=>number_format($share_anzahl),
				"%sharesize"=>sizeformat($share_groesse)));
		echo "</td></tr></table>";
	}else{
		echo $lang['SHAREINFO_MISSING']
			." (<a href=\"".$_SERVER['PHP_SELF']."?".SID."&amp;reloadshare=1\">"
			.$_SESSION['language']['GENERAL']['REFRESH']."</a>)<br />";
	}
	echo "</div>";

//Netzwerk
	echo "<h2>".$lang['NETWORK']."</h2>";
	echo "<div style=\"margin-left:0.5cm;\">";
	$srv_timediff=$Servers->netstats['timeconnected'];
	$srv_timediff=sprintf("%dh %dmin %ds",
		$srv_timediff/3600,($srv_timediff%3600)/60,$srv_timediff%60);
	echo "<table><tr><td>";
	echo strtr($lang['SERVER_TEXT'],
		array("%servername"=>$Servers->netstats['servername'],
			"%servercount"=>$Servers->netstats['servercount'],
			"%timediff"=>$srv_timediff))
		."<br />";
	$info=$Servers->info();
	echo $info['OPENCONNECTIONS']
		.$lang['OPENCONNECTIONS']."<br />";
	echo strtr($_SESSION['language']['SERVER']['NETSTATS'],
		array("%usercount"=>number_format($Servers->netstats['users']),
			"%filecount"=>number_format($Servers->netstats['filecount']),
			"%sharesize"=>sizeformat($Servers->netstats['filesize'])))
			."<br />";
	echo "</td></tr></table>";
	echo "</div>";

echo "</body>
</html>";
