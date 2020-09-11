<?php
session_start();
require_once "subs.php";
require_once "classes/class_core.php";
$core = new Core;

echo writehead('Share-Export');
echo $_SESSION['stylesheet'];
echo "</head><body>";

if(!empty($_GET['clear_list'])) $_SESSION['shareexport']=array();
if(empty($_GET['exp_format'])) $_GET['exp_format']="Default";

echo "<form name=\"exportform\" action=\"\">";
echo "<div align=\"center\">";
echo $_SESSION['language']['SHARE']['EXPORT_TEXT'];
echo "</div><br />";
echo "<input name=\"withsource\" type=\"checkbox\" value=\"1\" "
	."onclick=\"document.location.href='".$_SERVER['PHP_SELF']."?exp_format="
	.$_GET['exp_format']
	."&amp;withsource='+document.exportform.withsource.checked+'&amp;"
	.SID."';\"";
if(!empty($_GET['withsource']) && $_GET['withsource']=="true")
	echo " checked=\"checked\"";
echo " /> ";
$exportf = dirlisting("../export","php");
for($i=0;$i<count($exportf[0]);$i++){
	echo "<input type=\"button\" value=\"".$exportf[1][$i]
		."\" onclick=\"document.location.href='".$_SERVER['PHP_SELF']."?exp_format="
		.$exportf[1][$i]
		."&amp;withsource='+document.exportform.withsource.checked+'&amp;"
		.SID."';\" />";
}

echo "<br /><textarea cols=\"90\" rows=\"16\" wrap=\"off\">";
if(!empty($_SESSION['shareexport'])){
	if(!in_array($_GET['exp_format'],$exportf[1]))
		$_GET['exp_format']="Default";
	require_once "../export/".$_GET['exp_format'].".php";
	if(!empty($_GET['withsource']) && $_GET['withsource']=="true"){
		$netinfo=$core->command("xml",
			"modified.xml?filter=informations;server");
		$temp_netinfo=array_keys($netinfo['NETWORKINFO']);
		$curr_coreip=$netinfo['NETWORKINFO'][$temp_netinfo[0]]['IP'];
		$curr_serverip="";
		$curr_serverport="";
		if($netinfo['NETWORKINFO'][$temp_netinfo[0]]
				['CONNECTEDWITHSERVERID']!="-1"){
			$curr_serverip=":".$netinfo['SERVER']
				[$netinfo['NETWORKINFO'][$temp_netinfo[0]]
				['CONNECTEDWITHSERVERID']]['HOST'];
			$curr_serverport=":".$netinfo['SERVER']
				[$netinfo['NETWORKINFO'][$temp_netinfo[0]]
				['CONNECTEDWITHSERVERID']]['PORT'];
		}
	}
	asort($_SESSION['shareexport']);
	foreach($_SESSION['shareexport'] as $a){
		$share_ex = explode('/',$a);
		$share_ex = explode('|',$share_ex[2]);
		$share_ex_link = $a;
		if(!empty($_GET['withsource']) && $_GET['withsource']=="true"){
			$share_ex_link=substr($share_ex_link,0,strlen($share_ex_link)-1)."|"
				.$curr_coreip.":".$_SESSION['phpaj']['core_source_port']
				.$curr_serverip.$curr_serverport."/";
		}
		$share_ex_name = $share_ex[1];
		$share_ex_hash = $share_ex[2];
		$share_ex_bytesize = $share_ex[3];
		$share_ex_size = sizeformat($share_ex[3]);
		write_linkexport($share_ex_link,$share_ex_name,$share_ex_hash,
			$share_ex_bytesize,$share_ex_size);
	}
}
echo "</textarea><br />";
echo "<input type=\"button\" value=\""
	.$_SESSION['language']['SHARE']['DEL_EXPORT']
	."\" onclick=\"document.location.href='".$_SERVER['PHP_SELF']."?clear_list=1&"
	.SID."';\" />";
echo "</form>
</body>
</html>";

