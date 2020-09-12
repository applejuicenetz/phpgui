<?php
session_start();
require_once "subs.php";
require_once "classes/class_share.php";
require_once "classes/class_uploads.php";
require_once "classes/class_icons.php";

$icon_img = new Icons();
$lang =& $_SESSION['language']['UPLOADS'];

//standardmaessig nur laufende uploads zeigen
	if(empty($_GET['show_uplds'])) $_GET['show_uplds']=1;
	if(empty($_GET['show_queue'])) $_GET['show_queue']=-1;

echo writehead('Uploads');
//neu laden
echo "<meta http-equiv=\"refresh\" content=\""
	.$_ENV['GUI_REFRESH_UPLOADS']."; URL=".$_SERVER['PHP_SELF']."?"
	."show_uplds=".$_GET['show_uplds']."&amp;show_queue=".$_GET['show_queue']
	."&amp;".SID."\" />";
echo $_SESSION['stylesheet'];

echo '</head>
<body>';

$Sharelist = new Share();
$Uploadlist = new Uploads();
$Uploadlist->refresh_cache();

// Zeitpunkt, an dem die daten vom core geholt wurden + reload link
	echo $_SESSION['language']['GENERAL']['TIME'].": ".$Uploadlist->time();
	echo " (<a href='javascript: window.location.reload()'>"
		.$_SESSION['language']['GENERAL']['REFRESH']."</a>)<br />\n";

$uploadusercount="0";
$uploaduserpercent="?";
if(!empty($Uploadlist->cache['IDS']['VALUES']['UPLOADID']))
	$uploadusercount=
		count($Uploadlist->cache['IDS']['VALUES']['UPLOADID']);
if(isset($Uploadlist->cache['phpaj_MAXUPLOADPOSITIONS'])){
	$uploaduserpercent= (int) ((($uploadusercount/
		$Uploadlist->cache['phpaj_MAXUPLOADPOSITIONS'])*100)+0.5);
}
echo strtr($lang['LIMIT'], array("%percent"=>$uploaduserpercent));

// Tabelle anlegen + Zeilenueberschriften
echo "<table width=\"100%\">\n";
echo "<tr>
<th colspan=\"2\">".$lang['FILE']."</th>";
if(!empty($_ENV['REL_INFO'])) {
    echo '<th width="16" align="center"><img src="../style/default/info.png" width="16" alt="" /></th>';
}
echo "<th>".$lang['USERNAME']."</th>
<th>".$lang['STATUS']."</th>
<th>".$lang['SPEED']."</th>
<th>".$lang['SIZE']."</th>
<th width=\"100\">".$lang['FINISHED']."</th>
<th>".$lang['FINISHED_FILE']."</th>
<th>".$lang['PRIORITY']."</th>
<th>".$lang['CLIENTVERSION']."</th></tr>\n\n";

// Uebertrage
if($_GET['show_uplds']==1){
	echo "<tr><td colspan=\"10\"><a href=\"".$_SERVER['PHP_SELF']."?show_uplds=-1&amp;"
		."show_queue=".$_GET['show_queue']."&amp;".SID."\">"
		."<img src=\"../style/"
		.$_SESSION['minus_icon']."\" border=\"0\" alt=\"-\" />&nbsp;&nbsp;<b>"
		.$lang['TRANSFERRING']."</b> ("
		.$Uploadlist->cache['phpaj_ul'].")</a></td></tr>";
	if(!empty($Uploadlist->cache['UPLOAD'])){
		foreach($Uploadlist->cache['phpaj_ids_ul'] as $a){
			$current_upload=$Uploadlist->get_upload($a);
			echo "<tr><td width=\"10\"></td>";
			$current_shareid=&$current_upload['SHAREID'];
			$current_share=$Sharelist->get_file($current_shareid);
			echo "<td>".$icon_img->directstate[$current_upload['DIRECTSTATE']];
			//dateiname
			echo '<a href="'.$current_share['LINK'].'">' . htmlspecialchars($current_share['SHORTFILENAME']).'</a></td>';

            //relInfo
            if (!empty($_ENV['REL_INFO'])) {
                echo '<td align="center"><a target="_blank" href="' . sprintf($_ENV['REL_INFO'], $current_share['LINK']) . '"><img src="../style/default/info.png" width="16" alt="" border="0" title="Information" /></a></td>';
            }

			//Nick des Users
			echo "<td title=\"".htmlspecialchars($current_upload['NICK'])."\">"
				.htmlspecialchars(cutstring($current_upload['NICK'],30))."</td>\n";
			//Status
			echo "<td>".$_SESSION['language']['ULSTATUS']['STATUS_'
				.$current_upload['STATUS']]."</td>\n";
			//Geschwindigkeit
			echo "<td class=\"right\">"
				.sizeformat($current_upload['SPEED'])
				."/s</td>\n";
			//Groesse des parts + wieviel davon schon geladen ist
			$fortschritt=(
				(($current_upload['ACTUALUPLOADPOSITION'])
					-($current_upload['UPLOADFROM']))/
				(($current_upload['UPLOADTO'])
					-($current_upload['UPLOADFROM'])))*100;
			$geladen=sizeformat(
				($current_upload['ACTUALUPLOADPOSITION'])
				-($current_upload['UPLOADFROM']));
			echo "<td class=\"right\">".sizeformat(
				($current_upload['UPLOADTO'])
				-($current_upload['UPLOADFROM']))."</td>";
			echo "<td width=\"100\">".progressbar($fortschritt,$geladen)."</td>";
			echo "<td class=\"right\">";
			if(isset($current_upload['LOADED']) && $current_upload['LOADED'] != -1){
				echo number_format($current_upload['LOADED']*100,2)."%";
			}else{
				echo "N/A";
			}
			echo "</td>\n";
			$pdlwert="";
			if($current_upload['PRIORITY'] > $current_share['PRIORITY']){
				$pdlwert="("
					.((($current_upload['PRIORITY']-$current_share['PRIORITY'])-10)/10)
					.") ";
			}
			//Upload prio
			echo "<td class=\"right\">".$pdlwert.$current_upload['PRIORITY']."</td>\n";
			//Betriebssystem + aj-version
			echo "<td>".$icon_img->os[$current_upload['OPERATINGSYSTEM']]
				.$current_upload['VERSION']."</td>";
			echo "</tr>\n";
		}
	}
}else{
	echo "<tr><td colspan=\"10\"><a href=\"".$_SERVER['PHP_SELF']."?show_uplds=1&amp;"
		."show_queue=".$_GET['show_queue']."&amp;"
		.SID."\"><img src=\"../style/"
		.$_SESSION['plus_icon']."\" border=\"0\" alt=\"+\" />&nbsp;&nbsp;<b>"
		.$lang['TRANSFERRING']."</b> ("
		.$Uploadlist->cache['phpaj_ul'].")</a></td></tr>";
}

