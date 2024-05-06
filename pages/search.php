<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Search;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$core = new Core();
$Search = new Search();
$subs = new subs;
$template = new template();

//Language
$language = Kernel::getLanguage();
$lang = $language->translate();

if(empty($_GET['searchid'])) $_GET['searchid']="alles";

$action_echo='';
//suchanfrage an core uebergeben
if(!empty($_POST['searchstring'])){
	$_POST['searchstring']=trim($_POST['searchstring']);
	$Search->start($_POST['searchstring']);
}

//suche abbrechen
if(!empty($_GET['cancelid'])){
	$Search->cancel($_GET['cancelid']);
}


//suche loeschen
if(!empty($_GET['deleteid'])){
	$action_echo .= $Search->delete($_GET['deleteid']);
}

//alle ergebnisse loeschen
if(!empty($_GET['deleteall'])){
	echo $Search->delete_all();
}

if($_GET['searchid'] == "alles"){
	$active_all = "active";
}
// Datei download
if( !empty( $_GET['link'] ) )
{
	$linkss = $_GET['link'];

	$regexe = [
        // ajfsp://file|ajcore-0.31.149.110.jar|653f4d793595e65bbbe58c0c55620589|313164/
        '#ajfsp://(file)\|([^|]*)\|([a-z0-9]{32})\|([\d]*)/#',

        // ajfsp://server|knastbruder.applejuicenet.de|9855/
        '#ajfsp://(server)\|([^|]*)\|([\d]{1,5})/#',

        // ajfsp://file|ajcore-0.31.149.110.so|653f4d793595e65bbbe58c0c55620589|313164|123.123.123.123:9850/
        '#ajfsp://(file)\|([^|]*)\|([a-z0-9]{32})\|([\d]*)\|[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}:[\d]{1,5}/#',

        // ajfsp://file|ajcore-0.31.149.110.jar|653f4d793595e65bbbe58c0c55620589|313164|123.123.123.123:9850:knastbruder.applejuicenet.de:9855/
        '#ajfsp://(file)\|([^|]*)\|([a-z0-9]{32})\|([\d]*)\|[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}:[\d]{1,5}:[^:]*:[\d]{1,5}/#',
    ];

	$links = [];
	foreach ($regexe as $regex) {
        preg_match_all($regex, urldecode($linkss), $matches, PREG_SET_ORDER);
        $links = array_merge($links, $matches);
    }

foreach ($links as $link) {

        //Infos fr Dateilink anzeigen + im hauptfenster die downloads zeigen
        if ('file' === $link[1]) {

            $text = htmlspecialchars($link[2]) . ' (' . subs::sizeformat($link[4]) . ')';

            if ($core->command('function', 'processlink?link=' . urlencode($link[0])) == "ok") {
                $template->alert("success", $lang->Downloads->get_start, $text);
            }

        }
}    
}
//Searchcontent
	//Suchformular
	echo'<div class="row clearfix">
                    <div class="col-sm-12 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="align-left">
                                	<form action="?site=search&'.SID.'" method="post">
                                		<input type="text" size="40" name="searchstring">
                                		<button type="submit" class="btn btn-primary">'.$lang->Search->button.'</button>
                                	</form><br>
                                </div>
                            </div>
                        </div>
                    </div>';

echo'<div class="row">
  <div class="col-3">
    <div class="list-group" id="list-tab" role="tablist">
      <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center active" id="list-search-all" data-coreui-toggle="list" href="#search-all" role="tab" aria-controls="search-all">
    	' . $lang->Search->all . '
    	<span class="badge text-bg-primary rounded-pill">' . $Search->cache['SEARCHENTRY_count'] . '</span>
		</a>';
//Tabellen�berschrift
$Search->refresh_cache();

$Search->process_results();

