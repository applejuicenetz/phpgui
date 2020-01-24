<?php
session_start();
include_once "subs.php";
include_once "classes/class_downloads.php";
include_once "classes/class_icons.php";

$icon_img =new Icons();
$Downloadlist = new Downloads();
$lang =& $_SESSION['language']['DOWNLOADS'];

if(empty($_GET['sort'])) $_GET['sort']="name";

echo writehead('Downloads');
echo "<meta http-equiv=\"refresh\" content=\""
	.$_SESSION['reloadtime']['downloads']."; URL=".$_SERVER['PHP_SELF']."?"
	.SID."&amp;sort=".$_GET['sort']."\" />";
echo $_SESSION['stylesheet'];
echo "<script>
var dl_ids = [];		//download ausgewaehlt?
var dl_names = [];		//download namen
var dl_pdl = [];		//momentaner pdl-wert
var dl_subdirs = [];	//unterverzeichnissnummern

var renameopen = 0;
var renamelink;

function rename(id){
	if(renameopen!=0){
		var zelle_alt=document.getElementById('nametd_'+renameopen);
		while(zelle_alt.firstChild!=null){
			zelle_alt.removeChild(zelle_alt.firstChild);
		}
		zelle_alt.appendChild(renamelink);
	}
	var zelle=document.getElementById('nametd_'+id);
	renamelink=zelle.firstChild.cloneNode(true);
	var nameinput=document.createElement('input');
		nameinput.setAttribute('id', 'newname_'+id);
		nameinput.setAttribute('value', dl_names[id]);
		nameinput.setAttribute('size', dl_names[id].length);
	zelle.replaceChild(nameinput, zelle.firstChild);
	var okbutton=document.createElement('input');
		okbutton.setAttribute('type', 'button');
		okbutton.setAttribute('value', 'OK');
		okbutton.onclick=new Function('dorename('+id+');'); //scheiss ie
	zelle.appendChild(okbutton);
	renameopen=id;
}

function dorename(id){
	var newname=encodeURIComponent(
		eval('document.dl_form.newname_'+id+'.value'));
	window.location.href='".$_SERVER['PHP_SELF']."?action=renamedownload&dl_id[0]='+
		id+'&action_value=' + newname + '&".SID."';
}

function dlparts(id){
	var ajpartinfo=window.open('dl_parts.php?dl_id='+id+'&"
		.SID."','ajdlparts',
		'width=540,height=300,left=10,top=10,dependent=yes,scrollbars=no');
	ajpartinfo.focus();
}

function dlusers(id){
	var ajdlinfo=window.open('dl_users.php?dl_id='+id+'&"
		.SID."','ajdlinfo',
		'width=1000,height=600,left=10,top=10,dependent=yes,scrollbars=yes');
	ajdlinfo.focus();
}";


echo"
function inc_pdl(){
	if(document.dl_form.pdl.value==1){
		document.dl_form.pdl.value='2.2';
	}else if(document.dl_form.pdl.value<=49.9 
			&& document.dl_form.pdl.value>1){
			var neuer_pdlwert=(document.dl_form.pdl.value*1)+0.1;
			document.dl_form.pdl.value=neuer_pdlwert.toFixed(1);
	}else{
			document.dl_form.pdl.value='1.0';
	}
}
	
function dec_pdl(){
	if(document.dl_form.pdl.value<2.3){
		document.dl_form.pdl.value='1.0';
	}else if(document.dl_form.pdl.value>50){
			document.dl_form.pdl.value='50.0';
	}else{
			var neuer_pdlwert=(document.dl_form.pdl.value*1)-0.1;
			document.dl_form.pdl.value=neuer_pdlwert.toFixed(1);
	}
}

function change(id){
	var dl_zeile=document.getElementById('zeile_'+id);
	var zelle=dl_zeile.firstChild;
	if(dl_ids[id]==1){
		dl_ids[id]=0;
		document.dl_form.pdl.value='1.0';
		while(zelle!=null){
			if(zelle.nodeName=='TD')
				zelle.style.backgroundColor='';
			zelle=zelle.nextSibling;
		}
		document.getElementById('dlcheck_'+id).checked=false;
	}else{
		dl_ids[id]=1;
		document.dl_form.pdl.value=dl_pdl[id];
		while(zelle!=null){
			if(zelle.nodeName=='TD')
				zelle.style.backgroundColor='".$_SESSION['selected_td_color']."';
			zelle=zelle.nextSibling;
		}
		document.getElementById('dlcheck_'+id).checked=true;
	}
}

