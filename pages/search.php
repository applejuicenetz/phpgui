<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Search;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$core = new Core();
$Search = new Search();
$subs = new subs;
$template = new template();

//Language
$language = Kernel::getLanguage();
$lang = $language->translate();

if(empty($_GET['searchid'])) $_GET['searchid']="alles";

$action_echo='';
//suchanfrage an core uebergeben
if(!empty($_POST['searchstring'])){
	$_POST['searchstring']=trim($_POST['searchstring']);
	$Search->start($_POST['searchstring']);
}

//suche abbrechen
if(!empty($_GET['cancelid'])){
	$Search->cancel($_GET['cancelid']);
}


//suche loeschen
if(!empty($_GET['deleteid'])){
	$action_echo .= $Search->delete($_GET['deleteid']);
}

//alle ergebnisse loeschen
if(!empty($_GET['deleteall'])){
	echo $Search->delete_all();
}

// mehrere dateien herunterladen
if(!empty($_POST['selected_links'])){
    $downloaded = 0;
    foreach($_POST['selected_links'] as $raw_link){
        if($core->command('function', 'processlink?link=' . urlencode($raw_link)) === 'ok'){
            $downloaded++;
        }
    }
    if($downloaded > 0){
        $template->alert("success", $lang->Downloads->get_start, $downloaded . ' Datei(en) zum Download hinzugef&uuml;gt.');
    }
}

if($_GET['searchid'] == "alles"){
	$active_all = "active";
}
// Datei download
if( !empty( $_GET['link'] ) )
{
	$linkss = $_GET['link'];

	$regexe = [
        // ajfsp://file|ajcore-0.31.149.110.jar|653f4d793595e65bbbe58c0c55620589|313164/
        '#ajfsp://(file)\|([^|]*)\|([a-z0-9]{32})\|([\d]*)/#',

        // ajfsp://server|knastbruder.applejuicenet.de|9855/
        '#ajfsp://(server)\|([^|]*)\|([\d]{1,5})/#',

        // ajfsp://file|ajcore-0.31.149.110.so|653f4d793595e65bbbe58c0c55620589|313164|123.123.123.123:9850/
        '#ajfsp://(file)\|([^|]*)\|([a-z0-9]{32})\|([\d]*)\|[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}:[\d]{1,5}/#',

        // ajfsp://file|ajcore-0.31.149.110.jar|653f4d793595e65bbbe58c0c55620589|313164|123.123.123.123:9850:knastbruder.applejuicenet.de:9855/
        '#ajfsp://(file)\|([^|]*)\|([a-z0-9]{32})\|([\d]*)\|[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}:[\d]{1,5}:[^:]*:[\d]{1,5}/#',
    ];

	$links = [];
	foreach ($regexe as $regex) {
        preg_match_all($regex, urldecode($linkss), $matches, PREG_SET_ORDER);
        $links = array_merge($links, $matches);
    }

foreach ($links as $link) {

        //Infos fr Dateilink anzeigen + im hauptfenster die downloads zeigen
        if ('file' === $link[1]) {

            $text = htmlspecialchars($link[2]) . ' (' . subs::sizeformat($link[4]) . ')';

            if ($core->command('function', 'processlink?link=' . urlencode($link[0])) == "ok") {
                $template->alert("success", $lang->Downloads->get_start, $text);
            }

        }
}    
}
//Searchcontent
	//Suchformular
	echo'<div class="row clearfix">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                            	<form action="?site=search&'.SID.'" method="post">
                                	<div class="input-group mb-3">
                                		<input type="text" class="form-control" name="searchstring" aria-describedby="button-addon2">
                                		<button class="btn btn-outline-primary" type="submit">' . $lang->Search->button . '</button>
									</div>
								</form>
								
                            </div>
                        </div>
                    </div></div>';

echo'<div class="row">
  <div class="col-sm-3 col-lg-3">
    <div class="list-group" id="list-tab" role="tablist">
    
      <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center active" id="list-search-all" data-coreui-toggle="list" href="#search-all" role="tab" aria-controls="search-all">
    	' . $lang->Search->all . '
    	<span id="aj-search-badge-all" class="badge text-bg-primary rounded-pill">' . $Search->cache['SEARCHENTRY_count'] . '</span>
		</a>';
