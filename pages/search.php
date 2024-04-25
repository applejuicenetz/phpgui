<?php

use appleJuiceNETZ\appleJuice\Search;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$Search = new Search();
$template = new template();

//Language
$language = Kernel::getLanguage();
$lang = $language->translate();

if(empty($_GET['searchid'])) $_GET['searchid']="alles";
	
echo "<script type=\"text/javascript\">
<!--
function dllink(ajfsplink){
	parent.oben.document.linkform.ajfsp_link.value=ajfsplink;
	parent.oben.document.linkform.showlinkpage.value=0;
	parent.oben.document.linkform.submit();
}

function toggleinfo(id,items){
	var infobox=document.getElementById('infobox_'+id);
	if(infobox.style.display=='block'){
		infobox.style.display='none';
	}else{
		if(infobox.firstChild==null){
			var zeilen=items.split('|');
			var linkinfo=zeilen.pop().split('/');
			infobox.appendChild(document.createTextNode('"
				.addslashes($lang->Search->know_filename)
				.":'));
			infobox.appendChild(document.createElement('br'));
			while(zeilen.length>0){
				var nameinfo=zeilen.shift().split('/');
				infobox.appendChild(
					document.createTextNode('['+nameinfo[0]+'x] '));
				var ajlink=document.createElement('a');
				ajlink.setAttribute('href',
					\"javascript:dllink('ajfsp://file|\"
					+nameinfo[1].replace(/\'/g,\"\\\\'\")+\"|\"
					+linkinfo.join('|')+\"/\\')\");
				ajlink.appendChild(document.createTextNode(nameinfo[1]));
				infobox.appendChild(ajlink);
				infobox.appendChild(document.createElement('br'));
			}
		}
		infobox.style.display='block';
	}
}
//-->
</script>";
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
//Searchcontent
	//Suchformular
	echo'<div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="align-left">
                                	<form action="?site=search&'.SID.'" method="post">
                                		<input type="text" size="40" name="searchstring">
                                		<button type="submit" class="btn btn-primary">'.$lang->Search->button.'</button>
                                	</form><br>
                                </div>
                            </div>
                        </div>
                    </div>';

echo'<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="'.$active_all.'">
                                <a href="#searchid_all" data-toggle="tab">
                                    '.$lang->Search->all.' ('.$Search->cache['SEARCHENTRY_count'].')
                                    <button type="reset" class="btn btn-danger btn-xs" onclick="location.href=\'?site=search&deleteall=1\'">
                                    	<i class="fa fa-trash"></i>
                                    </button>
                                </a>
                            </li>
                            
                            ';

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
			$button = '<button type="reset" class="btn btn-warning btn-xs" onclick="location.href=\'?site=search&cancelid='.$b.'\'">
                                    	<i class="fa fa-close"></i>
                                    </button>';	
		}else{
			$button = '<button type="reset" class="btn btn-danger btn-xs" onclick="location.href=\'?site=search&deleteid='.$b.'\'">
                                    	<i class="fa fa-trash"></i>
                                    </button>';
		}
		//name der suche + zahl der ergebnisse
		echo'<li role="presentation" class="'.$active.'">
                <a href="#searchid_'.$b.'" data-toggle="tab">
                    '.$Search->cache['SEARCH'][$b]['SEARCHTEXT'].' ('.$Search->cache['SEARCH'][$b]['phpaj_FOUNDFILES'].')
                    '.$button.'
                </a>
            </li>';
		}
}
echo'</ul>
        <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active" id="searchid_all">';echo'<div class="table-responsive">
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
        
//link fuer alle ergebnisse
if(!empty($Search->cache['SEARCH'])){
	//links fuer die einzelnen suchen
	foreach(array_keys($Search->cache['SEARCH']) as $searchid){
		echo'<div role="tabpanel" class="tab-pane fade in" id="searchid_'.$searchid.'">';
			if($searchid !=="alles"){
	
	$current_search=&$Search->cache['SEARCH'][$searchid];
	if(($current_search['SUMSEARCHES']+$current_search['OPENSEARCHES'])>0){
		$current_search_percent=(($current_search['SUMSEARCHES']*100)/
			($current_search['SUMSEARCHES']+$current_search['OPENSEARCHES']));
			$balken = round($current_search_percent,2);
			$details = $current_search['SUMSEARCHES']."/".($current_search['SUMSEARCHES']+$current_search['OPENSEARCHES']);
		echo'<div class="progress mt-3">
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
echo'</div>';