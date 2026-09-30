<?php

use appleJuiceNETZ\appleJuice\Core;
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
$core = new Core();

// fetch current download speed and max speed setting
$modified_dl = $core->command("xml", "modified.xml?filter=informations");
$temp_dl = array_keys($modified_dl['INFORMATION']);
$info_dl =& $modified_dl['INFORMATION'][$temp_dl[0]];
$current_dl_speed = (int)$info_dl['DOWNLOADSPEED']; // bytes/s

$settings_xml = $core->command("xml", "settings.xml");
$max_dl_bytes = (int)$settings_xml["MAXDOWNLOAD"]["VALUES"]["CDATA"]; // bytes/s, 0 = unlimited
$max_dl_kb = $max_dl_bytes / 1024;

if(empty($_GET['sort'])) $_GET['sort']="status";

// sort direction: 'asc' or 'desc', converted to 0/1 for ajsort
$sort_dir_str = $_GET['sort_dir'] ?? null;
$sort_dir = ($sort_dir_str === 'asc') ? 0 : (($sort_dir_str === 'desc') ? 1 : null);

// default directions per sortable column (null = use ids() default)
$sort_defaults = ['name' => 'asc', 'status' => 'asc', 'done' => 'desc', 'pdl' => 'desc'];

// build a sort link for a column header
function dl_sort_link($field, $label, $current_sort, $current_dir_str, $defaults) {
    $base = '?site=downloads&sort=' . $field;
    if ($current_sort === $field) {
        $new_dir = ($current_dir_str === 'asc') ? 'desc' : 'asc';
        $arrow   = ($current_dir_str === 'asc') ? ' ↑' : ' ↓';
    } else {
        $new_dir = $defaults[$field] ?? 'asc';
        $arrow   = '';
    }
    return '<a href="' . $base . '&sort_dir=' . $new_dir . '" style="color:inherit;text-decoration:none;">'
        . htmlspecialchars($label) . $arrow . '</a>';
}

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

$speed_bar_pct = 0;
$speed_bar_label = subs::sizeformat($current_dl_speed) . '/s';
if ($max_dl_bytes > 0) {
    $speed_bar_pct = min(100, round(($current_dl_speed / $max_dl_bytes) * 100, 1));
    $speed_bar_label .= ' / ' . subs::sizeformat($max_dl_bytes, 2, true) . '/s';
} else {
    $speed_bar_label .= ' / &#8734;';
}
$speed_bar_color = 'bg-info';

echo '<div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="card mb-4">
                            <div class="card-body row align-items-center">
        						<div class="col-auto mb-2">
								<div class="input-group input-group-sm">
  <span class="input-group-text"><i class="fa fa-tachometer"></i></span>
  <input type="text" inputmode="decimal" class="form-control" style="width:70px" id="maxdl" name="maxdl" value="' . $max_dl_kb . '" onkeydown="if(event.key===\'Enter\'){applyMaxDl();}">
  <button class="btn btn-outline-secondary active" type="button" id="maxdl_kb" onclick="setDlUnit(\'kb\')">KB/s</button>
  <button class="btn btn-outline-secondary" type="button" id="maxdl_mb" onclick="setDlUnit(\'mb\')">MB/s</button>
  <button class="btn btn-outline-secondary" type="button" onclick="applyMaxDl()" title="' . $lang->Settings->save . '"><i class="fa fa-check"></i></button>
  </div></div>
  <div class="col mb-2">
    <div class="d-flex align-items-center">
      <div class="progress flex-grow-1" style="height:20px" title="' . $speed_bar_label . '">
        <div class="progress-bar ' . $speed_bar_color . '" id="aj-dl-speed-bar" role="progressbar" style="width:' . $speed_bar_pct . '%">' . $speed_bar_label . '</div>
      </div>
    </div>
  </div>
  <div class="col-auto mb-2">
								<div class="input-group input-group-sm">
  <button class="btn btn-outline-secondary" type="button" onclick="javascript:dec_pdl()"><i class="fa fa-minus"></i></button>
  <input type="text" class="form-control" style="width:50px" id="pdl" name="pdl" value="1.0">
  <button class="btn btn-outline-secondary" type="button" onclick="javascript:inc_pdl()"><i class="fa fa-plus"></i></button>
  <button class="btn btn-outline-secondary" type="button" onclick="dlaction(\'setpowerdownload\')">' . $lang->Downloads->set_pdl . '</button>
  </div></div>
  <div class="col-auto mb-2">
  <div class="input-group input-group-sm">
  <button class="btn btn-outline-secondary text-warning" type="button" onclick="javascript:dlaction(\'pausedownload\')"><i class="fa fa-pause"></i></button>
  <button class="btn btn-outline-secondary text-success" type="button" onclick="javascript:dlaction(\'resumedownload\')"><i class="fa fa-play"></i></button>
  <button class="btn btn-outline-secondary text-danger" type="button" onclick="javascript:dlaction(\'canceldownload\')"><i class="fa fa-times"></i></button>
  <button class="btn btn-outline-secondary" type="button" onclick="javascript:dlaction(\'settargetdir\')"><i class="fa fa-folder"></i></button>
  <button class="btn btn-outline-secondary text-primary" type="button" onclick="location.href=\'index.php?site=downloads&action=cleandownloadlist&dl_id=1\'"><i class="fa fa-magic"></i></button>