//Tabellen�berschrift
$Search->refresh_cache();

$Search->process_results();

//link fuer alle ergebnisse
if(!empty($Search->cache['SEARCH'])){
	//links fuer die einzelnen suchen
	foreach(array_keys($Search->cache['SEARCH']) as $b){
		
		
		// Suche aktiv oder inaktiv
		if($Search->cache['SEARCH'][$b]['RUNNING']==="true"){
			$button = '<div class="mb-4">
    		<a class="btn btn-warning" href="?site=search&cancelid=' . $b . '"><i class="fa fa-cross"></i> ' . $lang->Search->cancle_search . '</a>
		</div>';	
		}else{
			$button = '<div class="mb-4">
    		<a class="btn btn-danger" href="?site=search&deleteid=' . $b . '"><i class="fa fa-trash"></i> ' . $lang->Search->delet_search . '</a>
		</div>';
		}
		//name der suche + zahl der ergebnisse
		echo'<a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" id="list-search-' . $b . '" data-coreui-toggle="list" href="#search-' . $b . '" role="tab" aria-controls="search-' . $b . '">
    	' . $Search->cache['SEARCH'][$b]['SEARCHTEXT'] . '
    	<span id="aj-search-badge-' . $b . '" class="badge text-bg-primary rounded-pill">' . $Search->cache['SEARCH'][$b]['phpaj_FOUNDFILES'] . '</span>
		</a>
      ';
		}
}
echo'
      </div>
  </div>
  <div class="col-sm-9 col-lg-9">
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show active" id="search-all" role="tabpanel" aria-labelledby="search-all">
    	<div class="mb-4">
    		<a class="btn btn-danger" href="?site=search&deleteall=1"><i class="fa fa-trash"></i> ' . $lang->Search->delet . '</a>
		</div>';

if(empty($_GET['sort'])) $_GET['sort'] = "name";
$sort_dir_str = $_GET['sort_dir'] ?? null;
$sort_dir = ($sort_dir_str === 'asc') ? 0 : (($sort_dir_str === 'desc') ? 1 : null);
$sort_defaults = ['name' => 'asc', 'size' => 'desc', 'count' => 'desc', 'format' => 'asc'];

function search_sort_link($field, $label, $current_sort, $current_dir_str, $defaults) {
    $base = '?site=search&sort=' . $field;
    if ($current_sort === $field) {
        $new_dir = ($current_dir_str === 'asc') ? 'desc' : 'asc';
        $arrow   = ($current_dir_str === 'asc') ? ' ↑' : ' ↓';
    } else {
        $new_dir = $defaults[$field] ?? 'desc';
        $arrow   = '';
    }
    return '<a href="' . $base . '&sort_dir=' . $new_dir . '" style="color:inherit;text-decoration:none;">'
        . $label . $arrow . '</a>';
}

//Sortieren
if(!empty($Search->cache['SEARCHENTRY'])){
    $searchsort = $Search->sortieren($_GET['sort'], $sort_dir);
}



//suchergebnisse anzeigen
echo'<form method="post" action="?site=search&' . SID . '">
<div class="mb-3">
	<button type="submit" class="btn btn-primary"><i class="fa fa-download"></i> Alle markierten Eintr&auml;ge herunterladen</button>
