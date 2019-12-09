<?php
session_start();
include_once "subs.php";
include_once "classes/class_core.php";
include_once "classes/class_share.php";

$core = new Core();
$share = new Share();

echo writehead('Share');

echo '<style type="text/css">
select {width:100%;}
</style>';
echo $_SESSION['stylesheet'];
echo "<script type=\"text/javascript\">
<!--
function ShowFiles(dir){
	dir=encodeURIComponent(dir);
	var sharelist=window.open('sharefiles.php?dir='+dir+'&".SID."','ajsharelist',
		'width=720,height=500,left=10,top=10,dependent=yes,scrollbars=yes');
}

function do_setsubs(name, newsub){
	name=encodeURIComponent(name);
	window.location.href='".$_SERVER['PHP_SELF']."?".SID."&setsubs='+name+'&newsub='+newsub;
}

function delshare(name){
	name=encodeURIComponent(name);
	window.location.href='".$_SERVER['PHP_SELF']."?".SID."&share_del='+name;
}

function newshare(){
	var name=encodeURIComponent(document.mainform.new_share.value);
	var subs=document.mainform.new_subs.checked ? 1 : 0;
	window.location.href='".$_SERVER['PHP_SELF']."?".SID."&new_share='+name+'&new_subs='+subs;
}

function share_export(){
    window.location.href = 'shareexport.php';
}

function select_dir(){
	var dirlist=window.open(
		'directory.php?returninput=mainform.new_share.value&amp;".SID."',
		'Dirlist','width=400,height=350,left=10,top=10,dependent=yes,scrollbars=no');
	dirlist.focus();
}

//-->
</script>
</head>
<body>";

//einstellungen fuer unterverzeichnis aendern
if(!empty($_GET['setsubs'])){
	$share->changesub($_GET['setsubs'], $_GET['newsub']);
}

//verzeichnis aus share nehmen
if(!empty($_GET['share_del'])){
	$share->del_share($_GET['share_del']);
}

//verzeichnis sharen
if(!empty($_GET['new_share'])){
	$share->add_share($_GET['new_share'], $_GET['new_subs']);
}

echo "<form action=\"\" name=\"mainform\">";
echo "<table width=\"100%\">\n
	<tr><th>".$_SESSION['language']['SHARE']['SHARED_DIRS']
	."</th><th width=\"160\">".$_SESSION['language']['SHARE']['W_SUBDIRS']
	."</th><th>&nbsp;</th></tr>\n";

//auch temp-verzeichnis anzeigen (f�r dateien die gerade geladen werden)
echo "<tr>\n<td><a href=\"sharefiles.php?dir=" .addslashes(htmlspecialchars($share->get_temp()))."\">"
	.htmlspecialchars($share->get_temp())."</a></td>\n";
echo "<td>&nbsp;</td><td>&nbsp;</td></tr>";

$sharedirs=$share->get_shared_dirs(1);

//freigegebene verzeichnisse anzeigen
foreach($sharedirs as $a){
	$cur_share=&$share->get_shared_dir($a);
	//verzeichnisname -> link zu den einzelnen dateien
	echo "<tr>\n<td><a href=\"sharefiles.php?dir="
		.addslashes(htmlspecialchars($cur_share['NAME']))."\">"
		.htmlspecialchars($cur_share['NAME'])
		."</a></td>\n";
	//checkbox fuer unterverzeichnisse
	echo "<td><input type=\"checkbox\" "
		."onclick=\"do_setsubs('".addslashes(htmlspecialchars($cur_share['NAME']))."',"
		.(($cur_share['SHAREMODE'] == 'subdirectory') ? "0" : "1")
		.");\" value=\"1\" "
		.$share->sharemode[$cur_share['SHAREMODE']]." />\n";
	echo "</td>\n";
	echo "<td><a href=\"javascript:delshare('"
		.addslashes(htmlspecialchars($cur_share['NAME']))."');\">"
		.$_SESSION['language']['SHARE']['DELETE']
		."</a></td></tr>\n\n";	//l�schen
}

//Neues verzeichnis freigeben
echo "\n<tr><td>".$_SESSION['language']['SHARE']['NEW']
	.": <input name=\"new_share\" size=\"60\" />";
echo " <input type=\"button\" value=\"...\" onclick=\"select_dir();\" /></td>";
echo "<td><input type=\"checkbox\" name=\"new_subs\" value=\"1\" "
	."checked=\"checked\" /></td><td><input type=\"button\" value=\""
	.$_SESSION['language']['SHARE']['ADD']."\" "
	."onclick=\"newshare()\"/></td></tr>\n";
echo "</table>";

echo "<br />\n";
echo "<div align=\"center\"><table><tr>\n";
echo "<td><input type=\"button\" onclick=\"do_setsubs('*sharecheck',0);\" value='"
	.$_SESSION['language']['SHARE']['SHARECHECK']."' /></td>";
echo "<td><input type=\"button\" onclick=\"share_export()\" value='"
	.$_SESSION['language']['SHARE']['EXPORTLIST']."' /></td>\n";
echo "</tr></table></div>\n";
echo "</form>\n";

echo "</body>
</html>";
