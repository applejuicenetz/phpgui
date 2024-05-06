<?php

use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\Kernel;

$language = Kernel::getLanguage();
$lang = $language->translate();

$icon_img =new Icons();
$Downloadlist = new Downloads();

if(empty($_GET['sort'])) $_GET['sort']="status";

//pause, fortsetzen, abbrechen, pdl setzen...
	$action_echo='';
	if(!empty($_GET['action'])){
		if(!empty($_GET['dl_id'])){
			if(empty($_GET['action_value'])) $_GET['action_value']="";
		$action_echo = $Downloadlist->action($_GET['action'],
			$_GET['dl_id'],$_GET['action_value']);
			$Downloadlist->message($action_echo);
	}
}

$Downloadlist->refresh_cache();

echo "<form action=\"\" name=\"dl_form\" onsubmit=\"return false\">";


echo'<div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">';
          

                            	echo'<div class="input-group mb-2">
                            	
  <button class="btn btn-outline-secondary" type="button" onclick="javascript:dec_pdl()"><i class="fa fa-minus"></i></button>
  <input type="text" size="6" class="form-control" id="pdl" name="pdl" value="1.0">
  <button class="btn btn-outline-secondary" type="button" onclick="javascript:inc_pdl()"><i class="fa fa-plus"></i></button>
  <button class="btn btn-outline-secondary" type="button" onclick="dlaction(\'setpowerdownload\')">' . $lang->Downloads->set_pdl . '</button>
  </div>
  <div class="input-group align-right mb-4">
  
  <button class="btn btn-outline-secondary text-warning" type="button" onclick="javascript:dlaction(\'pausedownload\')"><i class="fa fa-pause"></i></button>
  <button class="btn btn-outline-secondary text-success" type="button" onclick="javascript:dlaction(\'resumedownload\')"><i class="fa fa-play"></i></button>
  <button class="btn btn-outline-secondary text-danger" type="button" onclick="javascript:dlaction(\'canceldownload\')"><i class="fa fa-times"></i></button>
  <button class="btn btn-outline-secondary" type="button" onclick="javascript:dlaction(\'settargetdir\')"><i class="fa fa-folder"></i></button>
  <button class="btn btn-outline-secondary text-danger" type="button" onclick="location.href=\'index.php?site=downloads&action=cleandownloadlist&dl_id=1\'"><i class="fa fa-trash"></i></button>
 
</div>';
//Tabellenüberschrift
echo'<div class="table-responsive">
			  <table class="table border mb-0" data-click-to-select="true">
                      <thead class="fw-semibold text-nowrap">
                        <tr>
                    <th scope="col">#</th>
                    <th scope="col">'.$lang->Downloads->source.'</th>
                    <th scope="col">'.$lang->Downloads->filename.'</th>
                    <th scope="col">'.$lang->Downloads->statuss.'</th>
                    <th scope="col">'.$lang->Downloads->speed.'</th>
                    <th scope="col">'.$lang->Downloads->pdl.'</th>
                    <th scope="col">'.$lang->Downloads->size.'</th>
                    <th scope="col">'.$lang->Downloads->progress.'</th>
                  </tr>
                </thead>
                <tbody>';	

$spaltenzahl=9;
if(!empty($_ENV['REL_INFO'])) 
{
    $spaltenzahl++;
}

$subdircounter=0;