</div>
<div class="table-responsive">
		  <table class="table border mb-0">
			<thead class="fw-semibold text-nowrap">
			<tr class="align-middle">
				<th class="bg-body-secondary" style="width:1%"><input type="checkbox" onclick="searchSelectAll(this)"></th>
				<th class="bg-body-secondary" style="min-width:180px">' . search_sort_link('name', $lang->Search->name, $_GET['sort'], $sort_dir_str, $sort_defaults) . ' <a href="#" onclick="toggleSearchFilter(this); return false;"><i class="fa fa-filter"></i></a><div class="mt-1" style="display:none"><input type="text" class="form-control form-control-sm search-filter-name" placeholder="Filter..." oninput="filterSearchResults(this)"></div></th>
				<th class="bg-body-secondary" style="width:1%">' . search_sort_link('size', $lang->Search->size, $_GET['sort'], $sort_dir_str, $sort_defaults) . '</th>
				<th class="bg-body-secondary" style="width:1%">' . search_sort_link('format', $lang->Search->format, $_GET['sort'], $sort_dir_str, $sort_defaults) . ' <a href="#" onclick="toggleSearchFilter(this); return false;"><i class="fa fa-filter"></i></a><div class="search-format-buttons" style="display:none"></div></th>
				<th class="bg-body-secondary" style="width:1%">' . search_sort_link('count', $lang->Search->sources, $_GET['sort'], $sort_dir_str, $sort_defaults) . '</th>
				<th class="bg-body-secondary" style="width:1%">Info</th>
				<th class="bg-body-secondary" style="width:1%"></th>
			</tr>
			</thead>
			<tbody id="aj-search-tbody-all">
';
if(!empty($Search->cache['SEARCHENTRY'])){
	foreach(array_keys($searchsort) as $a ){
		$cur_search =& $Search->cache['SEARCHENTRY'][$a];

		$sort_names=array();
			foreach(array_keys($cur_search['FILENAME']) as $b){
				$sort_names["$b"]=$cur_search['FILENAME'][$b]['USER'];
			}
		arsort($sort_names,SORT_NUMERIC);
		$names=array_keys($sort_names);
		$ajfsp_link = "ajfsp://file|" . addslashes(htmlspecialchars($names[0])). "|" . $cur_search['CHECKSUM'] . "|" . $cur_search['SIZE'] . "/";
		$rel_info_file = '';
		if(!empty($_ENV['REL_INFO']))
		{
        	$rel_info_file = '<a target="_blank" href="' . sprintf($_ENV['REL_INFO'], $ajfsp_link) . '"><i class="fa fa-info-circle text-primary"></i></a>';
		}
		echo'<tr class="align-middle" data-entry-id="' . $a . '" data-search-name="' . htmlspecialchars($names[0]) . '" data-search-format="' . htmlspecialchars($cur_search['phpaj_FORMAT']) . '">
				<td><input class="form-check-input" type="checkbox" name="selected_links[]" value="' . htmlspecialchars($ajfsp_link) . '"></td>
				<td><b>' . htmlspecialchars($names[0]) . '</b></td>
				<td class="text-nowrap">' . subs::sizeformat($cur_search['SIZE']) . '</td>
				<td class="text-nowrap">' . htmlspecialchars($cur_search['phpaj_FORMAT']) . '</td>
				<td class="text-nowrap" data-aj="sources">' . $cur_search['phpaj_COUNT'] . '</td>
				<td data-aj="info">' . $rel_info_file . '</td>
				<td><a href="?site=search&link=' . $ajfsp_link . '" class="btn btn-success btn-sm"><i class="fa fa-download"></i></a></td>
			</tr>';
		}
}
echo "</tbody></table></div></form>";
echo"</div>";
        