</div>
</div>';
//Tabellenüberschrift
echo'<div class="table-responsive">
<table class="table border mb-0">
                      <thead class="fw-semibold text-nowrap">
                        <tr class="align-middle">
                          <th class="bg-body-secondary" style="width:1%"><input type="checkbox" onclick="dlSelectAll(this)"></th>
                          <th class="bg-body-secondary" style="min-width:180px">'.dl_sort_link('name',    $lang->Downloads->filename, $_GET['sort'], $sort_dir_str, $sort_defaults).' <a href="#" onclick="toggleDlFilter(); return false;"><i class="fa fa-filter"></i></a><div class="mt-1" id="dl_filter_box" style="display:none"><input type="text" class="form-control form-control-sm" id="dl_filter" placeholder="Filter..." oninput="filterDownloads(this.value)"></div></th>
                          <th class="bg-body-secondary text-center" style="width:1%">'.dl_sort_link('status',  $lang->Downloads->statuss,  $_GET['sort'], $sort_dir_str, $sort_defaults).'</th>
                          <th class="bg-body-secondary" style="width:25%">'.dl_sort_link('done',    $lang->Downloads->progress, $_GET['sort'], $sort_dir_str, $sort_defaults).'</th>
                          <th class="bg-body-secondary text-center" style="width:1%">'.dl_sort_link('pdl', $lang->Downloads->pdl, $_GET['sort'], $sort_dir_str, $sort_defaults).'</th>
                          <th class="bg-body-secondary" style="width:1%">'.$lang->Downloads->speed.'</th>
                          <th class="bg-body-secondary" style="width:1%"></th>
                        </tr>
                      </thead>
                      <tbody>
                       ';	

$subdircounter=0;

//alle downloads zeigen
foreach(array_keys($Downloadlist->subdirs) as $subdir){
	$subdircounter++;
	$downloadids=$Downloadlist->ids($_GET['sort'],$subdir,$sort_dir); //ids der downloads sortiert holen
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
			
		$eta = '';
		if(!empty($current_download['phpaj_dl_speed'])){
			$restzeit=$current_download['phpaj_REST']/$current_download['phpaj_dl_speed'];
			$stunden=$restzeit/3600;
			if($stunden<24){
				$eta = sprintf("%02d:%02d:%02d",$stunden,($restzeit%3600)/60,$restzeit%60);
			}else{
				$eta = sprintf("%.1fd",$stunden/24);
			}
		}
		echo'<tr class="align-middle" id="zeile_' . $a . '">
                          <td>
                        	<input class="form-check-input" type="checkbox" onclick="change(' . $a . ');" id="dlcheck_' . $a . '">
                          </td>
                          <td style="max-width:0">
                            <div class="text-truncate" id="nametd_' . $a . '">
                            	<a onclick="javascript:rename(' . $a . ')" title="' . htmlspecialchars($current_download['FILENAME']) . '">
                            		' . htmlspecialchars($current_download['FILENAME']) . '
                            	</a>
                            </div>
                            <div class="small text-body-secondary text-nowrap">
                            <span data-aj="sources"><a onclick="location.href=\'index.php?site=dl_users&dl_id=' . $a . ' \'" title="Mehr Info">
					' . ($current_download['phpaj_quellen_queue'] + $current_download['phpaj_quellen_dl']) . '/' . $current_download['phpaj_quellen_gesamt']
					.'</a></span> | ' . subs::sizeformat($current_download['SIZE']) . '' .subs::parts($current_download['FILENAME']) . '</div>
                          </td>
                          <td class="text-center" data-aj="status">
                            ' . $Downloadlist->status($current_download['phpaj_STATUS']) . '
                          </td>
                          <td>
                            <div class="d-flex justify-content-between align-items-baseline">
                              <div class="fw-semibold" data-aj="done-pct">' . $balken . '%</div>
                              <div class="text-nowrap small text-body-secondary ms-3" data-aj="rest">' . $rest . '- ' . $eta . '</div>
                            </div>
                            <div class="progress progress-thin">
                              <div class="progress-bar bg-success" role="progressbar" style="width: ' . $balken . '%" data-aj="progress-bar"></div>
                            </div>
                          </td>
                          <td class="text-center" data-aj="pdl">
                            ' . ((($current_download['POWERDOWNLOAD'])+10)/10) . '
                          </td>
                          <td data-aj="speed">
                            ' . subs::sizeformat($current_download['phpaj_dl_speed']) . '/s
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

echo "</table></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>";

echo "</form>";

// Pass max download speed to JS for live speed bar updates
echo '<script>var aj_max_dl_bytes = ' . $max_dl_bytes . ';</script>';