//link fuer alle ergebnisse
if(!empty($Search->cache['SEARCH'])){
	//links fuer die einzelnen suchen
	foreach(array_keys($Search->cache['SEARCH']) as $b){
		
		//suche ausgewaehlt, oder nicht?
		if(!empty($_GET['searchid']) && $b==$_GET['searchid']){
			$active = "active";
		}else{
			$active = "";
		}
		
		// Suche aktiv oder inaktiv
		if($Search->cache['SEARCH'][$b]['RUNNING']==="true"){
			$button = '<span class="badge text-bg-warning" onclick="location.href=\'?site=search&cancelid='.$b.'\'">
						<i class="fa fa-close"></i>
					  </span>';	
		}else{
			$button = '<span class="badge text-bg-danger" onclick="location.href=\'?site=search&deleteid='.$b.'\'">
						<i class="fa fa-trash"></i>
					  </span>';
		}
		//name der suche + zahl der ergebnisse
		echo'<a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" id="list-search-' . $b . '" data-coreui-toggle="list" href="#search-' . $b . '" role="tab" aria-controls="search-' . $b . '">
    	' . $Search->cache['SEARCH'][$b]['SEARCHTEXT'] . '
    	<span class="badge text-bg-primary rounded-pill">' . $Search->cache['SEARCH'][$b]['phpaj_FOUNDFILES'] . '</span>
		</a>
      ';
		}
}
echo'
      </div>
  </div>
  <div class="col-9">
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show active" id="search-all" role="tabpanel" aria-labelledby="search-all">
      <span class="badge text-bg-danger" onclick="location.href=\'?site=search&deleteall=1\'"><i class="fa fa-trash"></i></span>
      <div class="table-responsive">
			 
      <table class="table table-striped">';

//Sortieren
if(!empty($Search->cache['SEARCHENTRY'])){
if(empty($_GET['sort'])) $_GET['sort']="count";
$searchsort=$Search->sortieren($_GET['sort']);
}

//tabellenueberschriften
echo "<tr>
		<th>".$lang->Search->name."
		</th>";

if(!empty($_ENV['REL_INFO'])) {
    echo '<th width="16" align="center">
    		<i class="fa fa-info-circle"></i>
    	  </th>';
}

echo "<th nowrap>".$lang->Search->size."</th>
<th>".$lang->Search->counter."</th>
<th>&nbsp;</th></tr>";

//suchergebnisse anzeigen
if(!empty($Search->cache['SEARCHENTRY'])){
	$result_counter=500;
	foreach(array_keys($searchsort) as $a ){
		$cur_search =& $Search->cache['SEARCHENTRY'][$a];
		//pruefen, ob ergebnis zu suche gehrt
		$result_counter--;
		if($result_counter<0 && empty($_GET['nolimit'])){
			//nach 500 ergebnissen den rest weglassen, wenn nicht anders gewuenscht
			echo "<tr><th colspan=\"4\"><a href=\"".$_SERVER['PHP_SELF']."?site=search&searchid="
				.$_GET['searchid']."&amp;nolimit=1&amp;".SID."\">"
				.$lan['NO_LIMIT']."</a></th></tr>";
			break;
		}
		//anzeige aller namen + anzahl
		$sort_names=array();
			foreach(array_keys($cur_search['FILENAME']) as $b){
				$sort_names["$b"]=$cur_search['FILENAME'][$b]['USER'];
			}
		arsort($sort_names,SORT_NUMERIC);
		$names=array_keys($sort_names);
		echo "<tr><td>\n<a href=\"javascript:toggleinfo($a,'";
		foreach($names as $c){
			echo $sort_names["$c"]."/".addslashes(htmlspecialchars($c))."|";
		}
		echo $cur_search['CHECKSUM']."/".$cur_search['SIZE'];
		echo "')\"><i class='fa fa-angle-down text-primary'></i></a>\n";
		//download starten
		$ajfsp_link="ajfsp://file|".addslashes(htmlspecialchars($names[0]))."|"
			.$cur_search['CHECKSUM']."|".$cur_search['SIZE']."/";
		echo "<a href=\"javascript:dllink('".$ajfsp_link
			."');\" title=\"Download\">\n".htmlspecialchars($names[0])."</a>";
		echo "<br /><div id=\"infobox_$a\" class=\"infobox\"></div></td>\n";
		//dateigr��e

        if (!empty($_ENV['REL_INFO'])) {
            echo '<td align="center"><a target="_blank" href="' . sprintf($_ENV['REL_INFO'], $ajfsp_link) . '"><i class="fa fa-info-circle text-rimary"></i></a></td>';
        }

		echo "<td nowrap class=\"rigt\">"
			.subs::sizeformat($cur_search['SIZE'])
			."</td>\n";
		//anzahl der ergebnisse
		echo "<td class=\"right\">"
			.$cur_search['phpaj_COUNT']
			."\n</td>";
		//ajfsp-link zu datei
		echo "<td><a class='btn btn-success' href=\"?site=search&link=".$ajfsp_link."\">download</a></td></tr>\n\n";
	}
}

echo "</table></div></div>";
        
