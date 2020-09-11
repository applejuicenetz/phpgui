<?php
session_start();
require_once "subs.php";
require_once "classes/class_downloads.php";
$Downloadlist = new Downloads;

if(!empty($_GET['dl_id'])){
	$dl = $Downloadlist->download($_GET['dl_id']);
	$title = $dl['TEMPORARYFILENUMBER'].".data - ".htmlspecialchars($dl['FILENAME']);
}elseif(!empty($_GET['usr_id'])){
	$usr = $Downloadlist->user($_GET['usr_id']);
	$title = htmlspecialchars($usr['NICKNAME']." - ".$usr['FILENAME']);
}else die('wtf');

echo writehead('Download Parts ('.$title.')');
echo $_SESSION['stylesheet'];

echo "</head><body>
<div align=\"center\">
<table width=\"100%\">\n";

//Legende
echo "<tr>";
echo "<td width=\"20\" style=\"background:#0000FF;\">&nbsp;</td><td>"
	.$_SESSION['language']['PARTS']['AVAILABLE']."</td>";
echo "<td width=\"20\" style=\"background:#FF0000;\">&nbsp;</td><td>"
	.$_SESSION['language']['PARTS']['NA']."</td>\n";
echo "</tr>\n";
echo "<tr>";
echo "<td width=\"20\" style=\"background:#000000;\">&nbsp;</td><td>"
	.$_SESSION['language']['PARTS']['RECEIVED']."</td>";
echo "<td width=\"20\" style=\"background:#00FF00;\">&nbsp;</td><td>"
	.$_SESSION['language']['PARTS']['CHECKED']."</td>";
echo "</tr>\n";
echo "<tr>";
echo "<td width=\"20\" style=\"background:#FFFF00;\">&nbsp;</td>"
	."<td colspan=\"3\">"
	.$_SESSION['language']['PARTS']['ACTIVE_TRANSFER']."</td>\n";
echo "</tr>\n";
echo "</table><br />\n";

//user oder downloadbild
if(!empty($_GET['dl_id'])){
	echo "<img src=\"showparts.php?dl_id=".$_GET['dl_id']."&amp;"
		.SID."\" alt='download partlist' />\n";
}elseif(!empty($_GET['usr_id'])){
	echo "<img src=\"showparts.php?usr_id=".$_GET['usr_id']."&amp;"
		.SID."\" alt='user partlist' />\n";
}else die("wtf");


echo '</div>
</body>
</html>';
