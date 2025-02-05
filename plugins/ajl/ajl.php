<?php

use appleJuiceNETZ\appleJuice\Core;

?>
<script>
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
</script>
<?php
echo'<div class="card mb-4">
		<div class="card-body">
			<form name="conselect" methode="post" action"' . $phpaj_ownurl . '" enctype="multipart/form-data">
				<div class="row mb-3">
					<label for="colFormLabelSm" class="col-sm-2 col-form-label col-form-label-sm">
						Filetype
					</label>
					<div class="col-sm-10">
    					<select class="form-select" name="filetype" aria-label="Default select example">
							<option selected>Open this select menu</option>
							<option value="ajl">.ajl-file</option>
							<option value="text">text/html</option>
						</select>
					</div>
				</div>
				<div class="row mb-3">
					<label for="colFormLabelSm" class="col-sm-2 col-form-label col-form-label-sm">
						File
					</label>
					<div class="col-sm-10">
    					<div class="form-check">
							<input class="form-check-input" type="radio" name="source" value="upload" onclick="layout(\'upload\');" checked/>
							<label class="form-check-label" for="flexRadioDefault1">
    							Upload File
							</label><br>
							<input class="form-check-input" type="radio" name="source" value="textarea" onclick="layout(\'txt\');">
							<label class="form-check-label" for="flexRadioDefault1">
    							Text
							</label>
						</div>
					</div>
				</div>
				<div class="row mb-3" id="uploadfeld">
					<label for="colFormLabelSm" class="col-sm-2 col-form-label col-form-label-sm">
						File
					</label>
					<div class="col-sm-10">
    					<div class="form-check">
							<input class="form-control" type="file" name="userfile">
						</div>
					</div>
				</div>
				<div class="row mb-3" id="linktext"  style="display:none;">
					<label for="colFormLabelSm" class="col-sm-2 col-form-label col-form-label-sm">
						Links
					</label>
					<div class="col-sm-10">
    					<div class="form-check">
							<textarea class="form-control" name="linktext">
							</textarea>
						</div>
					</div>
				</div>
				<div class="row mb-3">
					<label for="colFormLabelSm" class="col-sm-2 col-form-label col-form-label-sm">
						Dowload to subdir
					</label>
					<div class="col-sm-10">
    					<div class="form-check">
							<input class="form-control" type="text" name="subdir">
							</textarea>
						</div>
					</div>
				</div>
				<input type="hidden" name="MAX_FILE_SIZE" value="' . (200*1024) . '">
				<input type="hidden" name="show" value="' . $phpaj_show . '">
			</form>
		</div>
	</div>	

';
if(!empty($_POST['source'])){
	if($_POST['source']=="upload" && !empty($_FILES['userfile']['name'])){
		echo $_FILES['userfile']['name'].":<br />";
		$ajl_file=file($_FILES['userfile']['tmp_name']);
	}else{
		$ajl_file=explode("\n",$_POST['linktext']);
	}
	$core = new Core();
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
				echo htmlspecialchars($ajl_file[$i])." (".subs::sizeformat($ajl_file[$i+2])
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
						.subs::sizeformat($linkinfo[3]).") &rArr; "
						.$core->command("function","processlink?link="
						.rawurlencode($link)."&subdir="
						.rawurlencode($_POST['subdir']));
					echo "<br />";
				}
			break;
	}

}