//link fuer alle ergebnisse
if(!empty($Search->cache['SEARCH'])){
	//links fuer die einzelnen suchen
	foreach(array_keys($Search->cache['SEARCH']) as $searchid){
		echo'<div class="tab-pane fade" id="search-' . $searchid . '" role="tabpanel" aria-labelledby="search-' . $searchid . '">'.$button;
      
		if($searchid !=="alles"){
	
		$current_search=&$Search->cache['SEARCH'][$searchid];
		if(($current_search['SUMSEARCHES']+$current_search['OPENSEARCHES'])>0)
		{
			$current_search_percent=(($current_search['SUMSEARCHES']*100) / ($current_search['SUMSEARCHES']+$current_search['OPENSEARCHES']));
			$balken = round($current_search_percent,2);
			
			$details = $current_search['SUMSEARCHES']."/".($current_search['SUMSEARCHES']+$current_search['OPENSEARCHES']);
			if($balken != 100)
			{
				echo'<div id="aj-search-progress-' . $searchid . '" class="progress mb-3">
                		<div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: '.$balken.'%" aria-valuenow="'.$balken.'" aria-valuemin="0" aria-valuemax="100">
                			'.$balken.' %
                		</div>
            		</div>';
			}
		
	}
}

echo'<form method="post" action="?site=search&' . SID . '">
<div class="mb-3">
	<button type="submit" class="btn btn-primary"><i class="fa fa-download"></i> Alle markierten Eintr&auml;ge herunterladen</button>
</div>
<div class="table-responsive">
			  <table class="table border mb-0">
				<thead class="fw-semibold text-nowrap">
				<tr class="align-middle">
					<th class="bg-body-secondary" style="width:1%"><input type="checkbox" onclick="searchSelectAll(this)"></th>
					<th class="bg-body-secondary" style="min-width:180px">' . search_sort_link('name', $lang->Search->name, $_GET['sort'], $sort_dir_str, $sort_defaults) . ' <a href="#" onclick="toggleSearchFilter(this); return false;"><i class="fa fa-filter"></i></a><div class="mt-1" style="display:none"><input type="text" class="form-control form-control-sm search-filter-name" placeholder="Filter..." oninput="filterSearchResults(this)"></div></th>
					<th class="bg-body-secondary" style="width:1%">' . search_sort_link('size', $lang->Search->size, $_GET['sort'], $sort_dir_str, $sort_defaults) . '</th>
					<th class="bg-body-secondary" style="width:1%">' . search_sort_link('format', $lang->Search->format, $_GET['sort'], $sort_dir_str, $sort_defaults) . ' <a href="#" onclick="toggleSearchFilter(this); return false;"><i class="fa fa-filter"></i></a><div class="search-format-buttons" style="display:none"></div></th>
					<th class="bg-body-secondary" style="width:1%">' . search_sort_link('count', $lang->Search->sources, $_GET['sort'], $sort_dir_str, $sort_defaults) . '</th>
					<th class="bg-body-secondary" style="width:1%">Info</th>
					<th class="bg-body-secondary" style="width:1%"></th>
				</tr>
				</thead>
				<tbody id="aj-search-tbody-' . $searchid . '">
	';
//suchergebnisse anzeigen
if(!empty($Search->cache['SEARCHENTRY'])){
	foreach(array_keys($searchsort) as $a ){
		$cur_search =& $Search->cache['SEARCHENTRY'][$a];

		if($searchid!=="alles"
			&& $cur_search['SEARCHID'] != $searchid) continue;

		$sort_names=array();
			foreach(array_keys($cur_search['FILENAME']) as $b)
			{
				$sort_names["$b"]=$cur_search['FILENAME'][$b]['USER'];
			}
		arsort($sort_names,SORT_NUMERIC);
		$names=array_keys($sort_names);
		$ajfsp_link = "ajfsp://file|" . addslashes(htmlspecialchars($names[0])). "|" . $cur_search['CHECKSUM'] . "|" . $cur_search['SIZE'] . "/";
		$rel_info_file = '';
		if(!empty($_ENV['REL_INFO']))
		{
        	$rel_info_file = '<a target="_blank" href="' . sprintf($_ENV['REL_INFO'], $ajfsp_link) . '"><i class="fa fa-info-circle text-primary"></i></a>';
		}
		echo'<tr class="align-middle" data-entry-id="' . $a . '" data-search-name="' . htmlspecialchars($names[0]) . '" data-search-format="' . htmlspecialchars($cur_search['phpaj_FORMAT']) . '">
				<td><input class="form-check-input" type="checkbox" name="selected_links[]" value="' . htmlspecialchars($ajfsp_link) . '"></td>
				<td><b>' . htmlspecialchars($names[0]) . '</b></td>
				<td class="text-nowrap">' . subs::sizeformat($cur_search['SIZE']) . '</td>
				<td class="text-nowrap">' . htmlspecialchars($cur_search['phpaj_FORMAT']) . '</td>
				<td class="text-nowrap" data-aj="sources">' . $cur_search['phpaj_COUNT'] . '</td>
				<td data-aj="info">' . $rel_info_file . '</td>
				<td><a href="?site=search&link=' . $ajfsp_link . '" class="btn btn-success btn-sm"><i class="fa fa-download"></i></a></td>
			</tr>';
		}

	}
echo "</tbody></table></div></form></div>";

}
        
}
echo"</div></div></div>";
