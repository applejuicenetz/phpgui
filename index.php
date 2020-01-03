<?php
error_reporting(0);

Header("Cache-Control: no-cache");
Header('Content-Type: text/html; charset=UTF-8');
session_start();
$_SESSION = array();	//session daten alle loeschen

include_once "vars.php";

//sprache
	if(!empty($_GET['c_lang'])){
		$_GET['c_lang']=str_replace("/",'',$_GET['c_lang']);
		$_GET['c_lang']=str_replace("\\",'',$_GET['c_lang']);
	}
	if(empty($_GET['c_lang']) || !preg_match('/\.xml$/i',$_GET['c_lang'])
			|| !file_exists("language/".$_GET['c_lang'])){
		$language_xml="language/".$standard_language_xml;
	}else{
		$language_xml="language/".$_GET['c_lang'];
	}

//sprachdatei lesen
//------------------

	$_SESSION['language']=array();
	function startLanguageElement($parser, $name, $attrs) {
		$keys=array_keys($attrs);
		$_SESSION['language'][$name]=array();
		foreach($keys as $l){
			$_SESSION['language'][$name][$l]=$attrs[$l];
		}
	}

	function endLanguageElement($parser, $name) {}

	$language_file = @file($language_xml);
	$language_file = join("",$language_file);
		/*echo "<!--";
		echo $language_file;
		echo "\n-->\n\n";*/
	$language_parser = xml_parser_create();
	xml_set_element_handler($language_parser,
		"startLanguageElement","endLanguageElement");
	xml_parse($language_parser, $language_file);
	xml_parser_free($language_parser);

//------------------

include_once "main/subs.php";

//style
	$styles_liste=dirlisting("style","php");
	if(empty($_GET['c_style'])
		|| !in_array($_GET['c_style'],$styles_liste[0])){
		$_SESSION['stylefile']=$standard_stylefile;
	}else{
		$_SESSION['stylefile']=$_GET['c_style'];
	}
	include_once "style/".$_SESSION['stylefile'];
	$_SESSION['stylesheet']="<link rel=\"stylesheet\" type=\"text/css\""
		." href=\"../style/".$stylesheet."\" />\n";

$core_standard_pass="";

//core daten aus url uebernehmen
if (isset($_GET['l']) && !empty($_GET['l'])) {
    $login_data = explode('|', base64_decode(trim($_GET['l'])), 2);

    if (2 === count($login_data)) {
        $core_standard_host = $login_data[0];
        $core_standard_pass = $login_data[1];
    }
}

echo "<!DOCTYPE html>
<html>
<head>
<title>php-applejuice</title>\n";
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"style/".$stylesheet."\" />\n";
echo '<style type="text/css">
select {width:100%;}
</style>';
echo "<meta charset=\"utf-8\">
<meta http-equiv=\"Cache-Control\" content=\"no-cache\" />\n";
echo "</head>
<body onload=\"document.loginform.cpass.focus()\">
<div align=\"center\">";

echo "<h2>".$_SESSION['language']['LOGIN']['HEADLINE']."</h2>\n";

echo "<div>".$phpguiversion."</div>\n";

echo "<form name=\"loginform\" action=\"main/index.php?".SID."\" method=\"post\" autocomplete=\"off\">\n"
	."<input type=\"hidden\" name=\"reloadnews\" value=\""
	.$start_shownews."\" /><input type=\"hidden\" name=\"reloadshare\" "
	."value=\"".$start_showshareinfo."\" />\n"
	."<table>\n";

echo "<tr><td><label for=\"ip\">"
	.$_SESSION['language']['LOGIN']['CORE_HOST']."</label>:</td>"
	."<td><input id=\"host\" name=\"host\" value=\"".$core_standard_host."\" required />"
	."</td></tr>\n";
echo "<tr><td><label for=\"cpass\">"
	.$_SESSION['language']['LOGIN']['CORE_PASSWORD']."</label>:</td>"
	."<td><input id=\"cpass\" type=\"password\" name=\"cpass\" value=\""
	.$core_standard_pass."\" autofocus required /></td></tr>\n";

//style-auswahl
	echo "<tr><td><label for=\"c_style\">"
		.$_SESSION['language']['LOGIN']['GUI_STYLE']."</label>:</td>"
		."<td><select id=\"c_style\" name=\"c_style\" size=\"1\" onchange=\""
		."window.location.href='index.php?c_style='"
		."+document.forms[0].c_style.value+'&amp;c_lang='"
		."+document.forms[0].c_lang.value+'&amp;".SID."';\">\n";
	for($i=0;$i<count($styles_liste[0]);$i++){
		echo "<option value='".$styles_liste[0][$i]."'";
		if($styles_liste[0][$i]==$_SESSION['stylefile'])
			echo " selected=\"selected\"";
		echo ">".$styles_liste[1][$i]."</option>\n";
	}
	echo "</select></td></tr>\n";

//sprach-auswahl
	echo "<tr><td><label for=\"c_lang\">"
		.$_SESSION['language']['LOGIN']['GUI_LANGUAGE']."</label>:</td>"
		."<td><select id=\"c_lang\" name=\"c_lang\" size=\"1\" onchange=\""
		."window.location.href='index.php?c_lang='"
		."+document.forms[0].c_lang.value+'&amp;c_style='"
		."+document.forms[0].c_style.value+'&amp;".SID."';\">\n";
	$lang_liste=dirlisting("language","xml");
	for($i=0;$i<count($lang_liste[0]);$i++){
		echo "<option value=\"".$lang_liste[0][$i]."\"";
		if("language/".$lang_liste[0][$i]==$language_xml)
			echo " selected=\"selected\"";
		echo ">".$lang_liste[1][$i]."</option>\n";
	}

echo "</select></td></tr>\n"
	."<tr><td colspan=\"2\"><div align=\"right\">"
	."<input type=\"submit\" value=\""
	.$_SESSION['language']['LOGIN']['OK']."\" /></div></td></tr>\n"
	."</table></form>\n";

if(isset($_GET['l']))
	echo "<script type=\"text/javascript\">\n<!--\n"
		."document.loginform.submit();\n"
		."//-->\n</script>";
echo "<a href=\"minigui/\">[miniGUI]</a>";

echo "<div class=\"authors\">\n";
echo "Code by UP<br />";
echo 'modified (again) by <a href="https://github.com/red171/" target="_blank">red171</a>';
echo "</div>\n";
echo "</div>
</body>
</html>";
