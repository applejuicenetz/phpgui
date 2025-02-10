<?php

use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\Kernel;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Template\Downloadpage;
use appleJuiceNETZ\UI\Language;

$language = Language::getLanguage();


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
			template::toast($_GET['action'], "info");
	}
	
	
}

$Downloadlist->refresh_cache();

echo "<form action=\"\" name=\"dl_form\" onsubmit=\"return false\" method=\"post\">";


echo'<div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="card mb-4">
                            <div class="card-body row">
        						<div class="col-sm-4">
								<div class="input-group">
                            	
  <button class="btn btn-outline-secondary" type="button" onclick="javascript:dec_pdl()"><i class="fa fa-minus"></i></button>
  <input type="text" size="1" style="width: 10px;" class="form-control" id="pdl" name="pdl" value="1.0">
  <button class="btn btn-outline-secondary" type="button" onclick="javascript:inc_pdl()"><i class="fa fa-plus"></i></button>
  <button class="btn btn-outline-secondary" type="button" onclick="dlaction(\'setpowerdownload\')">' . $language->translate('Downloads.set_pdl') . '</button>
  </div></div>
  <div class="col-sm-8 d-flex justify-content-end">
								
  <div class="btn-group d-flex justify-content-end">
  <div class="btn-group btm-group" role="group">
  <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-coreui-toggle="dropdown" aria-expanded="false">
    ' . $language->translate('System.select') . '
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="javascript:select_all(0);">' . $language->translate('System.all') . '</a></li>
    <li><a class="dropdown-item" href="javascript:select_all(1);">' . $language->translate('System.none') . '</a></li>
  </ul>
</div>
  <button class="btn btn-outline-secondary text-warning" id="liveToastBtn" type="button" onclick="javascript:dlaction(\'pausedownload\')"><i class="fa fa-pause"></i></button>
  <button class="btn btn-outline-secondary text-success" type="button" onclick="location.href=\'?site=Downloads&act=startdownload\'"><i class="fa fa-play"></i></button>
  <button class="btn btn-outline-secondary text-danger" type="button" onclick="javascript:dlaction(\'canceldownload\')"><i class="fa fa-times"></i></button>
  <button class="btn btn-outline-secondary" type="button" onclick="javascript:dlaction(\'settargetdir\')"><i class="fa fa-folder"></i></button>
  <button class="btn btn-outline-secondary text-danger" type="button" onclick="location.href=\'index.php?site=Downloads&action=cleandownloadlist&dl_id=1\'"><i class="fa fa-trash"></i></button>
 
</div>
</div></div></div>
<div class="table-responsive">
<table class="table border mb-0 table-striped">
                      <thead class="fw-semibold text-nowrap">
                        <tr class="align-middle">
                          <th class="bg-body-secondary"></th>
                          <th class="bg-body-secondary">Dateiennamen</th>
                          <th class="bg-body-secondary">Status</th>
                          <th class="bg-body-secondary">Fortschritt</th>
                          <th class="bg-body-secondary text-center">PDL</th>
                          <th class="bg-body-secondary"></th>
                        </tr>
                      </thead>
                      <tbody>';

//Neu

$subdircounter=0;