//alle downloads zeigen
foreach(array_keys($Downloadlist->subdirs) as $subdir){
	$subdircounter++;
	$downloadids=$Downloadlist->ids($_GET['sort'],$subdir); //ids der downloads sortiert holen
	if(!empty($subdir))
		//Unterverzeichnis
		echo "<tr><td colspan=\"$spaltenzahl\">"
		."<a href=\"javascript:togglesubdir(".$subdircounter.")\">"
		."<img id=\"img_$subdircounter\" "
		."src=\"../style/".$_SESSION['minus_icon']."\" border=\"0\" alt=\"\" />"
		."&nbsp;&nbsp;<b>".htmlspecialchars($subdir)."</b> (".count($downloadids).")</a>\n"
		."<span style=\"margin-left:5px\">"
		.$lang->System->select.":</span> "
		."<a href=\"javascript:select_sub(".$subdircounter.", 0);\">"
		.$lang->System->all."</a>, "
		."<a href=\"javascript:select_sub(".$subdircounter.", 1);\">"
		.$lang->System->none."</a></td></tr>\n";
	foreach(array_keys($downloadids) as $a){
		//sieht doch etwas uebersichtlicher aus :)
		$current_download = $Downloadlist->download($a);
		echo '<tr id="zeile_' . $a . '">';
		//checkbox zur auswahl
			echo "<td class='form-group'>
				<script type=\"text/javascript\">\n<!--\n"
				."dl_names[$a]='".addslashes($current_download['FILENAME'])."';\n"
				."dl_pdl[$a]=".((($current_download['POWERDOWNLOAD'])+10)/10).";\n"
				."dl_ids[$a]=0;\n"
				."dl_subdirs[$a]=$subdircounter;\n"
				."//-->\n</script>\n";
			echo "<input type=\"checkbox\" id=\"dlcheck_$a\""
				." onclick=\"change($a);\" /></td>\n";
			//quellenzahl (link zu dl details)
				echo "<td class=\"right\">"
					."<a onclick=\"location.href='index.php?site=dl_users&dl_id=".$a."'\" title=\"Mehr Info\">"
					.($current_download['phpaj_quellen_queue']
						+$current_download['phpaj_quellen_dl'])
					."/".$current_download['phpaj_quellen_gesamt']
					." (".$current_download['phpaj_quellen_dl'].")</a></td>\n";
			//Dateiname
			echo "<td id=\"nametd_$a\">"
				."<a onclick=\"javascript:rename($a)\" title=\"".$lang->Downloads->rename."\">";
			echo $current_download['FILENAME'] . "</a></td>\n";

           

            //status
			echo "<td>".$Downloadlist->status($current_download['phpaj_STATUS'])."</td>\n";
			//geschwindigkeit
            echo "<td class=\"right\" nowrap>"
				.subs::sizeformat($current_download['phpaj_dl_speed'])
				."/s</td>\n";
			//pdl wert
			echo "<td class=\"text-center\">"
				.((($current_download['POWERDOWNLOAD'])+10)/10)."</td>\n";
			//groesse
			echo "<td nowrap>".subs::sizeformat($current_download['SIZE'])."</td>";
			//Rest
			
			$fortschritt=&$current_download['phpaj_DONE'];
			$balken = round($fortschritt, 2);
			$rest= $current_download["phpaj_REST"];
			$rest = subs::sizeformat($rest);
			
			echo'<td>  <div class="d-flex justify-content-between align-items-baseline">
                              <div class="fw-semibold">' . $balken . '%</div>
                              <div class="text-nowrap small text-body-secondary ms-3">' . $rest . ' - ';
                              if(!empty($current_download['phpaj_dl_speed'])){
				$restzeit=$current_download['phpaj_REST']/$current_download['phpaj_dl_speed'];
				$stunden=$restzeit/3600;
				if($stunden<24)
					printf("%02d:%02d:%02d",$stunden,($restzeit%3600)/60,$restzeit%60);
				else
					printf("%.1fd",$stunden/24);
			}
			echo'</div>
                            </div>
                            <div class="progress progress-thin">
                              <div class="progress-bar bg-success" role="progressbar" style="width: ' . $balken . '%" aria-valuenow="' . $balken . '" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          ';
			
			echo'</td>';
			echo "</tr>\n";
	}
}
//alle/keine auswaehlen

echo "<tr><th colspan=\"$spaltenzahl\">\n";
echo $lang->System->select.": ";
echo "<a href=\"javascript:select_all(0);\">"
	.$lang->System->all."</a>, ";
echo "<a href=\"javascript:select_all(1);\">"
	.$lang->System->none."</a>"
	."</th></tr></table></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>";

echo "</form>";