//Warteschlange
if($_GET['show_queue']==1){
	echo "<tr><td colspan=\"10\"><a href=\"".$_SERVER['PHP_SELF']."?show_uplds="
		.$_GET['show_uplds']."&amp;show_queue=-1"."&amp;"
		.SID."\"><img src=\"../style/"
		.$_SESSION['minus_icon']."\" border=\"0\" alt=\"-\" />&nbsp;&nbsp;<b>"
		.$lang['QUEUE']."</b> ("
		.$Uploadlist->cache['phpaj_queue'].")</a></td></tr>";
	if(!empty($Uploadlist->cache['UPLOAD'])){
		foreach($Uploadlist->cache['phpaj_ids_queue'] as $a){
			$current_upload=$Uploadlist->get_upload($a);
			echo "<tr><td width=\"10\"></td>";
			$current_shareid=$current_upload['SHAREID'];
			$current_share=$Sharelist->get_file($current_shareid);
			echo "<td>".$icon_img->directstate[$current_upload['DIRECTSTATE']];
            echo '<a href="'.$current_share['LINK'].'">' . htmlspecialchars($current_share['SHORTFILENAME']).'</a></td>';

            //relInfo
            if (!empty($_ENV['REL_INFO'])) {
                echo '<td align="center"><a target="_blank" href="' . sprintf($_ENV['REL_INFO'], $current_share['LINK']) . '"><img src="../style/default/info.png" width="16" alt="" border="0" title="Information" /></a></td>';
            }

			echo "<td title=\"".htmlspecialchars($current_upload['NICK'])."\">"
				.htmlspecialchars(cutstring($current_upload['NICK'],30))."</td>\n";
			if(isset($current_upload['LASTCONNECTION'])){
				$ul_timediff=($Uploadlist->cache['TIME']['VALUES']['CDATA']
					-$current_upload['LASTCONNECTION'])
					/1000;
				echo "<td class=\"right\">"
					.sprintf("%dmin %02ds",$ul_timediff/60,$ul_timediff%60)
					."</td>\n";
			}else{
				echo "<td class=\"right\">&nbsp;</td>\n";
			}
			echo "<td></td>\n";
			echo "<td></td>\n";
			echo "<td></td>\n";
			echo "<td class=\"right\">";
			if(isset($current_upload['LOADED']) && $current_upload['LOADED'] != -1){
				echo number_format($current_upload['LOADED']*100,2)."%";
			}else{
				echo "N/A";
			}
			echo "</td>\n";
			$pdlwert="";
			if($current_upload['PRIORITY'] > $current_share['PRIORITY']){
				$pdlwert="("
					.((($current_upload['PRIORITY'] - $current_share['PRIORITY'])-10)/10)
					.") ";
			}
			//Upload prio
			echo "<td class=\"right\">"
				.$pdlwert.$current_upload['PRIORITY']
				."</td>\n";
			echo "<td>".$icon_img->os[$current_upload['OPERATINGSYSTEM']]
				.$current_upload['VERSION']."</td>";
			echo "</tr>\n";
		}
	}
}else{
	echo "<tr><td colspan=\"10\"><a href=\"".$_SERVER['PHP_SELF']."?show_uplds="
		.$_GET['show_uplds']."&amp;show_queue=1"."&amp;".SID."\">"
		."<img src=\"../style/"
		.$_SESSION['plus_icon']."\" border=\"0\" alt=\"+\" />&nbsp;&nbsp;<b>"
		.$lang['QUEUE']."</b> ("
		.$Uploadlist->cache['phpaj_queue'].")</a></td></tr>";
}

echo "</table><br />";

echo "</body>
</html>";
