<script type="text/javascript">
<!--
function layout(typ){
	switch(typ){
		case 'upload':
			document.getElementById('linktext').style.display='none';
			document.getElementById('uploadfeld').style.display='block';
			break;
		default:
			document.getElementById('uploadfeld').style.display='none';
			document.getElementById('linktext').style.display='block';
			break;
	}
}
//-->
</script>
<?php
echo "<form name=\"conselect\" method=\"post\" action=\"$phpaj_ownurl\" "
	."enctype=\"multipart/form-data\">";
echo "<div style=\"float:left;\">";
echo "Filetype:\n";
echo "\n</div><div style=\"float:left;\">\n";
echo "<ul style=\"list-style:none; margin:0px; padding:0px\">";
echo "<li><input type=\"radio\" name=\"filetype\" value=\"ajl\" "
	."checked=\"checked\" />.ajl</li>\n";
echo "<li><input type=\"radio\" name=\"filetype\" value=\"text\" />txt/html</li>";
echo "\n</ul></div><div style=\"float:left;\">";
echo "File:\n";
echo "\n</div><div style=\"float:left;\">\n";
echo "<ul style=\"list-style:none; margin:0px; padding:0px\">";
echo "<li><input type=\"radio\" name=\"source\" value=\"upload\" "
	."checked=\"checked\" onclick=\"layout('upload');\" />Upload</li>\n";
echo "<li><input type=\"radio\" name=\"source\" value=\"txtarea\" "
	."onclick=\"layout('txt');\" />Textarea\n</li>\n</ul></div>";
echo "\n<div style=\"clear:left;\">\n";
echo "<input id=\"uploadfeld\" name=\"userfile\" type=\"file\" size=\"50\" />";
echo "<textarea id=\"linktext\"cols=\"100\" rows=\"20\" name=\"linktext\" "
	."style=\"display:none;\"></textarea>";
echo "<br />\n";
echo "Download to subdir: <input name=\"subdir\" size=\"25\" /><br /><br />\n";
echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".(200*1024)."\" />";
echo "<input type=\"hidden\" name=\"show\" value=\"".$phpaj_show."\" />";	// <- wichtig ;)
echo "<input type=\"submit\" value=\"OK\" />";
echo "</div></form>";
if(!empty($_POST['source'])){
	if($_POST['source']=="upload" && !empty($_FILES['userfile']['name'])){
		echo $_FILES['userfile']['name'].":<br />";
		$ajl_file=file($_FILES['userfile']['tmp_name']);
	}else{
		$ajl_file=explode("\n",$_POST['linktext']);
	}
	include_once "classes/class_core.php";
	$core = new Core;
	switch($_POST['filetype']){
		case "ajl":
			//anfang abschneiden
			while(!empty($ajl_file) && trim($ajl_file[0])!="100")
				array_shift($ajl_file);
			@array_shift($ajl_file);
			for($i=0;$i<(count($ajl_file)-2);$i+=3){
				$ajl_file[$i]=trim($ajl_file[$i]);
				$ajl_file[$i+1]=trim($ajl_file[$i+1]);
				$ajl_file[$i+2]=trim($ajl_file[$i+2]);
				if(empty($ajl_file[$i+2])) break;
				$link="ajfsp://file|".$ajl_file[$i]."|".$ajl_file[$i+1]."|"
					.$ajl_file[$i+2]."/";
				echo htmlspecialchars($ajl_file[$i])." (".sizeformat($ajl_file[$i+2])
					.") &rArr; ".$core->command("function","processlink?link="
					.rawurlencode($link)."&subdir="
					.rawurlencode($_POST['subdir']))."<br />";
			}
			break;
		default:
			$ajl_file=implode("",$ajl_file);
			preg_match_all("/ajfsp:\/\/file\\|(.*)\//U",$ajl_file,
				$link_array);
				foreach($link_array[0] as $link){
					$linkinfo=explode('|',$link);
					echo htmlspecialchars($linkinfo[1])." ("
						.sizeformat($linkinfo[3]).") &rArr; "
						.$core->command("function","processlink?link="
						.rawurlencode($link)."&subdir="
						.rawurlencode($_POST['subdir']));
					echo "<br />";
				}
			break;
	}

}