//link fuer alle ergebnisse
if(!empty($Search->cache['SEARCH'])){
	//links fuer die einzelnen suchen
	foreach(array_keys($Search->cache['SEARCH']) as $searchid){
		echo'<div class="tab-pane fade" id="search-' . $searchid . '" role="tabpanel" aria-labelledby="search-' . $searchid . '">';
      
			if($searchid !=="alles"){
	
	$current_search=&$Search->cache['SEARCH'][$searchid];
	if(($current_search['SUMSEARCHES']+$current_search['OPENSEARCHES'])>0){
		$current_search_percent=(($current_search['SUMSEARCHES']*100)/
			($current_search['SUMSEARCHES']+$current_search['OPENSEARCHES']));
			$balken = round($current_search_percent,2);
			
			$details = $current_search['SUMSEARCHES']."/".($current_search['SUMSEARCHES']+$current_search['OPENSEARCHES']);
		echo'<div class="progress mb-3">
                <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: '.$balken.'%" aria-valuenow="'.$fortstritt.'" aria-valuemin="0" aria-valuemax="100">
                	'.$balken.' %
                </div>
              </div>';
		
	}
}


echo'<div class="table-responsive">
			  <table class="table table-striped">';

//Sortieren
if(!empty($Search->cache['SEARCHENTRY'])){
if(empty($_GET['sort'])) $_GET['sort']="count";
$searchsort=$Search->sortieren($_GET['sort']);
}

//tabellenueberschriften
echo "<tr>
		<th>".$lang->Search->name."
		</th>";

if(!empty($_ENV['REL_INFO'])) {
    echo '<th width="16" align="center">
    		<i class="fa fa-info-circle"></i>
    	  </th>';
}

echo "<th>".$lang->Search->size."</th>
<th>".$lang->Search->counter."</th>
<th>&nbsp;</th></tr>";

//suchergebnisse anzeigen
if(!empty($Search->cache['SEARCHENTRY'])){
	$result_counter=500;
	foreach(array_keys($searchsort) as $a ){
		$cur_search =& $Search->cache['SEARCHENTRY'][$a];
		//pruefen, ob ergebnis zu suche gehrt
		if($searchid!=="alles"
			&& $cur_search['SEARCHID'] != $searchid) continue;
		$result_counter--;
		if($result_counter<0 && empty($_GET['nolimit'])){
			//nach 500 ergebnissen den rest weglassen, wenn nicht anders gewuenscht
			echo "<tr><th colspan=\"4\"><a href=\"".$_SERVER['PHP_SELF']."?site=search&searchid="
				.$_GET['searchid']."&amp;nolimit=1&amp;".SID."\">"
				.$lan['NO_LIMIT']."</a></th></tr>";
			break;
		}
		//anzeige aller namen + anzahl
		$sort_names=array();
			foreach(array_keys($cur_search['FILENAME']) as $b){
				$sort_names["$b"]=$cur_search['FILENAME'][$b]['USER'];
			}
		arsort($sort_names,SORT_NUMERIC);
		$names=array_keys($sort_names);
		echo "<tr><td>\n<a href=\"javascript:toggleinfo($a,'";
		foreach($names as $c){
			echo $sort_names["$c"]."/".addslashes(htmlspecialchars($c))."|";
		}
		echo $cur_search['CHECKSUM']."/".$cur_search['SIZE'];
		echo "')\"><i class='fa fa-angle-down text-primary'></i></a>\n";
		//download starten
		$ajfsp_link="ajfsp://file|".addslashes(htmlspecialchars($names[0]))."|"
			.$cur_search['CHECKSUM']."|".$cur_search['SIZE']."/";
		echo "<a href=\"javascript:dllink('".$ajfsp_link
			."');\" title=\"Download\">\n".htmlspecialchars($names[0])."</a>";
		echo "<br /><div id=\"infobox_$a\" class=\"infobox\"></div></td>\n";
		//dateigr��e

        if (!empty($_ENV['REL_INFO'])) {
            echo '<td align="center"><a target="_blank" href="' . sprintf($_ENV['REL_INFO'], $ajfsp_link) . '"><i class="fa fa-info-circle text-rimary"></i></a></td>';
        }

		echo "<td class=\"rigt\">"
			.subs::sizeformat($cur_search['SIZE'])
			."</td>\n";
		//anzahl der ergebnisse
		echo "<td class=\"right\">"
			.$cur_search['phpaj_COUNT']
			."\n</td>";
		//ajfsp-link zu datei
		echo "<td><a href=\"".$ajfsp_link."\">ajfsp-link</a></td></tr>\n\n";
	}
}

echo "</table></div></div>";
        
}}
echo'</div></div></div>';