//alle downloads zeigen
foreach(array_keys($Downloadlist->subdirs) as $subdir){
  $subdircounter++;
	$downloadids=$Downloadlist->ids($_GET['sort'],$subdir); //ids der downloads sortiert holen
	if(!empty($subdir)){
  
		//Unterverzeichnis
    echo'<tr class="table-info"><td></td><td colspan="5">
    ' . htmlspecialchars($subdir) . ' ( ' . count($downloadids) . ' )
</td>
  </tr>';
	foreach(array_keys($downloadids) as $a){
		//sieht doch etwas uebersichtlicher aus :)
		$current_download = $Downloadlist->download($a);
		
    //sieht doch etwas uebersichtlicher aus :)
		$current_download = $Downloadlist->download($a);
		
		$fortschritt=&$current_download['phpaj_DONE'];
		$balken = round($fortschritt, 2);
		$rest= $current_download["phpaj_REST"];
    $status = $template->status($current_download['phpaj_STATUS']);
    $title = substr($current_download['FILENAME'], 0, 40);
		$sources = ($current_download['phpaj_quellen_queue'] + $current_download['phpaj_quellen_dl']) . '/' . $current_download['phpaj_quellen_gesamt'];
    $size = subs::sizeformat($current_download['SIZE']);
    $parts = subs::parts($current_download['FILENAME']);
    $pdl = ((($current_download['POWERDOWNLOAD'])+10)/10);
    if($current_download['phpaj_dl_speed'] != "0")
    {
      $speed = subs::sizeformat($current_download['phpaj_dl_speed']) . ' - ';
    }
    else
    {
      $speed = "";
    }

    if($rest == "0")
    {
      $rest = "";
    }
    else
    {
      $rest = subs::sizeformat($rest);
    }
    if(!empty($current_download['phpaj_dl_speed']))
    {
        $restzeit = (int)($current_download['phpaj_REST']/$current_download['phpaj_dl_speed']);
	    $time = (int)($restzeit/3600);
		  
    }
    else
    {
        $restzeit = "";
        $time = "";
    }
    $b = $current_download['FILENAME'];
    $c = $current_download['POWERDOWNLOAD'];
    Downloadpage::list_group($status, $title, $balken, $sources, $size, $parts, $rest, $speed, $time, $restzeit, $a, $b, $c, $subdircounter, $pdl);
  }
echo'</div>';
}else{
	//Ohne Unterverzeichnis
  
	foreach(array_keys($downloadids) as $a)
    {
        //sieht doch etwas uebersichtlicher aus :)
		$current_download = $Downloadlist->download($a);
		
		$fortschritt=&$current_download['phpaj_DONE'];
		$balken = round($fortschritt, 2);
		$rest= $current_download["phpaj_REST"];
        $status = $template->status($current_download['phpaj_STATUS']);
        $title = substr($current_download['FILENAME'], 0, 40);
		$sources = ($current_download['phpaj_quellen_queue'] + $current_download['phpaj_quellen_dl']) . '/' . $current_download['phpaj_quellen_gesamt'];
        $size = subs::sizeformat($current_download['SIZE']);
        $parts = subs::parts($current_download['FILENAME']);
        $pdl = ((($current_download['POWERDOWNLOAD'])+10)/10);
        if($current_download['phpaj_dl_speed'] != "0")
        {
          $speed = subs::sizeformat($current_download['phpaj_dl_speed']) . ' - ';
        }
        else
        {
          $speed = "";
        }

        if($rest == "0")
        {
          $rest = "";
        }
        else
        {
          $rest = subs::sizeformat($rest);
        }
        if(!empty($current_download['phpaj_dl_speed']))
        {
            $restzeit = (int)($current_download['phpaj_REST'] / $current_download['phpaj_dl_speed']);

            // Berechnungen für Stunden (sicherstellen, dass der Wert als Integer behandelt wird)
           $time = intval($restzeit / 3600); // `intval()` gibt einen Integer-Wert zurück
		  
        }
        else
        {
            $restzeit = "";
            $time = "";
        }
        $b = $current_download['FILENAME'];
        $c = $current_download['POWERDOWNLOAD'];
        echo "<script type=\"text/javascript\">\n<!--\n"
				."dl_names[$a]='".addslashes($b)."';\n"
				."dl_pdl[$a]=".((($c)+10)/10).";\n"
				."dl_ids[$a]=0;\n"
				."dl_subdirs[$a]=$subdircounter;\n"
				."//-->\n</script>\n";
        Downloadpage::list_once($status, $title, $balken, $sources, $size, $parts, $rest, $speed, $time, $restzeit, $a, $b, $c, $subdircounter, $pdl);
    #echo'
# <div class="card-body" id="zeile_' . $a . '" onclick="change(' . $a . ');">
 ##       <a onclick="javascript:rename(' . $a . ')" title="' . $language->translate('Downloads.rename . '">
 #       <span><a onclick="location.href=\'index.php?site=dl_users&dl_id=' . $a . ' \'" title="Mehr Info"></div>';
					
  }

}
}
echo'</div>
                    </div>
                </div>';

echo "</form>";