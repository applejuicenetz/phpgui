<?php
require_once "_classes/subs.php";
require_once "_classes/downloads.php";
require_once "_classes/icons.php";

$icon_img =new Icons();
$Downloadlist = new Downloads();
$lang =& $_SESSION['language']['DOWNLOADS'];

if(empty($_GET['sort'])) $_GET['sort']="name";

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
		id+'&action_value=' + newname + '&';
}

function dlparts(id){
	var ajpartinfo=window.open('dl_parts.php?dl_id='+id+'&"
		.SID."','ajdlparts',
		'width=540,height=300,left=10,top=10,dependent=yes,scrollbars=no');
	ajpartinfo.focus();
}

function dlusers(id){
	var ajdlinfo=window.open('index.php?site=dl_users&dl_id='+id+'&"
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
				zelle.style.backgroundColor='#CEE3F6';
			zelle=zelle.nextSibling;
		}
		document.getElementById('dlcheck_'+id).checked=true;
	}
}

function dlaction(action){
	var dlline='site=downloads&action='+action;
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
	window.location.href='".$_SERVER['PHP_SELF']."?' + dlline+'&';
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

</script>";

//pause, fortsetzen, abbrechen, pdl setzen...
	$action_echo='';
	if(!empty($_GET['action'])){
		if(!empty($_GET['dl_id'])){
			if(empty($_GET['action_value'])) $_GET['action_value']="";
		$action_echo = $Downloadlist->action($_GET['action'],
			$_GET['dl_id'],$_GET['action_value']);
			$Downloadlist->message($action_echo);
	}
}

$Downloadlist->refresh_cache();

echo "<form action=\"\" name=\"dl_form\" onsubmit=\"return false\">";


echo'<div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="align-right">
                                	<nav aria-label="Page navigation">
                <ul class="pagination justify-content-end">
                  <li class="page-item">';
                    echo"<a class=\"btn-warning page-link\" onclick=\"dlaction('pausedownload')\"><i class=\"text-warning material-icons\">pause_circle_outline</i></a>";
                  echo'</li>
                  <li class="page-item">';
                    echo"<a class=\"page-link\" onclick=\"dlaction('resumedownload')\"><i class=\"text-success material-icons\">play_circle_outline</i></a>";
                  echo'</li>
                  <li class="page-item">';
                    echo"<a class=\"page-link\" onclick=\"dlaction('canceldownload')\"><i class=\"text-danger material-icons\">close</i></a>";
                  echo'</li>
                  <li class="page-item">';
                    echo"<a class=\"page-link\" onclick=\"dlaction('settargetdir')\"><i class=\"material-icons\">folder_open</i></a>";
                  echo'</li>
                  <li class="page-item">
                    <a class="page-link" href="index.php?site=downloads&action=cleandownloadlist&dl_id=1"><i class="material-icons text-danger">delete_sweep</i></a>
                  </li>
                </ul>
              </nav>';
//Tabellenüberschrift
echo'<div class="table-responsive">
			  <table class="table table-striped">
				<thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Quellen</th>
                    <th scope="col">Dateiennamen</th>
                    <th scope="col">Status</th>
                    <th scope="col">Geschwindigkeit</th>
                    <th scope="col">PDL</th>
                    <th scope="col">Gr&ouml;ße</th>
                    <th scope="col">Rest</th>
                    <th scope="col">Fortschritt</th>
                  </tr>
                </thead>
                <tbody>
                
';	

$spaltenzahl=9;
if(!empty($_ENV['REL_INFO'])) {
    $spaltenzahl++;
}

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
			$current_download = $Downloadlist->download($a);
			echo "<tr id=\"zeile_$a\">\n";
			//checkbox zur auswahl
			echo "<td class=\"form-group\">\n<script type=\"text/javascript\">\n<!--\n"
				."dl_names[$a]='".addslashes($current_download['FILENAME'])."';\n"
				."dl_pdl[$a]=".((($current_download['POWERDOWNLOAD'])+10)/10).";\n"
				."dl_ids[$a]=0;\n"
				."dl_subdirs[$a]=$subdircounter;\n"
				."//-->\n</script>\n";
			echo "<input type=\"checkbox\" id=\"dlcheck_$a\""
				." onclick=\"change($a);\" /></td>\n";
			//quellenzahl (link zu dl details)
				echo "<td class=\"right\">"
					."<a href=\"javascript:dlusers($a)\" title=\"Mehr Info\">"
					.($current_download['phpaj_quellen_queue']
						+$current_download['phpaj_quellen_dl'])
					."/".$current_download['phpaj_quellen_gesamt']
					." (".$current_download['phpaj_quellen_dl'].")</a></td>\n";
			//Dateiname
			echo "<td id=\"nametd_$a\">"
				."<a href=\"javascript:rename($a)\" title=\"".$lang['RENAME']."\">";
			echo htmlspecialchars($current_download['FILENAME'])."</a></td>\n";

           

            //status
			echo "<td>".$Downloadlist->status($current_download['phpaj_STATUS'])."</td>\n";
			//geschwindigkeit
            echo "<td class=\"right\">"
				.sizeformat($current_download['phpaj_dl_speed'])
				."/s</td>\n";
			//pdl wert
			echo "<td class=\"right\">"
				.((($current_download['POWERDOWNLOAD'])+10)/10)."</td>\n";
			//groesse
			echo "<td>".sizeformat($current_download['SIZE'])."</td>";
			//Rest
			
			$fortschritt=&$current_download['phpaj_DONE'];
			$balken = round($fortschritt, 2);
			$rest= $current_download["phpaj_REST"];
			$rest = sizeformat($rest);
			echo'<td>'.$rest;
			if(!empty($current_download['phpaj_dl_speed'])){
				$restzeit=$current_download['phpaj_REST']/$current_download['phpaj_dl_speed'];
				echo "<br>";
				$stunden=$restzeit/3600;
				if($stunden<24)
					printf("%02d:%02d:%02d",$stunden,($restzeit%3600)/60,$restzeit%60);
				else
					printf("%.1fd",$stunden/24);
			}
			echo'</td><td>';
							echo '<div class="progress mt-3">
                <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: '.$balken.'%" aria-valuenow="'.$fortstritt.'" aria-valuemin="0" aria-valuemax="100">
                '.$balken.' %</div>
              </div></td><td>';
            
			
			echo "</td>";
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
	."</th></tr></table></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>";


//pdl
echo '<div class="panel panel-default">
            <div class="panel-body">
              <h5 class="card-title">Powerdownload setzen</h5>';
echo "<a href=\"javascript:dec_pdl()\"><i class=\"material-icons\">remove</i></a>&nbsp;"
	."<input size=\"4\" id=\"pdl\" name=\"pdl\" value=\"1.0\" />"
	."&nbsp;<a href=\"javascript:inc_pdl()\"><i class=\"material-icons\">add</i></a>&nbsp;"
	."<input type=\"button\"  class='btn btn-primary' value='".$lang['SET_PDL']
	."' onclick=\"dlaction('setpowerdownload')\" />"
	."\n";
echo "</div></div>";

echo "</form>";
