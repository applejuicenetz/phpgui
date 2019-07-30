<?php
session_start();
include_once "subs.php";
include_once "classes/class_search.php";
$Search = new Search();
$lang =& $_SESSION['language']['SEARCH'];

echo writehead('Search');

if(empty($_GET['searchid'])) $_GET['searchid']="alles";
echo "<meta http-equiv=\"refresh\" content=\""
	.$_SESSION['reloadtime']['search']."; URL=".$_SERVER['PHP_SELF']."?searchid="
	.$_GET['searchid']."&amp;".SID."\" />";    //neu laden
echo $_SESSION['stylesheet'];
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
				.addslashes($lang['KNOWN_FILENAMES'])
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
echo '</head><body>';

$action_echo='';
//suchanfrage an core uebergeben
if(!empty($_POST['searchstring'])){
	if(get_magic_quotes_gpc())
		$_POST['searchstring']=stripslashes($_POST['searchstring']);
	$_POST['searchstring']=trim($_POST['searchstring']);
	$Search->start($_POST['searchstring']);
}

//suche abbrechen
if(!empty($_GET['cancelid'])){
	$Search->cancel($_GET['cancelid']);
}

echo "<form action=\"".$_SERVER['PHP_SELF']."?".SID."\" method=\"post\">";
echo "<input size=\"50\" name=\"searchstring\" /> ";
echo "<input type=\"submit\" value='"
	.$lang['SEARCH']."' />";
echo "</form>\n";

$Search->refresh_cache();

echo $_SESSION['language']['GENERAL']['TIME'].": ".$Search->time();;
echo " (<a href=\"javascript: window.location.href='".$_SERVER['PHP_SELF']."?searchid="
	.$_GET['searchid']."&amp;".SID."'\">"
	.$_SESSION['language']['GENERAL']['REFRESH']
	."</a>)<br />\n";
echo "<table width=\"100%\">\n";

//suche loeschen
if(!empty($_GET['deleteid'])){
	$action_echo .= $Search->delete($_GET['deleteid']);
}

//alle ergebnisse loeschen
if(!empty($_GET['deleteall'])){
	$action_echo .= $Search->delete_all();
}

echo "<tr><td colspan=\"4\">\n";
echo "<span";
if($_GET['searchid']==="alles"){
	echo " class='search_selected'>";
}else{
	echo " class='search'>";
}

$Search->process_results();

//link fuer alle ergebnisse
echo "<a href=\"".$_SERVER['PHP_SELF']."?searchid=alles&amp;".SID."\">"
	.$lang['ALL_RESULTS']."("
	.$Search->cache['SEARCHENTRY_count']
	.")</a><a href=\"".$_SERVER['PHP_SELF']."?deleteall=1&amp;"
	.SID."\" title=\"Delete all\">"
	."<img border=\"0\" src=\"../style/".$_SESSION['search_delete_icon']
	."\" alt='delete' /></a></span>\n";
echo "&nbsp;";
if(!empty($Search->cache['SEARCH'])){
	//links fuer die einzelnen suchen
	foreach(array_keys($Search->cache['SEARCH']) as $b){
		echo "<span";
		//suche ausgewaehlt, oder nicht?
		if(!empty($_GET['searchid']) && $b==$_GET['searchid']){
			echo " class='search_selected'>";
		}else{
			echo " class='search'>";
		}
		//name der suche + zahl der ergebnisse
		echo "<a href=\"".$_SERVER['PHP_SELF']."?searchid=".$b."&amp;".SID."\">"
			.$Search->cache['SEARCH'][$b]['SEARCHTEXT']
			."(".$Search->cache['SEARCH'][$b]['phpaj_FOUNDFILES']
			.")</a>";
		//icon zum abbrechen/loeschen der suche
		if($Search->cache['SEARCH'][$b]['RUNNING']==="true"){
			echo "<a href=\"".$_SERVER['PHP_SELF']."?cancelid=".$b
				."&amp;".SID."\" title=\"Cancel search\">"
				."<img border=\"0\" src=\"../style/"
				.$_SESSION['search_cancel_icon']."\" alt='cancel' /></a>";
		}else{
			echo "<a href=\"".$_SERVER['PHP_SELF']."?deleteid=".$b
				."&amp;".SID."\" title=\"Delete search\">"
				."<img border=\"0\" src=\"../style/"
				.$_SESSION['search_delete_icon']."\" alt='delete' /></a>";
		}
		echo "</span>\n";
		echo "&nbsp;";
	}
}
echo "</td></tr>\n";

