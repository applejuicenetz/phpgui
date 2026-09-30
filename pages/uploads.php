<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\Kernel;

$language = Kernel::getLanguage();
$lang = $language->translate();

//standardmaessig nur laufende uploads zeigen
	if(empty($_GET['show_uplds'])) $_GET['show_uplds']=1;
	if(empty($_GET['show_queue'])) $_GET['show_queue']=1;

if(empty($_GET['ul_sort'])) $_GET['ul_sort'] = "status";
$ul_sort_dir_str = $_GET['ul_sort_dir'] ?? null;
$ul_sort_dir = ($ul_sort_dir_str === 'asc') ? 0 : (($ul_sort_dir_str === 'desc') ? 1 : null);
$ul_sort_defaults = ['name' => 'asc', 'status' => 'asc'];

function ul_sort_link($field, $label, $current_sort, $current_dir_str, $defaults) {
    $base = '?site=uploads&ul_sort=' . $field;
    if ($current_sort === $field) {
        $new_dir = ($current_dir_str === 'asc') ? 'desc' : 'asc';
        $arrow   = ($current_dir_str === 'asc') ? ' ↑' : ' ↓';
    } else {
        $new_dir = $defaults[$field] ?? 'asc';
        $arrow   = '';
    }
    return '<a href="' . $base . '&ul_sort_dir=' . $new_dir . '" style="color:inherit;text-decoration:none;">'
        . htmlspecialchars($label) . $arrow . '</a>';
}

$core_ul = new Core();
$Sharelist = new Share();
$Uploadlist = new Uploads();
$icon_img = new Icons();
$subs = new subs();

$Uploadlist->refresh_cache();

$modified_ul = $core_ul->command("xml", "modified.xml?filter=informations");
$temp_ul = array_keys($modified_ul['INFORMATION']);
$info_ul =& $modified_ul['INFORMATION'][$temp_ul[0]];
$current_ul_speed = (int)$info_ul['UPLOADSPEED'];
$settings_ul_xml = $core_ul->command("xml", "settings.xml");
$max_ul_bytes = (int)$settings_ul_xml["MAXUPLOAD"]["VALUES"]["CDATA"];
$max_ul_kb = $max_ul_bytes / 1024;

$ul_speed_bar_pct = 0;
$ul_speed_bar_label = subs::sizeformat($current_ul_speed) . '/s';
if ($max_ul_bytes > 0) {
    $ul_speed_bar_pct = min(100, round(($current_ul_speed / $max_ul_bytes) * 100, 1));
    $ul_speed_bar_label .= ' / ' . subs::sizeformat($max_ul_bytes, 2, true) . '/s';
} else {
    $ul_speed_bar_label .= ' / &#8734;';
}

$uploadusercount="0";
$uploaduserpercent="?";
if(!empty($Uploadlist->cache['IDS']['VALUES']['UPLOADID']))
	$uploadusercount=
		count($Uploadlist->cache['IDS']['VALUES']['UPLOADID']);
if(isset($Uploadlist->cache['phpaj_MAXUPLOADPOSITIONS']) && $Uploadlist->cache['phpaj_MAXUPLOADPOSITIONS'] > 0) {
	$uploaduserpercent= (int) ((($uploadusercount / $Uploadlist->cache['phpaj_MAXUPLOADPOSITIONS'])*100)+0.5);
}
echo "<form action=\"\" name=\"ul_form\" onsubmit=\"return false\">";

echo'<div class="row clearfix">
  <div class="col-sm-12">
    <div class="card mb-2">
      <div class="card-body row align-items-center">
        <div class="col-auto mb-2">
          <div class="input-group input-group-sm">
  <span class="input-group-text"><i class="fa fa-tachometer"></i></span>
  <input type="text" inputmode="decimal" class="form-control" style="width:70px" id="maxul" name="maxul" value="' . $max_ul_kb . '" onkeydown="if(event.key===\'Enter\'){applyMaxUl();}">
  <button class="btn btn-outline-secondary active" type="button" id="maxul_kb" onclick="setUlUnit(\'kb\')">KB/s</button>
  <button class="btn btn-outline-secondary" type="button" id="maxul_mb" onclick="setUlUnit(\'mb\')">MB/s</button>
  <button class="btn btn-outline-secondary" type="button" onclick="applyMaxUl()" title="' . $lang->Settings->save . '"><i class="fa fa-check"></i></button>
          </div>
        </div>
        <div class="col mb-2">
          <div class="d-flex align-items-center">
            <div class="progress flex-grow-1" style="height:20px" title="' . $ul_speed_bar_label . '">
              <div class="progress-bar bg-info" id="aj-ul-speed-bar" role="progressbar" style="width:' . $ul_speed_bar_pct . '%">' . $ul_speed_bar_label . '</div>
            </div>
          </div>
        </div>
        <div class="col-auto mb-2">
          <div class="align-right">
            '. strtr($lang->Uploads->limit, array("%percent"=>$uploaduserpercent)).'
          </div>
        </div>
      </div>
    </div>
  </div>
</div>';
echo '<script>var aj_max_ul_bytes = ' . $max_ul_bytes . ';</script>';

