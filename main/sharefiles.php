<?php
session_start();
include_once "subs.php";
include_once "classes/class_share.php";
$lang = $_SESSION['language']['SHARE'];

echo writehead('Sharefiles');
echo $_SESSION['stylesheet'];

echo "\n<script type=\"text/javascript\">
<!--
share_ids = new Array();

function change(id){
	var share_zeile=document.getElementById('zeile_'+id);
	var zelle=share_zeile.firstChild;
	if(share_ids[id]==1){
		share_ids[id]=0;
		while(zelle!=null){
			if(zelle.nodeName=='TD')
				zelle.style.backgroundColor='';
			zelle=zelle.nextSibling;
		}
		document.getElementById('sharecheck_'+id).checked=false;
	}else{
		share_ids[id]=1;
		while(zelle!=null){
			if(zelle.nodeName=='TD')
				zelle.style.backgroundColor='"
				.$_SESSION['selected_td_color']."';
			zelle=zelle.nextSibling;
		}
		document.getElementById('sharecheck_'+id).checked=true;
	}
}

function changeshareprio(){
	var shareline='';
	var counter=-1;
	for (var i in share_ids){
		if(share_ids[i]==0) continue;
		counter++;
		shareline+='&sharefile['+counter+']=' + i;
	}
	//alert(shareline);
	window.location.href='".$_SERVER['PHP_SELF']."?dir=".urlencode($_GET['dir'])
		."'+ shareline + '&sprio=' + document.shareprioform.shareprio.value
		+'&".SID."';
}

function reload(){
	window.location.href='".$_SERVER['PHP_SELF']."?dir=".urlencode($_GET['dir'])
		."&forcereload=1&".SID."';
}

function exportlinks(){
	var shareexpline='';
	var counter=-1;
	for (var i in share_ids){
		if(share_ids[i]==0) continue;
		counter++;
		shareexpline+='&shareexpfile['+counter+']=' + i;
	}
	//alert(shareexpline);
	window.location.href='".$_SERVER['PHP_SELF']."?dir=".urlencode($_GET['dir'])
		."'+ shareexpline+'&".SID."';
}

