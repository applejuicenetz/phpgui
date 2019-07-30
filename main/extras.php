<?php
session_start();
include_once "subs.php";
include_once "../plugins/register.php";
$Plugin =new Plugin();
$Plugin->Find_Plugins();

echo writehead('extras');
echo $_SESSION['stylesheet'];
echo "</head><body>";

$phpaj_pluginurllist=array();

// Links zu den plugins zeigen
echo "<div class=\"tabs\">";
foreach($Plugin->liste as $a){
	echo "<a href=\"extras.php?show=".$a[2]."&amp;".SID."\">";
	array_push($phpaj_pluginurllist,$a[2]);
	if(empty($a[1])) $a[1]="../style/".$_SESSION['tabs_extras_icon'];
	echo "<img src=\"../plugins/".$a[1]."\" alt=\"\" />";
	echo $a[0]."</a> ";
}
echo "</div>";

echo "<br /><br /><div>";
// $phpaj_ownurl enth�lt die url zur aufgerufenen seite
// $phpaj_show enth�lt die url zum plugin und muss vom plugin immer
	//als "show" mit get oder post �bergeben werden
$phpaj_ownurl=$_SERVER['PHP_SELF']."?".SID;
$phpaj_show="";
if(!empty($_GET['show'])) $phpaj_show=$_GET['show'];
if(!empty($_POST['show'])) $phpaj_show=$_POST['show'];
if(!empty($phpaj_show)){
	$phpaj_ownurl.="&amp;show=".$phpaj_show;
	//check, ob die an show �bergebene url auch zu einem plugin geh�rt ;)
	if(in_array($phpaj_show,$phpaj_pluginurllist))
		include "../plugins/".$phpaj_show;
}
echo "</div>";
echo "</body></html>";
