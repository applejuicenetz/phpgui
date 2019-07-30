<?php
session_start();
include_once "subs.php";
include_once "classes/class_core.php";

$core = new Core();

echo writehead('phpaj');
echo $_SESSION['stylesheet'];
echo "</head>
<body class=\"top\">
<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\" name=\"linkform\">
<input name=\"showlinkpage\" type=\"hidden\" value=\"1\" />
<input name=\"".session_name()."\" type=\"hidden\" value=\"".session_id()."\" />
<table><tr>";

echo "<td class=\"link\"><label for=\"link\">"
	.$_SESSION['language']['LINK']['LINK']."</label>:</td><td class=\"link\">"
	."<input id=\"link\" name=\"ajfsp_link\" size=\"60\" /></td>";
echo "<td class=\"link\"><input type=\"submit\" value=\""
	.$_SESSION['language']['LINK']['OK']."\" /></td>";
	
// modified by red171 (appledocs.to)
if(!empty($_SESSION['ajfsp_link']) and empty($_GET['ajfsp_link'])) {
	$_GET['ajfsp_link'] = $_SESSION['ajfsp_link'];
	$_GET['showlinkpage'] = 1;
	unset($_SESSION['ajfsp_link']);
}

if(!empty($_GET['ajfsp_link'])){
	if(get_magic_quotes_gpc())
		$_GET['ajfsp_link']=stripslashes($_GET['ajfsp_link']);
	$_GET['ajfsp_link']=urldecode($_GET['ajfsp_link']);	//falls link schon encodet eingegeben wurde
	//link zerlegen, um infos anzuzeigen
	$linkteile = explode('/',$_GET['ajfsp_link']);
	if(!empty($linkteile[2])){
		echo "<td id=\"newlinkinfo\">";
		$linkteile = explode("|",$linkteile[2]);
		//Infos fr Dateilink anzeigen + im hauptfenster die downloads zeigen
		if($linkteile[0]=='file'){
			echo "Download: ".$linkteile[1]." ("
				.sizeformat($linkteile[3]).") &rArr; ";
			echo $core->command("function","processlink?link="
				.urlencode($_GET['ajfsp_link']));
			if(!empty($_GET['showlinkpage'])){
				echo "\n<script type='text/javascript'><!--\n";
				echo "parent.main.location.href='downloads.php?".SID."';\n";
				echo "//-->\n</script>";
			}
		}
		//Infos für Serverlink anzeigen + im hauptfenster die server zeigen
		if($linkteile[0]=='server'){
			echo "Server: $linkteile[1]:$linkteile[2] &rArr; ";
			echo $core->command("function","processlink?link="
				.urlencode($_GET['ajfsp_link']));
			if(!empty($_GET['showlinkpage'])){
				echo "\n<script type='text/javascript'><!--\n";
				echo "parent.main.location.href='server.php?".SID."';\n";
				echo "//-->\n</script>";
			}
		}
		echo "\n<script type='text/javascript'><!--\n";
		echo "window.setTimeout(\""
			."document.getElementById('newlinkinfo').style.display='none'\","
			."5000);\n";
		echo "//-->\n</script>";
		echo "</td>";
	}
}
echo "</tr></table>";

if(!empty($_GET['killcore'])){
	$core->command("function","exitcore");
	echo "<script type='text/javascript'>
	<!--
	parent.location.href='../index.php?".SID."';
	//-->\n</script>";	
}

echo "<div class=\"tabs\">";
echo "<a href=\"start.php?".SID."\" target=\"main\">"
	."<img src=\"../style/".$_SESSION['tabs_start_icon']
	."\" alt=\"\" />".$_SESSION['language']['TABS']['START']."</a> ";
echo "<a href=\"downloads.php?".SID."\" target=\"main\">"
	."<img src=\"../style/".$_SESSION['tabs_downloads_icon']
	."\" alt=\"\" />".$_SESSION['language']['TABS']['DOWNLOADS']."</a> ";
echo "<a href=\"uploads.php?".SID."\" target=\"main\">"
	."<img src=\"../style/".$_SESSION['tabs_uploads_icon']
	."\" alt=\"\" />".$_SESSION['language']['TABS']['UPLOADS']."</a> ";
echo "<a href=\"shares.php?".SID."\" target=\"main\">"
	."<img src=\"../style/".$_SESSION['tabs_share_icon']
	."\" alt=\"\" />".$_SESSION['language']['TABS']['SHARE']."</a> ";
echo "<a href=\"search.php?".SID."\" target=\"main\">"
	."<img src=\"../style/".$_SESSION['tabs_search_icon']
	."\" alt=\"\" />".$_SESSION['language']['TABS']['SEARCH']."</a> ";
echo "<a href=\"server.php?".SID."\" target=\"main\">"
	."<img src=\"../style/".$_SESSION['tabs_server_icon']
	."\" alt=\"\" />".$_SESSION['language']['TABS']['SERVER']."</a> ";
echo "<a href=\"settings.php?".SID."\" target=\"main\">"
	."<img src=\"../style/".$_SESSION['tabs_settings_icon']
	."\" alt=\"\" />".$_SESSION['language']['TABS']['SETTINGS']."</a> ";
echo "<a href=\"extras.php?".SID."\" target=\"main\">"
	."<img src=\"../style/".$_SESSION['tabs_extras_icon']
	."\"alt=\"\" />".$_SESSION['language']['TABS']['EXTRAS']."</a> ";
echo "<a href=\"javascript:if(confirm('"
	.addslashes($_SESSION['language']['TABS']['COREKILL_QUESTION'])
	."')) window.location.href='".$_SERVER['PHP_SELF']."?".SID."&amp;killcore=1'\" "
	."style=\"margin-left:50px;\">"
	."<img src=\"../style/".$_SESSION['tabs_corekill_icon']
	."\" alt=\"\" />".$_SESSION['language']['TABS']['COREKILL']."</a> ";
echo "<a href=\"../index.php?".SID."\" target=\"_parent\">"
	."<img src=\"../style/".$_SESSION['tabs_logout_icon']
	."\" alt=\"\" />".$_SESSION['language']['TABS']['LOGOUT']."</a> ";
echo "</div>";

echo '</form>
</body>
</html>';