function toggleinfo(id,lastasked,askcount,searchcount,ajfsp){
	var infobox=document.getElementById('infobox_'+id);
	if(infobox.style.display=='block'){
		infobox.style.display='none';
	}else{
		if(infobox.firstChild==null){
			var text1=document.createTextNode('"
				.addslashes($lang['LASTASKED']).": '+lastasked);
			var text2=document.createTextNode('"
				.addslashes($lang['ASKCOUNT']).": '+askcount);
			var text3=document.createTextNode('"
				.addslashes($lang['SEARCHCOUNT']).": '+searchcount);
			infobox.appendChild(text1);
			infobox.appendChild(document.createElement('br'));
			infobox.appendChild(text2);
			infobox.appendChild(document.createElement('br'));
			infobox.appendChild(text3);
			infobox.appendChild(document.createElement('br'));
			var ajlink=document.createElement('a');
				ajlink.setAttribute('href', 'ajfsp://file|'+ajfsp+'|"
					.$_SESSION['phpaj']['core_source_ip'].":"
					.$_SESSION['phpaj']['core_source_port']."/');
			ajlink.appendChild(document.createTextNode('[source link]'));
			infobox.appendChild(ajlink);
		}
		infobox.style.display='block';
	}
}
	
function selectall(){
	for(var v in share_ids){
		if(share_ids[v]==0) change(v);
	}
}
	
function selectnone(){
	for(var v in share_ids){
		if(share_ids[v]==1) change(v);
	}
}
//-->
</script>";

echo "</head>\n<body>\n";

$Share = new Share();

$Sharelist = $Share;

//prio setzen
if(!empty($_GET['sharefile'])){
	$Sharelist->setpriority($_GET['sharefile'],$_GET['sprio']);
}

if(!empty($_GET['forcereload'])){
	$Sharelist->refresh_cache(0);
}

//sharecache neu laden, falls aelter als 60min
$Sharelist->refresh_cache(60);

if (!empty($_GET['shareexpfile'])) {
    $_SESSION['shareexport'] = [];
    foreach ($_GET['shareexpfile'] as $expid) {
        $shareentry = $Sharelist->get_file($expid);
        $export_currlink = 'ajfsp://file|' . $shareentry['SHORTFILENAME'] . '|' .
            $shareentry['CHECKSUM'] . '|' .
            $shareentry['SIZE'] . '/';
        $testx = array_search($export_currlink, $_SESSION['shareexport']);
        if ($testx !== FALSE) continue;
        array_push($_SESSION['shareexport'], $export_currlink);
    }
}

echo "<form name=\"shareprioform\" action=\"\">\n";
//verzeichnis anzeigen
echo $lang['FILES_IN']." "
	.htmlspecialchars($_GET['dir'])."\n<br /><br />\n";
//dropdown 1-250 fuer prio
echo $lang['PRIORITY']
	.": <select name=\"shareprio\" size=\"1\">\n";
for($i=1;$i<=250;$i++){
	echo "<option value=\"$i\">".$i."</option>";
}
echo "\n</select>\n <input type=\"button\" value=\"".$lang['CHANGE_PRIORITY']
	."\" onclick=\"changeshareprio();\" />\n";
echo " <input type=\"button\" value=\"".$lang['EXPORT']
	."\" onclick=\"exportlinks();\" />\n";
echo " <input type=\"button\" value=\"".$lang['RELOAD']
	."\" onclick=\"reload();\" /><br />\n";
echo "<table width=\"100%\">\n";

$spaltenzahl=4;
echo "<tr><th width=\"20\">&nbsp;</th>
<th>".$lang['FILENAME']."</th>
<th>".$lang['FILESIZE']."</th>
<th>".$lang['PRIORITY']."</th></tr>\n";

//unterverzeichnisse
$dirliste=$Sharelist->directory($_GET['dir']);
foreach($dirliste as $a){
	echo "<tr><td colspan=\"$spaltenzahl\">";
	echo "<a href=\"".$_SERVER['PHP_SELF']."?dir=".rawurlencode($a[0])."&amp;"
		.SID."\">".htmlspecialchars($a[1])."</a>";
	echo "</td></tr>";
}

//dateien anzeigen
foreach($Sharelist->get_fileids($_GET['dir']) as $a){
	$shareentry=&$Sharelist->get_file($a);
	echo "\n<tr id=\"zeile_$a\"><td><input type=\"checkbox\" "
		."id=\"sharecheck_$a\" onclick=\"change($a)\" />"
		."<script type=\"text/javascript\">\n"
		."<!--\n"
		."share_ids[$a]=0;\n"
		."//-->\n</script>\n"
		."</td><td>\n";
	$ajfsp=$shareentry['SHORTFILENAME']."|".$shareentry['CHECKSUM']."|"
		.$shareentry['SIZE'];
	$lastasked=(isset($shareentry['LASTASKED'])) ?
		date("j.n.y - H:i:s",($shareentry['LASTASKED']/1000))
		: "N/A";
	if(!isset($shareentry['ASKCOUNT'])) $shareentry['ASKCOUNT']="N/A";
	if(!isset($shareentry['SEARCHCOUNT'])) $shareentry['SEARCHCOUNT']="N/A";
	echo "<a href=\"javascript:toggleinfo($a,'"
		.$lastasked."','".$shareentry['ASKCOUNT']."','"
		.$shareentry['SEARCHCOUNT']."','".addslashes($ajfsp)."')\">";
	echo "<img border=\"0\" src=\"../style/"
		.$_SESSION['search_info_icon']."\" alt='info' /></a>\n";
	echo "<a href=\"ajfsp://file|".$ajfsp."/\">"
		.$shareentry['SHORTFILENAME']."</a>";
	echo "<br /><div id=\"infobox_$a\" class=\"infobox\"></div>";
	echo "</td>";
	//groesse der datei
	echo "<td class=\"right\">"
		.sizeformat($shareentry['SIZE'])."</td>";
	//shareprio
	echo "<td class=\"right\">".$shareentry['PRIORITY']."</td>";
	echo "</tr>";
}

echo "<tr><th colspan=\"$spaltenzahl\">";
echo $_SESSION['language']['GENERAL']['SELECT'].": ";
echo "<input type=\"button\" value=\""
	.$_SESSION['language']['GENERAL']['ALL']
	."\" onclick=\"selectall();\" /> ";
echo "<input type=\"button\" value=\""
	.$_SESSION['language']['GENERAL']['NONE']
	."\" onclick=\"selectnone();\" /></th></tr>";
echo "</table>";
echo "</form>";
echo strtr($lang['PRIO_SPENT'],array("%spent"=>$Sharelist->spentprio));

echo "</body>
</html>";
