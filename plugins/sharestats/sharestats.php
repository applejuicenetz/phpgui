<?php
include_once "classes/class_core.php";
$core = new Core;
/*
sharestats
*/
include_once "classes/class_share.php";
echo "<div class=\"tabs\" style=\"float:left;\">";
echo "<a href=\"".$phpaj_ownurl."&amp;stats=last\">"
	."Recently Requested</a><br /><br />";
echo "<a href=\"".$phpaj_ownurl."&amp;stats=-last\">"
	."Not Recently Requested</a><br /><br />";
echo "<a href=\"".$phpaj_ownurl."&amp;stats=most\">"
	."Most Requested</a><br /><br />";
echo "<a href=\"".$phpaj_ownurl."&amp;stats=-most\">"
	."Least Requested</a><br /><br />";
echo "<a href=\"".$phpaj_ownurl."&amp;stats=search\">"
	."Most Searched</a><br /><br />";
echo "<a href=\"".$phpaj_ownurl."&amp;stats=-search\">"
	."Least Searched</a><br /><br />";
echo "</div>";
$coreinfo=$core->getcoreversion();
$coresubversions=explode(".",$coreinfo['VERSION']);
if($coresubversions[2]<146) die("<img src=\"../style/"
	.$_SESSION['server_warning_icon']."\" alt=\"[!]\" />"
	."Core 0.30.146.1203 or newer required");
if(empty($_GET['stats'])) $_GET['stats']="last";
$Sharelist = new Share;
$Sharelist->refresh_cache(2);
if(!empty($Sharelist->cache['SHARES']['VALUES']['SHARE'])){
	echo "<div style=\"float:left; margin-left:1cm;\">";
	echo "<table>";
	echo "<tr>";
	echo "<th>Position</th>";
	$sfsort=array();
	switch($_GET['stats']){
		case "most":
			echo "<th>Requests</th>";
			$sfsort=ajsort($Sharelist->cache['SHARES']
				['VALUES']['SHARE'],'ASKCOUNT',SORT_NUMERIC,1);
			$statsvalue='ASKCOUNT';
			break;
		case "-most":
			echo "<th>Requests</th>";
			$sfsort=ajsort($Sharelist->cache['SHARES']
				['VALUES']['SHARE'],'ASKCOUNT',SORT_NUMERIC,0);
			$statsvalue='ASKCOUNT';
			break;
		case "search":
			echo "<th>Search Requests</th>";
			$sfsort=ajsort($Sharelist->cache['SHARES']
				['VALUES']['SHARE'],'SEARCHCOUNT',SORT_NUMERIC,1);
			$statsvalue='SEARCHCOUNT';
			break;
		case "-search":
			echo "<th>Search Requests</th>";
			$sfsort=ajsort($Sharelist->cache['SHARES']
				['VALUES']['SHARE'],'SEARCHCOUNT',SORT_NUMERIC,0);
			$statsvalue='SEARCHCOUNT';
			break;
		case "-last":
			echo "<th>Date</th>";
			$sfsort=ajsort($Sharelist->cache['SHARES']
				['VALUES']['SHARE'],'LASTASKED',SORT_NUMERIC,0);
			$statsvalue='LASTASKED';
			break;
		default:
			echo "<th>Date</th>";
			$sfsort=ajsort($Sharelist->cache['SHARES']
				['VALUES']['SHARE'],'LASTASKED',SORT_NUMERIC,1);
			$statsvalue='LASTASKED';
			break;
	}
	$sfsort=array_keys($sfsort);
	echo "<th>Filename</th></tr>";
	for($i=0;$i<30;$i++){
		if(!empty($sfsort[$i])){
			$cur_share=&$Sharelist->get_file($sfsort[$i]);
			echo "<tr><td style=\"font-weight:bold; text-align:center;\">"
				.($i+1).".</td><td style=\"text-align:center;\">";
			$wert=$cur_share[$statsvalue];
			echo ($statsvalue=='LASTASKED') ?
				date("j.n.y - H:i:s",($wert/1000)) : $wert;
			echo "</td><td>";
			echo "<a href=\"ajfsp://file|"
				.$cur_share['SHORTFILENAME']."|"
				.$cur_share['CHECKSUM']."|"
				.$cur_share['SIZE']."/\">";
			echo htmlspecialchars($cur_share['SHORTFILENAME']);
			echo "</a></td></tr>";
		}
	}
	echo "</table>";
	echo "</div>";
}
