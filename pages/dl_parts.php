<?php

use appleJuiceNETZ\appleJuice\Downloads;

$Downloadlist = new Downloads;

if(!empty($_GET['dl_id'])){
	$dl = $Downloadlist->download($_GET['dl_id']);
	$title = $dl['TEMPORARYFILENUMBER'].".data - ".htmlspecialchars($dl['FILENAME']);
}elseif(!empty($_GET['usr_id'])){
	$usr = $Downloadlist->user($_GET['usr_id']);
	$title = htmlspecialchars($usr['NICKNAME']." - ".$usr['FILENAME']);
}else die('wtf');

echo "</head><body>
<div align=\"center\">
<table width=\"100%\">\n";

//Legende
echo "<tr>";
echo "<td width=\"20\" style=\"background:#0000FF;\">&nbsp;</td><td>"
	.$lang->Downloads->parts->available."</td>";
echo "<td width=\"20\" style=\"background:#FF0000;\">&nbsp;</td><td>"
	.$lang->Downloads->parts->NA."</td>\n";
echo "</tr>\n";
echo "<tr>";
echo "<td width=\"20\" style=\"background:#000000;\">&nbsp;</td><td>"
	.$lang->Downloads->parts->received."</td>";
echo "<td width=\"20\" style=\"background:#00FF00;\">&nbsp;</td><td>"
	.$lang->Downloads->parts->checked."</td>";
echo "</tr>\n";
echo "<tr>";
echo "<td width=\"20\" style=\"background:#FFFF00;\">&nbsp;</td>"
	."<td colspan=\"3\">h"
	.$lang->Downloads->parts->active_transfer."</td>\n";
echo "</tr>\n";
echo "</table><br />\n";

//user oder downloadbild
if(!empty($_GET['dl_id'])){
	echo "<img src=\"pages/showparts.php?dl_id=".$_GET['dl_id']."&amp;"
		.SID."\" alt='download partlist' />\n";
}elseif(!empty($_GET['usr_id'])){
	echo "<img src=\"pages/showparts.php?usr_id=".$_GET['usr_id']."&amp;"
		.SID."\" alt='user partlist' />\n";
}else die("wtf");


echo '</div>
</body>
</html>';