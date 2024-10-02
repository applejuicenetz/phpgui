<?php

use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\Kernel;
use appleJuiceNETZ\GUI\template;

$language = Kernel::getLanguage();
$lang = $language->translate();

$icon_img =new Icons();
$Downloadlist = new Downloads();
$template = new template();

if(empty($_GET['sort'])) $_GET['sort']="status";

//pause, fortsetzen, abbrechen, pdl setzen...
	$action_echo='';
	if(!empty($_GET['action']))
	{
		if(!empty($_GET['dl_id']))
		{
			if(empty($_GET['action_value'])) $_GET['action_value']="";
			$action_echo = $Downloadlist->action($_GET['action'],$_GET['dl_id'],$_GET['action_value']);
			echo'
	<div style="position: fixed;
  top: 120px;
  right: 5px;
  z-index: 300;
  opacity: 0.9;">' . template::toast($_GET['site'], $_GET['action'], "info") . '</div>
';
	}
	
	
}

$Downloadlist->refresh_cache();

echo "<form action=\"\" name=\"dl_form\" onsubmit=\"return false\">";


echo'<div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="card mb-4">
                            <div class="card-body row">
        						<div class="col-sm-4 mb-4">
								<div class="input-group">
                            	
  <button class="btn btn-outline-secondary" type="button" onclick="javascript:dec_pdl()"><i class="fa fa-minus"></i></button>
  <input type="text" size="6" class="form-control" id="pdl" name="pdl" value="1.0">
  <button class="btn btn-outline-secondary" type="button" onclick="javascript:inc_pdl()"><i class="fa fa-plus"></i></button>
  <button class="btn btn-outline-secondary" type="button" onclick="dlaction(\'setpowerdownload\')">' . $lang->Downloads->set_pdl . '</button>
  </div></div>
  <div class="col-sm-8 d-flex justify-content-end mb-4">
								
  <div class="input-group d-flex justify-content-end">
  
  <button class="btn btn-outline-secondary text-warning" id="liveToastBtn" type="button" onclick="javascript:dlaction(\'pausedownload\')"><i class="fa fa-pause"></i></button>
  <button class="btn btn-outline-secondary text-success" type="button" onclick="javascript:dlaction(\'resumedownload\')"><i class="fa fa-play"></i></button>
  <button class="btn btn-outline-secondary text-danger" type="button" onclick="javascript:dlaction(\'canceldownload\')"><i class="fa fa-times"></i></button>
  <button class="btn btn-outline-secondary" type="button" onclick="javascript:dlaction(\'settargetdir\')"><i class="fa fa-folder"></i></button>
  <button class="btn btn-outline-secondary text-danger" type="button" onclick="location.href=\'index.php?site=downloads&action=cleandownloadlist&dl_id=1\'"><i class="fa fa-trash"></i></button>
 
</div>
</div>';
//Tabellenüberschrift
echo'<div class="table-responsive">
<table class="table border mb-0">
                      <thead class="fw-semibold text-nowrap">
                        <tr class="align-middle">
                          <th class="bg-body-secondary"></th>
                          <th class="bg-body-secondary">'.$lang->Downloads->filename.'</th>
                          <th class="bg-body-secondary">'.$lang->Downloads->statuss.'</th>
                          <th class="bg-body-secondary">'.$lang->Downloads->progress.'</th>
                          <th class="bg-body-secondary text-center">' . $lang->Downloads->pdl . '</th>
                          <th class="bg-body-secondary">'.$lang->Downloads->speed.'</th>
                          <th class="bg-body-secondary"></th>
                        </tr>
                      </thead>
                      <tbody>
                       ';	

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
	foreach(array_keys($downloadids) as $a){
		//sieht doch etwas uebersichtlicher aus :)
		$current_download = $Downloadlist->download($a);
		
		$fortschritt=&$current_download['phpaj_DONE'];
		$balken = round($fortschritt, 2);
		$rest= $current_download["phpaj_REST"];
		$rest = subs::sizeformat($rest);
			
			
		echo'<tr>';
		echo "<script type=\"text/javascript\">\n<!--\n"
				."dl_names[$a]='".addslashes($current_download['FILENAME'])."';\n"
				."dl_pdl[$a]=".((($current_download['POWERDOWNLOAD'])+10)/10).";\n"
				."dl_ids[$a]=0;\n"
				."dl_subdirs[$a]=$subdircounter;\n"
				."//-->\n</script>\n";
			
		echo'<tr class="align-middle" id="zeile_' . $a . '" onclick="change(' . $a . ');">
                          <td>
                        	<input class="form-check-input" type="checkbox" onclick="change(' . $a . ');" id="dlcheck_' . $a . '">
                          </td>
                          <td>
                            <div class="text-nowrap" id="nametd_' . $a . '">
                            	<a onclick="javascript:rename(' . $a . ')" title="' . $lang->Downloads->rename . '">
                            		' . substr($current_download['FILENAME'], 0, 40) . '
                            	</a>
                            </div>
                            <div class="small text-body-secondary text-nowrap">
                            <span><a onclick="location.href=\'index.php?site=dl_users&dl_id=' . $a . ' \'" title="Mehr Info">
					' . ($current_download['phpaj_quellen_queue'] + $current_download['phpaj_quellen_dl']) . '/' . $current_download['phpaj_quellen_gesamt']
					.'</a></span> | ' . subs::sizeformat($current_download['SIZE']) . '' .subs::parts($current_download['FILENAME']) . '</div>
                          </td>
                          <td class="text-center">
                            ' . $Downloadlist->status($current_download['phpaj_STATUS']) . '
                          </td>
                          <td>
                            <div class="d-flex justify-content-between align-items-baseline">
                              <div class="fw-semibold">' . $balken . '%</div>
                              <div class="text-nowrap small text-body-secondary ms-3">' . $rest . '- ';
                              if(!empty($current_download['phpaj_dl_speed'])){
								$restzeit=$current_download['phpaj_REST']/$current_download['phpaj_dl_speed'];
								$stunden=$restzeit/3600;
								if($stunden<24){
									printf("%02d:%02d:%02d",$stunden,($restzeit%3600)/60,$restzeit%60);
								}else{
									printf("%.1fd",$stunden/24);
								}}
			echo'</div>
                            </div>
                            <div class="progress progress-thin">
                              <div class="progress-bar bg-success" role="progressbar" style="width: ' . $balken . '%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          </td>
                          <td class="text-center">
                          
                            ' . ((($current_download['POWERDOWNLOAD'])+10)/10) . '
                          </td>
                          <td>
                            ' . subs::sizeformat($current_download['phpaj_dl_speed']) . '
                          </td>
                          <td>
                            <div class="dropdown">
                              <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg class="icon">
                                  <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-options"></use>
                                </svg>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item" href="#">Info</a><a class="dropdown-item" href="#">Edit</a><a class="dropdown-item text-danger" href="#">Delete</a></div>
                            </div>
                          </td>
                        </tr>';
		
			
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