<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\subs;

$core = new Core();

echo'<div class="card mb-4">
		<div class="card-body">
			<ul class="nav">
				<li class="nav-item">
    				<a class="nav-link active" aria-current="page" href="?site=extras&show=sharestats/sharestats.php&amp;stats=last">
    					k&uuml;rzlich angefordert
    				</a>
				</li>
				<li class="nav-item">
    				<a class="nav-link active" aria-current="page" href="?site=extras&show=sharestats/sharestats.php&amp;stats=-last">
    					nicht k&uuml;rzlich angefordert
    				</a>
				</li>
				<li class="nav-item">
    				<a class="nav-link active" aria-current="page" href="?site=extras&show=sharestats/sharestats.php&amp;stats=most">
    					h&auml;ufig angefrgat
    				</a>
				</li>
				<li class="nav-item">
    				<a class="nav-link active" aria-current="page" href="?site=extras&show=sharestats/sharestats.php&amp;stats=-most">
    					amwenigsten nachgefragt
    				</a>
				</li>
				<li class="nav-item">
    				<a class="nav-link active" aria-current="page" href="?site=extras&show=sharestats/sharestats.php&amp;stats=search">
    					h&auml;ufig gesucht
    				</a>
				</li>
				<li class="nav-item">
    				<a class="nav-link active" aria-current="page" href="?site=extras&show=sharestats/sharestats.php&amp;stats=-search">
    					amwenigsten gesucht
    				</a>
				</li>
			</ul>
		<div class="table-responsive">
                                    <table class="table table-hover">
                                        <tbody>
                                            
                                        ';
                


if(empty($_GET['stats'])) $_GET['stats']="most";
$Sharelist = new Share;
$Sharelist->refresh_cache(2);
if(!empty($Sharelist->cache['SHARES']['VALUES']['SHARE'])){
	echo "<th>#</th>";
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