function dlaction(action){
	var dlline='action='+action;
	var counter=-1;
	var fragetext='".addslashes($lang['CANCEL_QUESTION'])."';
	for (var v in dl_ids){
		if(dl_ids[v]==0) continue;
		counter++;
		dlline+='&dl_id['+counter+']=' + v;
		fragetext+='\\n'+dl_names[v];
	}
	if(action=='settargetdir'){
		var newname=prompt('".$lang['TARGETDIR'].":','');
		if(newname==null) return;
		dlline+='&action_value='+encodeURIComponent(newname);
	}
	if(action=='setpowerdownload')
		dlline+='&action_value='+document.dl_form.pdl.value;
	if(action=='canceldownload' && !confirm(fragetext))
		return;
	window.location.href='".$_SERVER['PHP_SELF']."?' + dlline+'&".SID."';
}

function select_all(moep){
	for(var v in dl_ids){
		if(dl_ids[v]==moep) change(v);
	}
}

function select_sub(subid, moep){
	for(var v in dl_ids){
		if(dl_subdirs[v]==subid && dl_ids[v]==moep) change(v);
	}
}

function togglesubdir(dircounter){
	var bild=document.getElementById('img_'+dircounter);
	var zeilen=new Array();
	for (var v in dl_subdirs){
		if(dl_subdirs[v] != dircounter) continue;
		var dl_zeile=document.getElementById('zeile_'+v);
		zeilen.push(dl_zeile);
	}
	var z=zeilen.shift();
	if(z.style.display != 'none'){
		while(z!=null){
			z.style.display='none';
			z=zeilen.shift();}
		bild.setAttribute('src','../style/".$_SESSION['plus_icon']."');
	}else{
		while(z!=null){
			z.style.display='';
			z=zeilen.shift();}
		bild.setAttribute('src','../style/".$_SESSION['minus_icon']."');
	}
}

</script>
</head><body>";

//pause, fortsetzen, abbrechen, pdl setzen...
	$action_echo='';
	if(!empty($_SESSION['phpaj']['autocleandownloadlist']))
		$Downloadlist->action("cleandownloadlist");
	if(!empty($_GET['action'])){
		if(!empty($_GET['dl_id'])){
			if(empty($_GET['action_value'])) $_GET['action_value']="";
		$action_echo = $Downloadlist->action($_GET['action'],
			$_GET['dl_id'],$_GET['action_value']);
	}
}

$Downloadlist->refresh_cache();

// Zeitpunkt, an dem die daten vom core geholt wurden + reload link
	echo $_SESSION['language']['GENERAL']['TIME'].": ".$Downloadlist->time();
	echo " (<a href=\"javascript: window.location.href='"
		.$_SERVER['PHP_SELF']."?"
		.SID."&amp;sort=".$_GET['sort']."'\">"
		.$_SESSION['language']['GENERAL']['REFRESH']."</a>)<br />\n";

echo "<form action=\"\" name=\"dl_form\" onsubmit=\"return false\">";

$spaltenzahl=8;
if(empty($_SESSION['phpaj']['savebw'])) $spaltenzahl++;