if($_GET['searchid']!=="alles"){
	echo "<tr><td colspan=\"4\">";
	$current_search=&$Search->cache['SEARCH'][$_GET['searchid']];
	if(($current_search['SUMSEARCHES']+$current_search['OPENSEARCHES'])>0){
		$current_search_percent=(($current_search['SUMSEARCHES']*100)/
			($current_search['SUMSEARCHES']+$current_search['OPENSEARCHES']));
		echo "<div style=\"width:".round($current_search_percent,0)
			."%;\" class=\"search_selected\" align=\"center\">"
			.number_format($current_search_percent,2)."% ("
			.$current_search['SUMSEARCHES']."/"
			.($current_search['SUMSEARCHES']+$current_search['OPENSEARCHES'])
			.")</div>";
	}
	echo "</td></tr>";
}

//Sortieren
if(!empty($Search->cache['SEARCHENTRY'])){
if(empty($_GET['sort'])) $_GET['sort']="count";
$searchsort=$Search->sortieren($_GET['sort']);
}

//tabellenueberschriften
echo "<tr>
<th><a href=\"".$_SERVER['PHP_SELF']."?sort=name&amp;searchid=".$_GET['searchid']
	."&amp;".SID."\">"
	.$lang['NAME']."</a></th>
<th><a href=\"".$_SERVER['PHP_SELF']."?sort=size&amp;searchid="
	.$_GET['searchid']."&amp;".SID."\">"
	.$lang['SIZE']."</a></th>
<th><a href=\"".$_SERVER['PHP_SELF']."?sort=count&amp;searchid=".$_GET['searchid']
	."&amp;".SID."\">"
	.$lang['COUNT']."</a></th>
<th>&nbsp;</th></tr>";

//suchergebnisse anzeigen
if(!empty($Search->cache['SEARCHENTRY'])){
	$result_counter=500;
	foreach(array_keys($searchsort) as $a ){
		$cur_search =& $Search->cache['SEARCHENTRY'][$a];
		//pruefen, ob ergebnis zu suche geh�rt
		if($_GET['searchid']!=="alles" 
			&& $cur_search['SEARCHID']!==$_GET['searchid']) continue;
		$result_counter--;
		if($result_counter<0 && empty($_GET['nolimit'])){
			//nach 500 ergebnissen den rest weglassen, wenn nicht anders gewuenscht
			echo "<tr><th colspan=\"4\"><a href=\"".$_SERVER['PHP_SELF']."?searchid="
				.$_GET['searchid']."&amp;nolimit=1&amp;".SID."\">"
				.$lang['NO_LIMIT']."</a></th></tr>";
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
		echo "')\"><img border=\"0\" src=\"../style/"
			.$_SESSION['search_info_icon']."\" alt='info' /></a>\n";
		//download starten
		$ajfsp_link="ajfsp://file|".addslashes(htmlspecialchars($names[0]))."|"
			.$cur_search['CHECKSUM']."|".$cur_search['SIZE']."/";
		echo "<a href=\"javascript:dllink('".$ajfsp_link
			."');\" title=\"Download\">\n".htmlspecialchars($names[0])."</a>";
		echo "<br /><div id=\"infobox_$a\" class=\"infobox\"></div></td>\n";
		//dateigr��e
		echo "<td class=\"right\">"
			.sizeformat($cur_search['SIZE'])
			."</td>\n";
		//anzahl der ergebnisse
		echo "<td class=\"right\">"
			.$cur_search['phpaj_COUNT']
			."\n</td>";
		//ajfsp-link zu datei
		echo "<td><a href=\"".$ajfsp_link."\">ajfsp-link</a></td></tr>\n\n";
	}
}

echo "</table>";
echo $action_echo;
echo "</body>
</html>";
