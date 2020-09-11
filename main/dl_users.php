<?php
session_start();
require_once "subs.php";
require_once "classes/class_downloads.php";
require_once "classes/class_icons.php";
$icon_img = new Icons;
$Downloadlist = new Downloads;
$Downloadlist->refresh_cache();

echo writehead('Download Info ('.htmlspecialchars($Downloadlist->cache
	['DOWNLOAD'][$_GET['dl_id']]['FILENAME']).')');
echo $_SESSION['stylesheet'];
echo "<script>
function showparts(id){
	var ajpartinfo=window.open('dl_parts.php?usr_id='+id+'&".SID."',
		'ajdlparts',
		'width=540,height=300,left=10,top=10,dependent=yes,scrollbars=no');
	ajpartinfo.focus();
}
</script>";
echo "</head><body>\n";

//default anzeige
	if(empty($_GET['show_dls'])){$_GET['show_dls']=1;}
	if(empty($_GET['show_queue'])){$_GET['show_queue']=-1;}
	if(empty($_GET['show_rest'])){$_GET['show_rest']=-1;}

echo $_SESSION['language']['GENERAL']['TIME'].": ".$Downloadlist->time();
echo " (<a href='javascript: window.location.reload()'>"
	.$_SESSION['language']['GENERAL']['REFRESH']."</a>)<br />\n";

//tabelle + Ueberschriften
echo "<table width=\"100%\">\n";
echo "<tr>
	<th width=\"10\">&nbsp;</th>
	<th>".$_SESSION['language']['DOWNLOADS']['SOURCES']."</th>
	<th>".$_SESSION['language']['DOWNLOADS']['FILENAME']."</th>
	<th>".$_SESSION['language']['DOWNLOADS']['STATUS']."</th>
	<th>".$_SESSION['language']['DOWNLOADS']['SPEED']."</th>
	<th>".$_SESSION['language']['DOWNLOADS']['POWERDOWNLOAD_SHORT']."</th>
	<th>".$_SESSION['language']['DOWNLOADS']['SIZE']."</th>
	<th width=\"100\">".$_SESSION['language']['DOWNLOADS']['FINISHED']."</th>
	<th>".$_SESSION['language']['DOWNLOADS']['CLIENTVERSION']."</th></tr>\n";