//tabellenanfang + ueberschriften
	echo "<table width=\"100%\">\n";
	echo "<tr>
		<th width=\"10\">&nbsp;</th>
		<th><a href=\"".$_SERVER['PHP_SELF']."?sort=sources&amp;".SID."\">"
		.$lang['SOURCES']."</a></th>
		<th><a href=\"".$_SERVER['PHP_SELF']."?sort=name&amp;".SID."\">"
		.$lang['FILENAME']."</a></th>";
		echo "<th><a href=\"".$_SERVER['PHP_SELF']."?sort=status&amp;".SID."\">"
		.$lang['STATUS']."</a></th>\n";
	if(empty($_SESSION['phpaj']['savebw']))
		echo "\t\t<th><a href=\"".$_SERVER['PHP_SELF']."?sort=speed&amp;"
			.SID."\">".$lang['SPEED']."</a></th>\n";
	echo "\t\t<th><a href=\"".$_SERVER['PHP_SELF']."?sort=pdl&amp;".SID."\">"
		.$lang['POWERDOWNLOAD_SHORT']."</a></th>
		<th><a href=\"".$_SERVER['PHP_SELF']."?sort=size&amp;".SID."\">"
		.$lang['SIZE']."</a></th>
		<th><a href=\"".$_SERVER['PHP_SELF']."?sort=rest&amp;".SID."\">"
		.$lang['REMAINING']."</a></th>
		<th width=\"100\"><a href=\"".$_SERVER['PHP_SELF']."?sort=done&amp;".SID."\">"
		.$lang['FINISHED']."</a></th></tr>\n";


ksort($Downloadlist->subdirs);
$subdircounter=0;

//alle downloads zeigen
foreach(array_keys($Downloadlist->subdirs) as $subdir){
	$subdircounter++;
	$downloadids=$Downloadlist->ids($_GET['sort'],$subdir); //ids der downloads sortiert holen
	if(!empty($subdir))
		//Unterverzeichnis
		echo "<tr><td colspan=\"$spaltenzahl\">"
		."<a href=\"javascript:togglesubdir($subdircounter)\">"
		."<img id=\"img_$subdircounter\" "
		."src=\"../style/".$_SESSION['minus_icon']."\" border=\"0\" alt=\"\" />"
		."&nbsp;&nbsp;<b>".htmlspecialchars($subdir)."</b> (".count($downloadids).")</a>\n"
		."<span style=\"margin-left:5px\">"
		.$_SESSION['language']['GENERAL']['SELECT'].":</span> "
		."<a href=\"javascript:select_sub($subdircounter, 0);\">"
		.$_SESSION['language']['GENERAL']['ALL']."</a>, "
		."<a href=\"javascript:select_sub($subdircounter, 1);\">"
		.$_SESSION['language']['GENERAL']['NONE']."</a></td></tr>\n";
	foreach(array_keys($downloadids) as $a){
			//sieht doch etwas uebersichtlicher aus :)
			$current_download =& $Downloadlist->download($a);
			echo "<tr id=\"zeile_$a\">\n";
			//checkbox zur auswahl
			echo "<td>\n<script type=\"text/javascript\">\n<!--\n"
				."dl_names[$a]='".addslashes($current_download['FILENAME'])."';\n"
				."dl_pdl[$a]=".((($current_download['POWERDOWNLOAD'])+10)/10).";\n"
				."dl_ids[$a]=0;\n"
				."dl_subdirs[$a]=$subdircounter;\n"
				."//-->\n</script>\n";
			echo "<input type=\"checkbox\" id=\"dlcheck_$a\""
				." onclick=\"change($a);\" /></td>\n";
			//quellenzahl (link zu dl details)
			if(empty($_SESSION['phpaj']['savebw'])){
				echo "<td class=\"right\">"
					."<a href=\"javascript:dlusers($a)\" title=\"Mehr Info\">"
					.($current_download['phpaj_quellen_queue']
						+$current_download['phpaj_quellen_dl'])
					."/".$current_download['phpaj_quellen_gesamt']
					." (".$current_download['phpaj_quellen_dl'].")</a></td>\n";
			}else{
				if(empty($Downloadlist->cache['IDS']['DOWNLOADID']
						[$current_download['ID']]['USERID'])){
					$current_download['phpaj_quellen_gesamt']=0;
				}else{
					$current_download['phpaj_quellen_gesamt']=count(
						$Downloadlist->cache['IDS']['DOWNLOADID']
						[$current_download['ID']]['USERID']);
				}
				echo "<td class=\"right\">?/"
					.$current_download['phpaj_quellen_gesamt']." (?)</td>\n";
			}
			//Dateiname
			echo "<td id=\"nametd_$a\">"
				."<a href=\"javascript:rename($a)\" title=\"".$lang['RENAME']."\">";
			echo htmlspecialchars($current_download['FILENAME'])."</a></td>\n";
			//status
			echo "<td>".$_SESSION['language']['DLSTATUS']
				['STATUS_'.$current_download['phpaj_STATUS']]."</td>\n";
			//geschwindigkeit
			if(empty($_SESSION['phpaj']['savebw']))
				echo "<td class=\"right\">"
				.sizeformat($current_download['phpaj_dl_speed'])
				."/s</td>\n";
			//pdl wert
			echo "<td class=\"right\">"
				.((($current_download['POWERDOWNLOAD'])+10)/10)."</td>\n";
			//groesse
			echo "<td>".sizeformat($current_download['SIZE'])."</td>";
			//Rest
			echo "<td>";
			echo sizeformat($current_download['phpaj_REST']);
			if(!empty($current_download['phpaj_dl_speed'])){
				$restzeit=$current_download['phpaj_REST']/$current_download['phpaj_dl_speed'];
				echo "|";
				$stunden=$restzeit/3600;
				if($stunden<24)
					printf("%02d:%02d:%02d",$stunden,($restzeit%3600)/60,$restzeit%60);
				else
					printf("%.1fd",$stunden/24);
			}
			echo "</td>";
			//dl fortschritt
			$fortschritt=&$current_download['phpaj_DONE'];
				echo "<td width=\"100\">";
				echo progressbar($fortschritt,sizeformat($current_download['phpaj_READY']),
					"<a href=\"javascript:dlparts($a)\" title=\"Part Anzeige\">");
				echo "</td>\n";
			echo "</tr>\n";
	}
}
//alle/keine auswaehlen