if(empty($Uploadlist->cache['UPLOAD']) && empty($Uploadlist->cache['UPLOAD'])){
	echo'<div class="text-center text-body-secondary py-5">
      <h1><i class="icon icon-xxl cil-frown text-danger"></i></h1>
      <p>Keine Uploads zur Zeit!</p>
    </div>';
}else{
// build combined sorted list
$all_upload_ids = array_merge(
    $Uploadlist->cache['phpaj_ids_ul'] ?? [],
    $Uploadlist->cache['phpaj_ids_queue'] ?? []
);
if ($_GET['ul_sort'] === 'name') {
    $name_map = [];
    foreach ($all_upload_ids as $id) {
        $upload = $Uploadlist->get_upload($id);
        $share  = $Sharelist->get_file($upload['SHAREID']);
        $name_map[$id] = $share['SHORTFILENAME'] ?? '';
    }
    ($ul_sort_dir ?? 0) === 0 ? asort($name_map, SORT_STRING) : arsort($name_map, SORT_STRING);
    $all_upload_ids = array_keys($name_map);
} else {
    $status_map = [];
    foreach ($all_upload_ids as $id) {
        $upload = $Uploadlist->get_upload($id);
        $status_map[$id] = $upload['phpaj_STATUS_SORT'];
    }
    ($ul_sort_dir ?? 0) === 0 ? asort($status_map, SORT_NUMERIC) : arsort($status_map, SORT_NUMERIC);
    $all_upload_ids = array_keys($status_map);
}

echo'<div class="row clearfix"><div class="col-sm-12 mb-4"><div class="card"><div class="table-responsive">
<table class="table border mb-0">
                      <thead class="fw-semibold text-nowrap">
                        <tr class="align-middle">
                        <th class="bg-body-secondary"></th>
                          <th class="bg-body-secondary">'.ul_sort_link('name',   $lang->Uploads->files,   $_GET['ul_sort'], $ul_sort_dir_str, $ul_sort_defaults).'</th>
                          <th class="bg-body-secondary">'.ul_sort_link('status', $lang->Uploads->statuss, $_GET['ul_sort'], $ul_sort_dir_str, $ul_sort_defaults).'</th>
                          <th class="bg-body-secondary">'.$lang->Uploads->progress.'</th>
                          <th class="bg-body-secondary">'.$lang->Uploads->speed.'</th>
                          <th class="bg-body-secondary"></th>
                        </tr>
                      </thead>
                      <tbody>';

if(!empty($Uploadlist->cache['UPLOAD'])){
    foreach($all_upload_ids as $a){
        $current_upload = $Uploadlist->get_upload($a);
        $current_shareid = $current_upload['SHAREID'];
        $current_share = $Sharelist->get_file($current_shareid);
        $is_active = $current_upload['STATUS'] === "1";

        $pdlwert = "";
        if($current_upload['PRIORITY'] > $current_share['PRIORITY']){
            $pdlwert = "(" . ((($current_upload['PRIORITY'] - $current_share['PRIORITY']) - 10) / 10) . ") ";
        }

        if($is_active){
            $fortschritt = number_format(
                (($current_upload['ACTUALUPLOADPOSITION'] - $current_upload['UPLOADFROM']) /
                 ($current_upload['UPLOADTO'] - $current_upload['UPLOADFROM'])) * 100, 2);
            $geladen = subs::sizeformat($current_upload['ACTUALUPLOADPOSITION'] - $current_upload['UPLOADFROM']);
            $progress_label = $fortschritt . '%';
            $progress_sub   = $geladen . '- ' . subs::sizeformat($current_upload['UPLOADTO'] - $current_upload['UPLOADFROM']);
            $progress_width = $fortschritt;
            $icon = $icon_img->directstate[$current_upload['DIRECTSTATE']];
        } else {
            $ul_timediff = isset($current_upload['LASTCONNECTION'])
                ? ($Uploadlist->cache['TIME']['VALUES']['CDATA'] - $current_upload['LASTCONNECTION']) / 1000
                : 0;
            $progress_label = sprintf("%dmin %02ds", $ul_timediff / 60, $ul_timediff % 60);
            $progress_sub   = subs::sizeformat($current_upload['UPLOADTO'] - $current_upload['UPLOADFROM']);
            $progress_width = 0;
            $icon = $icon_img->directstate['WAIT'];
        }

        echo'<tr class="align-middle" id="aj-ul-' . $a . '">
                          <td data-aj="icon">' . $icon . '</td>
                          <td>
                            <div class="text-nowrap">' . htmlspecialchars($current_share['SHORTFILENAME']) . '</div>
                            <div class="small text-body-secondary text-nowrap">
                              <span>' . $lang->Uploads->username . ': ' . htmlspecialchars(subs::cutstring($current_upload['NICK'], 30)) . '</span>
                              | ' . $lang->Uploads->pdl . ': ' . $pdlwert . $current_upload['PRIORITY'] . '
                            </div>
                          </td>
                          <td data-aj="status">' . subs::UploadStatus($current_upload['STATUS']) . '</td>
                          <td>
                            <div class="d-flex justify-content-between align-items-baseline">
                              <div class="fw-semibold" data-aj="progress-label">' . $progress_label . '</div>
                              <div class="text-nowrap small text-body-secondary ms-3" data-aj="progress-sub">' . $progress_sub . '</div>
                            </div>
                            <div class="progress progress-thin">
                              <div class="progress-bar bg-success" role="progressbar" style="width: ' . $progress_width . '%" data-aj="progress-bar"></div>
                            </div>
                          </td>
                          <td data-aj="speed">' . subs::sizeformat($current_upload['SPEED']) . '/s</td>
                          <td>
                            <div class="dropdown">
                              <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg class="icon"><use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-options"></use></svg>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item" href="#">Info</a><a class="dropdown-item" href="#">Edit</a><a class="dropdown-item text-danger" href="#">Delete</a></div>
                            </div>
                          </td>
                        </tr>';
    }
}

echo "</tbody></table></div></div></div></div>";
}