//download zeigen
if(!empty($Downloadlist->cache['DOWNLOAD'])){
	$a = $Downloadlist->download($_GET['dl_id']);
	echo "<tr>\n";
	echo "<td>&nbsp;</td>\n";
	echo "<td class=\"right\">"
		.($a['phpaj_quellen_queue']+$a['phpaj_quellen_dl'])."/"
		.$a['phpaj_quellen_gesamt']." (".$a['phpaj_quellen_dl'].")</td>\n";
	echo "<td title=\"".htmlspecialchars($a['FILENAME'])."\">"
		.htmlspecialchars(cutstring($a['FILENAME'],25))."</td>\n";
	echo "<td>"
		.$_SESSION['language']['DLSTATUS']['STATUS_'.$a['phpaj_STATUS']]."</td>\n";
	echo "<td class=\"right\">".sizeformat($a['phpaj_dl_speed'])."/s</td>\n";
	echo "<td class=\"right\">".((($a['POWERDOWNLOAD'])+10)/10)."</td>\n";
	echo "<td class=\"right\">".sizeformat($a['SIZE'])."</td>";
	$fortschritt=&$a['phpaj_DONE'];
	echo "<td width=\"100\">"
		.progressbar($fortschritt,sizeformat($a['phpaj_READY']))."</td>";
	echo "<td></td>";
	echo "</tr>\n";


//Einzelne Quellen
	//Uebertrage
	if($_GET['show_dls']==1){
		echo "<tr><td colspan=\"9\">"
			."<a href=\"".$_SERVER['PHP_SELF']."?dl_id=$a[ID]&amp;show_dls=-1&amp;"
			."show_queue=$_GET[show_queue]&amp;show_rest=$_GET[show_rest]&amp;"
			.SID."\">"
			."<img src=\"../style/".$_SESSION['minus_icon']."\" border=\"0\" "
			."alt=\"-\" />&nbsp;&nbsp;<b>"
			.$_SESSION['language']['DOWNLOADS']['TRANSFERRING']
			."</b> (".$a['phpaj_quellen_dl'].")</a></td></tr>\n";
		if(!empty($Downloadlist->cache['USER'])){
			foreach($a['phpaj_ids_quellen_dl'] as $b){
				$current_user = $Downloadlist->user($b);
				echo "<tr><td></td>";
				echo "<td>".$icon_img->directstate[$current_user['DIRECTSTATE']]
					."<a href=\"javascript:showparts($b)\" title=\""
					.htmlspecialchars($current_user['NICKNAME'])."\">"
					.htmlspecialchars(cutstring($current_user['NICKNAME'],25))
					."</a></td>";
				echo "<td title=\"".htmlspecialchars($current_user['FILENAME'])."\">"
					.htmlspecialchars(cutstring($current_user['FILENAME'],25))."</td>";
				echo "<td>".$_SESSION['language']['USERSTATUS']['STATUS_7']."</td>";
				echo "<td class=\"right\">"
					.sizeformat($current_user['SPEED'])."/s</td>";
				echo "<td class=\"right\">"
					.(($current_user['POWERDOWNLOAD'] +10)/10)."</td>";
				echo "<td class=\"right\">"
					.sizeformat($current_user['DOWNLOADTO']
					- $current_user['DOWNLOADFROM'])."</td>";
				$fortschritt=(($current_user['ACTUALDOWNLOADPOSITION']
					- $current_user['DOWNLOADFROM'])/
					($current_user['DOWNLOADTO'] - $current_user['DOWNLOADFROM']))
					*100;
				echo "<td width=\"100\">"
					.progressbar($fortschritt,sizeformat(
					$current_user['ACTUALDOWNLOADPOSITION']
					- $current_user['DOWNLOADFROM']))."</td>";
				echo "<td>".$icon_img->os[$current_user['OPERATINGSYSTEM']]
					.$current_user['VERSION']."</td>";
				echo "</tr>\n";
			}
		}
	}else{
		echo "<tr><td colspan=\"9\">"
			."<a href=\"".$_SERVER['PHP_SELF']."?dl_id=$a[ID]&amp;show_dls=1&amp;"
			."show_queue=$_GET[show_queue]&amp;show_rest=$_GET[show_rest]&amp;"
			.SID."\">"
			."<img src=\"../style/".$_SESSION['plus_icon']."\" border=\"0\" "
			."alt=\"+\" />&nbsp;&nbsp;<b>"
			.$_SESSION['language']['DOWNLOADS']['TRANSFERRING']
			."</b> (".$a['phpaj_quellen_dl'].")</a></td></tr>\n";
	}

	//Warteschlange
	if($_GET['show_queue']==1){
		echo "<tr><td colspan=\"4\">"
			."<a href=\"".$_SERVER['PHP_SELF']."?dl_id=$a[ID]&amp;show_queue=-1&amp;"
			."show_dls=$_GET[show_dls]&amp;show_rest=$_GET[show_rest]&amp;"
			.SID."\">"
			."<img src=\"../style/".$_SESSION['minus_icon']."\" border=\"0\" "
			."alt=\"-\" />&nbsp;&nbsp;<b>"
			.$_SESSION['language']['DOWNLOADS']['QUEUED']
			."</b> (".$a['phpaj_quellen_queue'].")</a></td>";
		echo "<th>".$_SESSION['language']['DOWNLOADS']['USERSOURCE']
			."</th><td colspan=\"4\">&nbsp;</td></tr>\n";
		if(!empty($Downloadlist->cache['USER'])){
		foreach($a['phpaj_ids_quellen_queue'] as $b){
					$current_user = $Downloadlist->user($b);
					echo "<tr><td></td>";
					echo "<td>"
						.$icon_img->directstate[$current_user['DIRECTSTATE']]
						."<a href=\"javascript:showparts($b)\" title=\""
						.htmlspecialchars($current_user['NICKNAME'])."\">"
						.htmlspecialchars(cutstring($current_user['NICKNAME'],25))
						."</a></td>";
					echo "<td title=\"".htmlspecialchars($current_user['FILENAME'])."\">"
						.htmlspecialchars(cutstring($current_user['FILENAME'],25))."</td>";
					echo "<td>".$_SESSION['language']['USERSTATUS']
						['STATUS_'.$current_user['STATUS']];
					if($current_user['QUEUEPOSITION'] > 0
							&& $current_user['STATUS']=="5"){
						echo " ".$current_user['QUEUEPOSITION'];
					}
					echo "</td>";
					echo "<td>";
					if(!empty($current_user['SOURCE'])
							&& $current_user['SOURCE']>=1
							&& $current_user['SOURCE']<=6)
						echo $_SESSION['language']['USERSOURCE']
							['SRC_'.$current_user['SOURCE']];
						else echo "N/A";
					echo "</td>";
					echo "<td class=\"right\">"
						.(($current_user['POWERDOWNLOAD'] +10)/10)."</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td>".$icon_img->os[$current_user['OPERATINGSYSTEM']]
						.$current_user['VERSION']."</td>";
					echo "</tr>\n";
		}
	}
	}else{
		echo "<tr><td colspan=\"4\">"
			."<a href=\"".$_SERVER['PHP_SELF']."?&amp;dl_id=$a[ID]&amp;show_queue=1&amp;"
			."show_dls=$_GET[show_dls]&amp;show_rest=$_GET[show_rest]&amp;"
			.SID."\">"
			."<img src=\"../style/".$_SESSION['plus_icon']."\" border=\"0\" "
			."alt=\"+\" />&nbsp;&nbsp;<b>"
			.$_SESSION['language']['DOWNLOADS']['QUEUED']
			."</b> (".$a['phpaj_quellen_queue'].")</a></td>";
		echo "<th>".$_SESSION['language']['DOWNLOADS']['USERSOURCE']
			."</th><td colspan=\"4\">&nbsp;</td></tr>\n";
	}

	//Rest
	if($_GET['show_rest']==1){
		echo "<tr><td colspan=\"9\">"
			."<a href=\"".$_SERVER['PHP_SELF']."?dl_id=$a[ID]&amp;show_rest=-1&amp;"
			."show_dls=$_GET[show_dls]&amp;show_queue=$_GET[show_queue]&amp;"
			.SID."\">"
			."<img src=\"../style/".$_SESSION['minus_icon']."\" border=\"0\" "
			."alt=\"-\" />&nbsp;&nbsp;<b>"
			.$_SESSION['language']['DOWNLOADS']['REST']
			."</b> (".($a['phpaj_quellen_gesamt']-$a['phpaj_quellen_queue']
			-$a['phpaj_quellen_dl']).")</a></td></tr>\n";
		if(!empty($Downloadlist->cache['USER'])){
		foreach($a['phpaj_ids_quellen_rest'] as $b){
					$current_user = $Downloadlist->user($b);
					echo "<tr><td></td>";
					echo "<td>"
						.$icon_img->directstate[$current_user['DIRECTSTATE']]
						."<a href=\"javascript:showparts($b)\" title=\""
						.htmlspecialchars($current_user['NICKNAME'])."\">"
						.htmlspecialchars(cutstring($current_user['NICKNAME'],25))
						."</a></td>";
					echo "<td title=\"".htmlspecialchars($current_user['FILENAME'])."\">"
						.htmlspecialchars(cutstring($current_user['FILENAME'],25))."</td>";
					echo "<td>".$_SESSION['language']['USERSTATUS']
						['STATUS_'.$current_user['STATUS']]."</td>";
					echo "<td>";
					if(!empty($current_user['SOURCE'])
							&& $current_user['SOURCE']>=1
							&& $current_user['SOURCE']<=6)
						echo $_SESSION['language']['USERSOURCE']
							['SRC_'.$current_user['SOURCE']];
						else echo "N/A";
					echo "</td>";
					echo "<td class=\"right\">"
						.(($current_user['POWERDOWNLOAD'] +10)/10)."</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td>".$icon_img->os[$current_user['OPERATINGSYSTEM']]
						.$current_user['VERSION']."</td>";
					echo "</tr>\n";
		}
	}
	}else{
		echo "<tr><td colspan=\"9\">"
			."<a href=\"".$_SERVER['PHP_SELF']."?dl_id=$a[ID]&amp;show_rest=1&amp;"
			."show_dls=$_GET[show_dls]&amp;show_queue=$_GET[show_queue]&amp;"
			.SID."\">"
			."<img src=\"../style/".$_SESSION['plus_icon']."\" border=\"0\" "
			."alt=\"+\" />&nbsp;&nbsp;<b>"
			.$_SESSION['language']['DOWNLOADS']['REST']
			."</b> (".($a['phpaj_quellen_gesamt']-$a['phpaj_quellen_queue']
			-$a['phpaj_quellen_dl']).")</a></td></tr>\n";
	}
}
echo "</table>";
echo "</body>
</html>";