echo "<tr><th colspan=\"$spaltenzahl\">\n";
echo $_SESSION['language']['GENERAL']['SELECT'].": ";
echo "<a href=\"javascript:select_all(0);\">"
	.$_SESSION['language']['GENERAL']['ALL']."</a>, ";
echo "<a href=\"javascript:select_all(1);\">"
	.$_SESSION['language']['GENERAL']['NONE']."</a>"
	."</th></tr>";

//pause
echo "<tr><td colspan=\"$spaltenzahl\">"
	."<input type=\"button\" value=\"".$lang['PAUSE']
	."\" onclick=\"dlaction('pausedownload')\" />\n";
//resume
echo "<input type=\"button\" value=\"".$lang['RESUME']
	."\" onclick=\"dlaction('resumedownload')\" />\n";
//abbrechen
echo "<input type=\"button\" value=\"".$lang['CANCEL']
	."\" onclick=\"dlaction('canceldownload')\" />\n";
//zielverzeichnis
echo "<input type=\"button\" value=\"".$lang['TARGETDIR']
	."\" onclick=\"dlaction('settargetdir')\" />\n";
//clean
echo "<input type=\"button\" value=\"".$lang['CLEANDOWNLOADLIST']
	."\" onclick=\"dlaction('cleandownloadlist&amp;dl_id=1')\" style=\"margin-left:5px;\" />\n"
	."</td></tr>\n";

//pdl
echo "<tr><td colspan=\"$spaltenzahl\"><label for=\"pdl\">"
	.$lang['POWERDOWNLOAD']."</label>:&nbsp;"
	."<a href=\"javascript:inc_pdl()\"><img src=\"../style/"
	.$_SESSION['plus_icon']."\" border=\"0\" alt=\"+\" /></a>&nbsp;"
	."<a href=\"javascript:dec_pdl()\"><img src=\"../style/"
	.$_SESSION['minus_icon']."\" border=\"0\" alt=\"-\" /></a>"
	." "
	."<input size=\"4\" id=\"pdl\" name=\"pdl\" value=\"1.0\" />"
	."&nbsp;<input type=\"button\" value='".$lang['SET_PDL']
	."' onclick=\"dlaction('setpowerdownload')\" />"
	."</td></tr>\n";
echo "</table><br />";

//meldungen ausgeben (s.o.)
echo $action_echo;

echo "</form>";
echo "</body>
</html>";
