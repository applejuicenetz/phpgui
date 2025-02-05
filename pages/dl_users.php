<?php

use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\Kernel;

$Downloadlist = new Downloads;
$Downloadlist->refresh_cache();

$subs = new subs();
$icon_img = new Icons();

$language = Kernel::getLanguage();
$lang = $language->translate();

echo'<div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="table-responsive">
					<table class="table border mb-0">
                      <thead class="fw-semibold text-nowrap">
                        <tr class="align-middle">
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


//download zeigen
if(!empty($Downloadlist->cache['DOWNLOAD'])){
	$a = $Downloadlist->download($_GET['dl_id']);
	$astatus = $a['phpaj_STATUS'];
	$balken = round($a['phpaj_DONE'], 2);
    echo'           <tr>
    
                          <td>
                            <div class="text-nowrap">
                            		' . substr($a['FILENAME'], 0, 40) . '...
                            </div>
                            <div class="small text-body-secondary text-nowrap">
                            <span>
					' . ($a['phpaj_quellen_queue'] + $a['phpaj_quellen_dl']) . '/' . $a['phpaj_quellen_gesamt']
					.'</span> | ' . subs::sizeformat($a['SIZE']) . '' .subs::parts($a['FILENAME']) . '</div>
                          </td>
                          <td class="text-center">
                            ' . $Downloadlist->status($a['phpaj_STATUS']) . '
                          </td>
                          <td>
                            <div class="d-flex justify-content-between align-items-baseline">
                              <div class="fw-semibold">' . $balken . '%</div>
                              <div class="text-nowrap small text-body-secondary ms-3">' . $rest . '- ';
                              if(!empty($a['phpaj_dl_speed'])){
								$restzeit=$a['phpaj_REST']/$a['phpaj_dl_speed'];
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
                          
                            ' . ((($a['POWERDOWNLOAD'])+10)/10) . '
                          </td>
                          <td>
                            ' . subs::sizeformat($a['phpaj_dl_speed']) . '
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
                        </tr>
                        ';
                        
                        
                        
                        
                        
                        
                        
                        
if(!empty($Downloadlist->cache['USER'])){
		foreach($a['phpaj_ids_quellen_dl'] as $b){
				$current_user = $Downloadlist->user($b);		
				$fortschritt = (($current_user['ACTUALDOWNLOADPOSITION'] - $current_user['DOWNLOADFROM']) / ($current_user['DOWNLOADTO'] - $current_user['DOWNLOADFROM'])) *100;
				$fortschritt = round($fortschritt, 2);
				$current_user = $Downloadlist->user($b);
		
				echo'           <tr>
				
                          <td>
                            <div class="text-nowrap">
                            	<i class="icon icon-l cil-arrow-thick-from-left"></i>	'.$icon_img->directstate[$current_user['DIRECTSTATE']].' ' . substr($current_user['FILENAME'], 0, 40) . '...
                            </div>
                            <div class="small text-body-secondary text-nowrap ms-5">
                            <span>User: <a href="index.php?site=dl_parts&usr_id='.$b.'">
								'.htmlspecialchars($current_user['NICKNAME']).'
							</a></span> | '.subs::sizeformat($current_user['DOWNLOADTO'] - $current_user['DOWNLOADFROM']).'</div>
                          </td>
                          <td class="text-center">
                            '.$lang->Downloads->dl_users->status_7.'
                          </td>
                          <td>
                            <div class="d-flex justify-content-between align-items-baseline">
                              <div class="fw-semibold">' . $fortschritt . '%</div>
                              <div class="text-nowrap small text-body-secondary ms-3">';
                              
			echo'</div>
                            </div>
                            <div class="progress progress-thin">
                              <div class="progress-bar bg-success" role="progressbar" style="width: ' . $fortschritt . '%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          </td>
                          <td class="text-center">
                          
                            ' . ((($current_user['POWERDOWNLOAD'])+10)/10) . '
                          </td>
                          <td>
                            '.subs::sizeformat($current_user['SPEED']).'/s
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
	//Warteschlange
	if($_GET['show_queue']==1){
		echo'<tr>
				<td colspan="9">
					<a href="?site=dl_users&dl_id='.$a['ID'].'&show_dl=1&show_rest='.$_GET['show_rest'].'">
						<i class="fa fa-minus"></i> <b>'.$lang->Downloads->queue.'</b> ('.$a['phpaj_quellen_queue'].')
					</a>
				</td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td colspan="3">
					'.$lang->Downloads->user_source.'
				</td>
			</tr>';
		
		if(!empty($Downloadlist->cache['USER'])){
			foreach($a['phpaj_ids_quellen_queue'] as $b){
				$current_user = $Downloadlist->user($b);
				
				echo'           <tr>
				
                          <td>
                            <div class="text-nowrap">
                            	<i class="icon icon-l cil-arrow-thick-from-left"></i>	'.$icon_img->directstate[$current_user['DIRECTSTATE']].' ' . substr($current_user['FILENAME'], 0, 40) . '...
                            </div>
                            <div class="small text-body-secondary text-nowrap ms-5">
                            <span>User: <a href="index.php?site=dl_parts&usr_id='.$b.'">
								'.htmlspecialchars($current_user['NICKNAME']).'
							</a></span> | '.subs::sizeformat($current_user['DOWNLOADTO'] - $current_user['DOWNLOADFROM']).'</div>
                          </td>
                          <td class="text-center">
                            '.$subs->dl_status($current_user['STATUS']).'
                          </td>
                          <td>
                            '.$subs->dl_source($current_user['SOURCE']).'</td>
                          <td class="text-center">
                          
                            ' . ((($current_user['POWERDOWNLOAD'])+10)/10) . '
                          </td>
                          <td>
                            '.subs::sizeformat($current_user['SPEED']).'/s
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
	}else{
		echo'<tr>
				<td colspan="9">
					<a href="?site=dl_users&dl_id='.$a['ID'].'&show_dl=1&show_queue=1&show_rest=0">
						<i class="fa fa-plus"></i> <b>'.$lang->Downloads->queue.'</b> ('.$a['phpaj_quellen_queue'].')
					</a>
				</td>
			</tr>';
		
	}

	//Rest
	if($_GET['show_rest']==1){
		
				echo'<tr>
				<td colspan="9">
					<a href="?site=dl_users&dl_id='.$a['ID'].'&show_dl=1&show_queue='.$_GET['show_queue'].'">
						<i class="fa fa-minus"></i> <b>'.$lang->Downloads->rest.'</b> ('.($a['phpaj_quellen_gesamt']-$a['phpaj_quellen_queue']
			-$a['phpaj_quellen_dl']).')
					</a>
				</td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td colspan="3">
					'.$lang->Downloads->user_source.'
				</td>
			</tr>';
		
		if(!empty($Downloadlist->cache['USER'])){
			foreach($a['phpaj_ids_quellen_rest'] as $b){
				$current_user = $Downloadlist->user($b);
				echo'           <tr>
				
                          <td>
                            <div class="text-nowrap">
                            	<i class="icon icon-l cil-arrow-thick-from-left"></i>	'.$icon_img->directstate[$current_user['DIRECTSTATE']].' ' . substr($current_user['FILENAME'], 0, 40) . '...
                            </div>
                            <div class="small text-body-secondary text-nowrap ms-5">
                            <span>User: <a href="index.php?site=dl_parts&usr_id='.$b.'">
								'.htmlspecialchars($current_user['NICKNAME']).'
							</a></span> | '.subs::sizeformat($current_user['DOWNLOADTO'] - $current_user['DOWNLOADFROM']).'</div>
                          </td>
                          <td class="text-center">
                            '.$subs->dl_status($current_user['STATUS']).'
                          </td>
                          <td>
                            '.$subs->dl_source($current_user['SOURCE']).'</td>
                          <td class="text-center">
                          
                            ' . ((($current_user['POWERDOWNLOAD'])+10)/10) . '
                          </td>
                          <td>
                            '.subs::sizeformat($current_user['SPEED']).'/s
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
		}else{
						echo'<tr>
				<td colspan="9">
					<a href="?site=dl_users&dl_id='.$a['ID'].'&show_dl=1&show_queue='.$_GET['show_queue'].'&show_rest=1">
						<i class="fa fa-plus"></i> <b>'.$lang->Downloads->rest.'</b> ('.($a['phpaj_quellen_gesamt']-$a['phpaj_quellen_queue']
			-$a['phpaj_quellen_dl']).')
					</a>
				</td>
			</tr>';
	}
                        
}
echo "</tbody></table></div></div></div>";
