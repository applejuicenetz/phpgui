<?php
session_start();
require_once "subs.php";
require_once "classes/class_core.php";
$core = new Core();
$lang =& $_SESSION['language']['SETTINGS'];

//einstellungen aendern
if(!empty($_POST['change'])){
	switch($_POST['change']){
		case 'standard':
			$_POST['incdir']=urlencode($_POST['incdir']);
			$_POST['tempdir']=urlencode($_POST['tempdir']);
			$_POST['nick']=urlencode($_POST['nick']);
			$core->command("function","setsettings?Incomingdirectory="
				.$_POST['incdir']."&Temporarydirectory=".$_POST['tempdir']
				."&Port=".$_POST['c_port']."&XMLPort=".$_POST['c_xml_port']
				."&Nickname=".$_POST['nick']);
			break;
		case 'verbindungen':
			if(empty($_POST['autoconnect'])) $_POST['autoconnect']='false';
			$_POST['maxul']=floor($_POST['maxul']*1024);	//von kb in bytes umrechnen
			$_POST['maxdl']=floor($_POST['maxdl']*1024);	//von kb in bytes umrechnen
			$core->command("function","setsettings?MaxConnections="
				.$_POST['maxcon']."&MaxUpload=".$_POST['maxul']
					."&Speedperslot=".$_POST['uls']."&MaxDownload="
					.$_POST['maxdl']."&MaxNewConnectionsPerTurn="
					.$_POST['conturn']."&AutoConnect=".$_POST['autoconnect']
					."&MaxSourcesPerFile=".$_POST['maxdlsrc']);
			break;
	}
}

echo writehead('Settings');
echo $_SESSION['stylesheet'];

echo "<script>
function GetDir(returnto){
	var dirlist=window.open('directory.php?returninput='+returnto+'&"
		.SID."','Dirlist',
		'width=400,height=350,left=10,top=10,dependent=yes,scrollbars=no');
	dirlist.focus();
}
</script>
</head>
<body>";

$settings_xml=$core->command("xml","settings.xml");

$_SESSION['phpaj']['core_source_port']=$settings_xml['PORT']['VALUES']['CDATA'];

//standardeinstellungen
echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."?".SID
	."\" name=\"standard\">";
echo "<table width=\"500\"><tr><th colspan=\"2\">"
	.$lang['STANDARD']."</th></tr>";
echo "<tr><td><label for=\"tempdir\">".$lang['TEMPDIR']."</label></td>";
echo "<td><input id=\"tempdir\" name=\"tempdir\" size=\"40\" value=\""
	.htmlspecialchars($settings_xml['TEMPORARYDIRECTORY']['VALUES']['CDATA'])
	."\" /> <input type=\"button\" value=\"...\" "
	."onclick=\"GetDir('standard.tempdir.value');\" /></td></tr>";
echo "<tr><td><label for=\"incdir\">".$lang['INCOMINGDIR']."</label></td>";
echo "<td><input id=\"incdir\" name=\"incdir\" size=\"40\" value=\""
	.htmlspecialchars($settings_xml['INCOMINGDIRECTORY']['VALUES']['CDATA'])
	."\" /> <input type=\"button\" value=\"...\" "
	."onclick=\"GetDir('standard.incdir.value');\" /></td></tr>";
echo "<tr><td><label for=\"c_port\">".$lang['PORT']."</label></td>";
echo "<td><input id=\"c_port\" name=\"c_port\" size=\"5\" value='"
	.$settings_xml['PORT']['VALUES']['CDATA']
	."' type=\"text\" style=\"text-align:right\" /></td></tr>";
echo "<tr><td><label for=\"c_xml_port\">".$lang['XML_PORT']."</label></td>";
echo "<td><input id=\"c_xml_port\" name=\"c_xml_port\" size=\"5\" value='"
	.$settings_xml['XMLPORT']['VALUES']['CDATA']
	."' type=\"text\" style=\"text-align:right\" /></td></tr>";
echo "<tr><td><label for=\"nick\">".$lang['NICK']."</label></td>";
echo "<td><input id=\"nick\" name=\"nick\" value=\""
	.htmlspecialchars($settings_xml['NICK']['VALUES']['CDATA'])."\" /></td></tr>";
echo "<tr><td colspan=\"2\">"
	."<input type=\"hidden\" name=\"change\" value=\"standard\" />";
echo "<input type=\"submit\" value=\""
	.$lang['OK']."\" /></td></tr>";
echo "</table></form><br />";

//einstellungen f�r verbindung
echo "<form method=\"post\" name=\"connection\" "
	."action=\"".$_SERVER['PHP_SELF']."?".SID."\">";
echo "<table width=\"500\"><tr><th colspan=\"2\">"
	.$lang['CONNECTION']."</th></tr>";
echo "<tr><td><label for=\"maxcon\">".$lang['MAXCONNECTIONS']."</label></td>";
echo "<td><input id=\"maxcon\" name=\"maxcon\" value='"
	.$settings_xml['MAXCONNECTIONS']['VALUES']['CDATA']
	."' type=\"text\" style=\"text-align:right\" /></td></tr>";
echo "<tr><td><label for=\"maxul\">".$lang['MAXUL']."</label></td>";
echo "<td><input id=\"maxul\" name=\"maxul\" value='"
	.($settings_xml['MAXUPLOAD']['VALUES']['CDATA'] / 1024)
	."' type=\"text\" style=\"text-align:right\" /></td></tr>";
echo "<tr><td><label for=\"uls\">".$lang['SPEEDPERSLOT']."</label></td>";
echo "<td><input id=\"uls\" name=\"uls\" value='"
	.$settings_xml['SPEEDPERSLOT']['VALUES']['CDATA']
	."' type=\"text\" style=\"text-align:right\" /></td></tr>";
echo "<tr><td><label for=\"maxdl\">".$lang['MAXDL']."</label></td>";
echo "<td><input id=\"maxdl\" name=\"maxdl\" value='"
	.($settings_xml['MAXDOWNLOAD']['VALUES']['CDATA'] / 1024)
	."' type=\"text\" style=\"text-align:right\" /></td></tr>";
echo "<tr><td><label for=\"conturn\">".$lang['MAXCONNECTIONSPERTURN']
	."</label></td>";
echo "<td><input id=\"conturn\" name=\"conturn\" value='"
	.$settings_xml['MAXNEWCONNECTIONSPERTURN']['VALUES']['CDATA']
	."' type=\"text\" style=\"text-align:right\" /></td></tr>";
echo "<tr><td><label for=\"maxdlsrc\">".$lang['MAXDLSRC']."</label></td>";
echo "<td><input id=\"maxdlsrc\" name=\"maxdlsrc\" value='"
	.$settings_xml['MAXSOURCESPERFILE']['VALUES']['CDATA']
	."' type=\"text\" style=\"text-align:right\" /></td></tr>";
echo "<tr>";
echo "<td colspan=\"2\">"
	."<input type=\"checkbox\" id=\"aconnect\" name=\"autoconnect\" value=\"true\"";
if($settings_xml['AUTOCONNECT']['VALUES']['CDATA']=='true')
	echo " checked=\"checked\"";
echo " /> <label for=\"aconnect\">".$lang['AUTOCONNECT']."</label></td></tr>";
echo "<tr><td colspan=\"2\">"
	."<input type=\"hidden\" name=\"change\" value=\"verbindungen\" />";
echo "<input type=\"submit\" value=\"".$lang['OK']."\" /></td></tr>";
echo "</table></form>";

echo "</body>
</html>";
