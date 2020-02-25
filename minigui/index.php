<?php
Header("Cache-Control: no-cache");
Header('Content-Type: text/html; charset=UTF-8');
session_start();
$_SESSION = array();	//session daten alle l�schen

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" "
	."\"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n";
include_once "../vars.php";		//einstellungen holen

//sprache
	if(!empty($_GET['c_lang'])){
		$_GET['c_lang']=str_replace("/",'',$_GET['c_lang']);
		$_GET['c_lang']=str_replace("\\",'',$_GET['c_lang']);
	}
	if(empty($_GET['c_lang']) || !preg_match('/\.xml$/i',$_GET['c_lang'])
			|| !file_exists("../language/".$_GET['c_lang'])){
		$language_xml="../language/".$standard_language_xml;
	}else{
		$language_xml="../language/".$_GET['c_lang'];
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
	xml_set_element_handler($language_parser, "startLanguageElement",
		"endLanguageElement");
	xml_parse($language_parser, $language_file);
	xml_parser_free($language_parser);

//------------------

include_once "../main/subs.php";

//style
	$styles_liste=dirlisting("../style","php");
	if(empty($_GET['c_style'])
			|| !in_array($_GET['c_style'],$styles_liste[0])){
		$_SESSION['stylefile']=$standard_stylefile;
	}else{
		$_SESSION['stylefile']=$_GET['c_style'];
	}
	include_once "../style/".$_SESSION['stylefile'];
	$_SESSION['stylesheet']="<link rel=\"stylesheet\" type=\"text/css\" "
		."href=\"../style/".$stylesheet."\" />";

//core daten aus url in form �bernehmen
if(!empty($_GET['ip'])) $core_standard_ip=$_GET['ip'];
if(!empty($_GET['xmlport'])) $core_standard_xml_port=$_GET['xmlport'];
$core_standard_pass="";
if(!empty($_GET['password'])) $core_standard_pass=$_GET['password'];

echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n"
."<head>\n<title>php-applejuice-miniGUI</title>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
<meta http-equiv=\"Cache-Control\" content=\"no-cache\" />\n";
echo $_SESSION['stylesheet'];
echo "</head>
<body onload=\"document.forms[0].cpass.focus()\">";
	
//testen, ob zlib funzt
if($_SESSION['phpaj']['zipped'] && !function_exists("gzuncompress")){
	$_SESSION['phpaj']['zipped']=0;
	echo "<span style=\"background: #FF0000\">"
		.$_SESSION['language']['LOGIN']['GZIP_FAILED']."</span><br />";
}	

echo "<h2 style=\"text-align:center;\">"
	.$_SESSION['language']['LOGIN']['HEADLINE']."-miniGUI</h2>\n";

echo "<div>".$phpguiversion."\n";

echo "<form action=\"minigui.php?".SID."\" method=\"post\">\n";

///

echo "<ul class=\"mini\">";
echo "<li><label for=\"ip\">"
	.$_SESSION['language']['LOGIN']['CORE_HOST']."</label>: "
	."<input type='url' id=\"host\" name=\"host\" value=\"".$core_standard_host."\" required /></li>\n";

echo "<li><label for=\"cpass\">"
	.$_SESSION['language']['LOGIN']['CORE_PASSWORD']."</label>: "
	."<input id=\"cpass\" type=\"password\" name=\"cpass\" value=\""
	.$core_standard_pass."\" required autofocus /></li>\n";

//style-auswahl
	echo "<li><label for=\"c_style\">"
		.$_SESSION['language']['LOGIN']['GUI_STYLE']."</label>: "
		."<select id=\"c_style\" name=\"c_style\" size=\"1\" "
		."onchange=\"window.location.href='index.php?c_style='"
		."+document.forms[0].c_style.value+'&amp;c_lang='"
		."+document.forms[0].c_lang.value;\">\n";
	for($i=0;$i<count($styles_liste[0]);$i++){
		echo "<option value=\"".$styles_liste[0][$i]."\"";
		if($styles_liste[0][$i]==$_SESSION['stylefile'])
			echo " selected=\"selected\"";
		echo ">".$styles_liste[1][$i]."</option>\n";
	}
	echo "</select></li>\n";

//sprach-auswahl
	echo "<li><label for=\"c_lang\">"
		.$_SESSION['language']['LOGIN']['GUI_LANGUAGE']."</label>: "
		."<select id=\"c_lang\" name=\"c_lang\" size=\"1\" "
		."onchange=\"window.location.href='index.php?c_lang='"
		."+document.forms[0].c_lang.value+'&amp;c_style='"
		."+document.forms[0].c_style.value;\">\n";
	$lang_liste=dirlisting("../language","xml");
	for($i=0;$i<count($lang_liste[0]);$i++){
		echo "<option value='".$lang_liste[0][$i]."'";
		if("../language/".$lang_liste[0][$i]==$language_xml)
			echo " selected=\"selected\"";
		echo ">".$lang_liste[1][$i]."</option>\n";
	}
	echo "</select></li>\n";

echo "<li><input type=\"hidden\" name=\"reloadnews\" value=\""
	.$start_shownews."\" />
<input type=\"hidden\" name=\"reloadshare\" value=\""
	.$start_showshareinfo."\" />";
echo "<input type=\"submit\" value=\""
	.$_SESSION['language']['LOGIN']['OK']."\" /></li>\n";
echo "</ul>";

///

echo "</form>\n";

if(isset($_GET['password']))
	echo "<script type=\"text/javascript\">\n"
		."<!--\n"
		."document.loginform.submit();\n"
		."//-->\n</script>";

echo "<a href=\"../\">[GUI]</a>";
echo "<div class=\"authors\">\n";
echo "Code by UP<br />";
echo 'modified (again) by <a href="https://bitbucket.org/red171/" target="_blank">red171</a>';
echo "</div>\n";
echo "</body>
</html>";
