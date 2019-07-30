<?php
/*
versionchecker
*/
include_once "classes/class_icons.php";
$icon_img = new Icons;

function versionchecker($srcarray){
	global $icon_img;
	echo "<table>";
	echo "<tr><th width=\"100\">VersionChecker</th>";
	echo "<th width=\"80\">"
		.$icon_img->os[0]."</th><th width=\"80\">"
		.$icon_img->os[1]."Win</th><th width=\"80\">"
		.$icon_img->os[2]."Linux</th><th width=\"80\">"
		.$icon_img->os[3]."Mac</th><th width=\"80\">"
		.$icon_img->os[4]."Solaris</th><th width=\"80\">"
		.$icon_img->os[5]."OS/2</th><th width=\"80\">"
		.$icon_img->os[6]."FreeBSD</th><th width=\"80\">"
		.$icon_img->os[7]."NetWare</th>";
	echo "</tr>";
	ksort($srcarray);
	foreach(array_keys($srcarray) as $b){
		echo "<tr><td>$b</td>";
		for($i=0;$i<=7;$i++){
			if(empty($srcarray[$b][$i])) $srcarray[$b][$i]=0;
			echo "<td class=\"right\">".$srcarray[$b][$i]."</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}

function versionchecker_merge($array1=array(),$array2=array()){
	foreach(array_keys($array2) as $b){
		if(empty($array1[$b])){
			$array1[$b]=array();
			$array1[$b]=$array2[$b];
		}else{
			foreach(array_keys($array2[$b]) as $c){
				if(empty($array1[$b][$c])){
					$array1[$b][$c]=$array2[$b][$c];
				}else{
					$array1[$b][$c]+=$array2[$b][$c];
				}
			}
		}
	}
	return $array1;
}

$versionchecker['download']=array();
$versionchecker['upload']=array();

include_once "classes/class_downloads.php";
$Downloadlist = new Downloads;
$Downloadlist->refresh_cache();
if(!empty($Downloadlist->cache['USER'])){
	foreach(array_keys($Downloadlist->cache['USER']) as $b){
		$dluser=&$Downloadlist->user($b);
		if(empty($versionchecker['download']
				[$dluser['VERSION']][$dluser['OPERATINGSYSTEM']]))
			$versionchecker['download']
				[$dluser['VERSION']][$dluser['OPERATINGSYSTEM']]=0;
		$versionchecker['download']
			[$dluser['VERSION']][$dluser['OPERATINGSYSTEM']]++;
	}
}

include_once "classes/class_uploads.php";
$Uploadlist = new Uploads;
$Uploadlist->refresh_cache();
if(!empty($Uploadlist->cache['UPLOAD'])){
	foreach(array_keys($Uploadlist->ids()) as $a){
		$uluser=&$Uploadlist->get_upload($a);
		if(empty($versionchecker['upload']
				[$uluser['VERSION']][$uluser['OPERATINGSYSTEM']]))
			$versionchecker['upload']
				[$uluser['VERSION']][$uluser['OPERATINGSYSTEM']]=0;
		$versionchecker['upload']
			[$uluser['VERSION']][$uluser['OPERATINGSYSTEM']]++;
	}
}

echo "<div align=\"center\">";
echo "==Download==<br />";
versionchecker($versionchecker['download']);
echo "</div><br />";
echo "<div align=\"center\">";
echo "==Upload==<br />";
versionchecker($versionchecker['upload']);
echo "</div><br />";
echo "<div align=\"center\">";
echo "==Download &amp; Upload==<br />";
versionchecker(versionchecker_merge($versionchecker['download'],
	$versionchecker['upload']));
echo "</div><br />";
