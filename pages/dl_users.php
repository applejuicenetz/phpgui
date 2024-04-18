<?php
require_once "_classes/subs.php";
require_once "_classes/downloads.php";

$Downloadlist = new Downloads;
$Downloadlist->refresh_cache();

echo "<script>
function showparts(id){
	var ajpartinfo=window.open('?site=dl_parts&usr_id='+id+'&".SID."',
		'ajdlparts',
		'width=540,height=300,left=10,top=10,dependent=yes,scrollbars=no');
	ajpartinfo.focus();
}
</script>";

//default anzeige
	if(empty($_GET['show_dls'])){$_GET['show_dls']=1;}
	if(empty($_GET['show_queue'])){$_GET['show_queue']=-1;}
	if(empty($_GET['show_rest'])){$_GET['show_rest']=-1;}

//tabelle + Ueberschriften
echo "<table width=\"100%\">\n";
echo "<tr>
	<th width=\"10\">&nbsp;</th>
	<th>".$lang->Downloads->source."</th>
	<th>".$lang->Downloads->filename."</th>
	<th>".$lang->Downloads->statuss."</th>
	<th>".$lang->Downloads->speed."</th>
	<th>".$lang->Downloads->pdl."</th>
	<th>".$lang->Downloads->size."</th>
	<th width=\"100\">".$lang->Downloads->finished."</th>
	<th>".$lang->Downloads->clientversion."</th></tr>\n";

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
	$balken=&$a['phpaj_DONE'];
	echo "<td width=\"100\">";
	echo '<div class="progress mt-3">
                <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: '.$balken.'%" aria-valuenow="'.$fortstritt.'" aria-valuemin="0" aria-valuemax="100">
                '.$balken.' %</div>
              </div>';
		echo"</td>";
	echo "<td></td>";
	echo "</tr>\n";


//Einzelne Quellen
	//Uebertrage
	if($_GET['show_dls']==1){
		echo "<tr><td colspan=\"9\">"
			."<a href=\"".$_SERVER['PHP_SELF']."?site=dl_users&dl_id=$a[ID]&amp;show_dls=1&amp;"
			."show_queue=$_GET[show_queue]&amp;show_rest=$_GET[show_rest]&amp;"
			.SID."\">"
			."<img src=\"../style/".$_SESSION['minus_icon']."\" border=\"0\" "
			."alt=\"-\" />&nbsp;&nbsp;<b>"
			.$_SESSION['language']['DOWNLOADS']['TRANSFERRING']
			."</b> (".$a['phpaj_quellen_dl'].")</a></td></tr>\n";
		if(empty($Downloadlist->cache['USER'])){
			foreach($a['phpaj_ids_quellen_dl'] as $b){
				$current_user = $Downloadlist->user($b);
				echo "<tr><td></td>";
				echo "<td>"
					."<a href=\"javascript:showparts($b)\" title=\""
					.htmlspecialchars($current_user['NICKNAME'])."\">2"
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
