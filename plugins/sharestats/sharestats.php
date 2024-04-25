<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\subs;

$core = new Core();

echo'
<div class="row clearfix">
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-2 email-menu">
                        <div class="list-group">';
                        echo "<a href=\"?site=extras&show=sharestats/sharestats.php&amp;stats=last\">"
	."Recently Requested</a><br /><br />";
echo "<a href=\"?site=extras&show=sharestats/sharestats.php&amp;stats=-last\">"
	."Not Recently Requested</a><br /><br />";
echo "<a href=\"?site=extras&show=sharestats/sharestats.php&amp;stats=most\">"
	."Most Requested</a><br /><br />";
echo "<a href=\"?site=extras&show=sharestats/sharestats.php&amp;stats=-most\">"
	."Least Requested</a><br /><br />";
echo "<a href=\"?site=extras&show=sharestats/sharestats.php&amp;stats=search\">"
	."Most Searched</a><br /><br />";
echo "<a href=\"?site=extras&show=sharestats/sharestats.php&amp;stats=-search\">"
	."Least Searched</a>";
                      echo'  </div>

                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">
                        <div class="panel panel-default panel">
                            
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <tbody>
                                            
                                        ';
                


$coreinfo=$core->getcoreversion();
$coresubversions=explode(".",$coreinfo['VERSION']);
if($coresubversions[2]<146) die("<img src=\"../style/"
	.$_SESSION['server_warning_icon']."\" alt=\"[!]\" />"
	."Core 0.30.146.1203 or newer required");
if(empty($_GET['stats'])) $_GET['stats']="last";
$Sharelist = new Share;
$Sharelist->refresh_cache(2);
if(!empty($Sharelist->cache['SHARES']['VALUES']['SHARE'])){
	echo "<th>Position</th>";
	$sfsort=array();
	switch($_GET['stats']){
		case "most":
			echo "<th>Requests</th>";
			$sfsort= subs::ajsort($Sharelist->cache['SHARES']
				['VALUES']['SHARE'],'ASKCOUNT',SORT_NUMERIC,1);
			$statsvalue='ASKCOUNT';
			break;
		case "-most":
			echo "<th>Requests</th>";
			$sfsort= subs::ajsort($Sharelist->cache['SHARES']
				['VALUES']['SHARE'],'ASKCOUNT',SORT_NUMERIC,0);
			$statsvalue='ASKCOUNT';
			break;
		case "search":
			echo "<th>Search Requests</th>";
			$sfsort= subs::ajsort($Sharelist->cache['SHARES']
				['VALUES']['SHARE'],'SEARCHCOUNT',SORT_NUMERIC,1);
			$statsvalue='SEARCHCOUNT';
			break;
		case "-search":
			echo "<th>Search Requests</th>";
			$sfsort= subs::ajsort($Sharelist->cache['SHARES']
				['VALUES']['SHARE'],'SEARCHCOUNT',SORT_NUMERIC,0);
			$statsvalue='SEARCHCOUNT';
			break;
		case "-last":
			echo "<th>Date</th>";
			$sfsort= subs::ajsort($Sharelist->cache['SHARES']
				['VALUES']['SHARE'],'LASTASKED',SORT_NUMERIC,0);
			$statsvalue='LASTASKED';
			break;
		default:
			echo "<th>Date</th>";
			$sfsort= subs::ajsort($Sharelist->cache['SHARES']
				['VALUES']['SHARE'],'LASTASKED',SORT_NUMERIC,1);
			$statsvalue='LASTASKED';
			break;
	}
	$sfsort=array_keys($sfsort);
	echo "<th>Filename</th></tr>";
	for($i=0;$i<50;$i++){
		if(!empty($sfsort[$i])){
			$cur_share=$Sharelist->get_file($sfsort[$i]);
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
	echo "</tbody>
                                    </table>
                                </div>";
	echo "</div>";
}