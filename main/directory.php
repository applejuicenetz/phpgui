<?php
session_start();
include_once "subs.php";
include_once "classes/class_share.php";
$Share = new Share;

echo writehead('Directory');

echo '<style type="text/css">
select {width:100%;}
</style>';
echo "<script type=\"text/javascript\">
<!--
function ChangeDir(){
	window.location.href='".$_SERVER['PHP_SELF']."?returninput="
		.urlencode($_GET['returninput'])
		."&dir='+encodeURI(document.forms[0].dirselect.value)+'&"
		.SID."';
}
	
function UseSelectedDir(){
	opener.document.".$_GET['returninput']
		."=document.forms[0].current_dir.value;
	window.close();	
}
//-->
</script>";
echo $_SESSION['stylesheet'];
echo "</head><body>";

echo "<div align=\"center\">";
echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."?".SID."\">";
//Verzeichnis Auswahl
	//bei doppelklick auswahl uebernehmen
	echo "<select size=\"16\" name=\"dirselect\" ondblclick=\"ChangeDir()\">\n";

	if(!isset($_GET['dir'])) $_GET['dir']="";
	$_GET['dir']=rawurldecode($_GET['dir']);
	$dirliste=$Share->directory($_GET['dir']);
	foreach($dirliste as $a){
		echo "<option value='".rawurlencode($a[0])."'>".htmlspecialchars($a[1])."</option>\n";
	}
	echo "</select><br />\n";
	//button als ersatz f�r doppelklick
	echo "<input type=\"button\" value=\"&gt;&gt;\" onclick=\"ChangeDir()\" "
		."style=\"width:100%;\" />\n<br /><br />\n";
	//feld mit aktuellem Verzeichnis + OK-button
	echo "<input name=\"current_dir\" value=\"".$_GET['dir']."\" size=\"50\" "
		."readonly=\"readonly\" />\n";
	echo " <input type=\"button\" onclick=\"UseSelectedDir();\" "
		."value=\"OK\" />\n";
	echo "</form>\n";

echo "</div>";
echo "</body>
</html>";
