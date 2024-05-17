<?php

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

$Sharelist = new Share();
$Uploadlist = new Uploads();
$icon_img = new Icons();
$subs = new subs();

$Uploadlist->refresh_cache();

$uploadusercount="0";
$uploaduserpercent="?";
if(!empty($Uploadlist->cache['IDS']['VALUES']['UPLOADID']))
	$uploadusercount=
		count($Uploadlist->cache['IDS']['VALUES']['UPLOADID']);
if(isset($Uploadlist->cache['phpaj_MAXUPLOADPOSITIONS'])){
	$uploaduserpercent= (int) ((($uploadusercount/
		$Uploadlist->cache['phpaj_MAXUPLOADPOSITIONS'])*100)+0.5);
}
if(empty($Uploadlist->cache['UPLOAD']) && empty($Uploadlist->cache['UPLOAD'])){
	echo'<div class="bg-body-tertiary d-flex flex-row align-items-center">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6">
            <div class="card mb-4 mx-4">
              <div class="card-body p-4">
                <div class="text-center">
                <h1><i class="icon icon-xxl mt-5 mb-2 cil-frown text-danger"></i></h1>	
				<p class="text-body-secondary">Keine Uploads zur Zeit!</p>
                </div>
                
            </div>
          </div>
        </div>
      </div>
    </div>';
   
}else{
	
echo'<div class="row clearfix">
                    <div class="col-sm-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="align-right">
                                	'. strtr($lang->Uploads->limit, array("%percent"=>$uploaduserpercent)).'
                                </div>';
//Tabellen�berschrift
echo'<div class="table-responsive">
<table class="table border mb-0">
                      <thead class="fw-semibold text-nowrap">
                        <tr class="align-middle">
                        <th class="bg-body-secondary"></th>
                          <th class="bg-body-secondary">'.$lang->Uploads->files.'</th>
                          <th class="bg-body-secondary">'.$lang->Uploads->username.'</th>
                          <th class="bg-body-secondary">'.$lang->Uploads->statuss.'</th>
                          <th class="bg-body-secondary">'.$lang->Uploads->progress.'</th>
                          <th class="bg-body-secondary text-center">' . $lang->Uploads->pdl . '</th>
                          <th class="bg-body-secondary">'.$lang->Uploads->speed.'</th>
                          <th class="bg-body-secondary"></th>
                        </tr>
                      </thead>
                      <tbody>

			  ';	
// Uebertrage
if($_GET['show_uplds']==1){
	echo "<tr>
			<td colspan=\"11\">
				<a onclick=\"location.href='".$_SERVER['PHP_SELF']."?site=uploads&show_uplds=-1&show_queue=".$_GET['show_queue']."'\">
					<i class='fa fa-minus'></i>&nbsp;&nbsp;<b>".$lang->Uploads->transferring."</b> (".$Uploadlist->cache['phpaj_ul'].")</a>
			</td>
		 </tr>";
	if(!empty($Uploadlist->cache['UPLOAD'])){
		foreach($Uploadlist->cache['phpaj_ids_ul'] as $a){
			$current_upload=$Uploadlist->get_upload($a);
			$current_shareid=&$current_upload['SHAREID'];
			$current_share=$Sharelist->get_file($current_shareid);
			
			//Groesse des parts + wieviel davon schon geladen ist
			$fortschritt=(
				(($current_upload['ACTUALUPLOADPOSITION'])
					-($current_upload['UPLOADFROM']))/
				(($current_upload['UPLOADTO'])
					-($current_upload['UPLOADFROM'])))*100;
			$fortschritt = number_format($fortschritt,2);
			$geladen=subs::sizeformat(
				($current_upload['ACTUALUPLOADPOSITION'])
				-($current_upload['UPLOADFROM']));
			$pdlwert="";
			if($current_upload['PRIORITY'] > $current_share['PRIORITY']){
				$pdlwert="("
					.((($current_upload['PRIORITY']-$current_share['PRIORITY'])-10)/10)
					.") ";
			}
		
		echo'<tr class="align-middle">
                          <td>
                        	' . $icon_img->directstate[$current_upload['DIRECTSTATE']] . '
                          </td>
                          <td>
                            <div class="text-nowrap">
                            	' . htmlspecialchars($current_share['SHORTFILENAME']) . '
                            </div>
                            <div class="small text-body-secondary text-nowrap">
                            <span><a onclick="location.href=\'index.php?site=dl_users&dl_id=' . $a . ' \'" title="Mehr Info">
					' . ($current_download['phpaj_quellen_queue'] + $current_download['phpaj_quellen_dl']) . '/' . $current_download['phpaj_quellen_gesamt']
					.'</a></span> | ' . subs::sizeformat($current_download['SIZE']) . '' .subs::parts($current_download['FILENAME']) . '</div>
                          </td>
                          <td class="text-center">
                            ' . htmlspecialchars(subs::cutstring($current_upload['NICK'],30)) . '
                          </td>
                          <td>
                          ' . $lang->Uploads->dl_status->$current_upload['STATUS'] . '
                          </td>
                          <td>
                            <div class="d-flex justify-content-between align-items-baseline">
                              <div class="fw-semibold">' . $fortschritt . '%</div>
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
                              <div class="progress-bar bg-success" role="progressbar" style="width: ' . $fortschritt . '%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          </td>
                          <td class="text-center">
                          
                            ' . $pdlwert . $current_upload['PRIORITY'] . '
                          </td>
                          <td>
                            ' . subs::sizeformat($current_upload['SPEED']) . '
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
		
			echo "<td class=\"right\" nowrap>".subs::sizeformat(
				($current_upload['UPLOADTO'])
				-($current_upload['UPLOADFROM']))."</td>";
			echo "<td width=\"100\" nowrap>";
			echo $subs->prozess_bar($fortschritt);
			echo "<td class=\"right\" nowrap>";
			if(isset($current_upload['LOADED']) && $current_upload['LOADED'] != -1){
				echo number_format($current_upload['LOADED']*100,2)."%";
			}else{
				echo "N/A";
			}
			echo "</td>\n";
			
		}
	}
}else{
	echo "<tr><td colspan=\"11\"><a onclick=\"location.href='".$_SERVER['PHP_SELF']."?site=uploads&show_uplds=1&amp;"
		."show_queue=".$_GET['show_queue']."&amp;"
		.SID."'\"><i class='fa fa-plus'></i>&nbsp;&nbsp;<b>"
		.$lang->Uploads->transferring."</b> ("
		.$Uploadlist->cache['phpaj_ul'].")</a></td></tr>";
}

//Warteschlange
if($_GET['show_queue']==1){
	echo "<tr><td colspan=\"11\"><a onclick=\"location.href='".$_SERVER['PHP_SELF']."?site=uploads&show_uplds="
		.$_GET['show_uplds']."&amp;show_queue=-1"."&amp;"
		.SID."'\"><i class='fa fa-minus'></i>&nbsp;&nbsp;<b>"
		.$lang->Uploads->queue."</b> ("
		.$Uploadlist->cache['phpaj_queue'].")</a></td></tr>";
	if(!empty($Uploadlist->cache['UPLOAD'])){
		foreach($Uploadlist->cache['phpaj_ids_queue'] as $a){
			$current_upload=$Uploadlist->get_upload($a);
			echo "<tr><td width=\"10\"></td>";
			$current_shareid=$current_upload['SHAREID'];
			$current_share=$Sharelist->get_file($current_shareid);
			echo "<td>";
            echo '<a href="'.$current_share['LINK'].'">' . htmlspecialchars($current_share['SHORTFILENAME']).'</a></td>';

            //relInfo
            if (!empty($_ENV['REL_INFO'])) {
                echo '<td align="center"><a target="_blank" href="' . sprintf($_ENV['REL_INFO'], $current_share['LINK']) . '"><i class="fa fa-info-circle"></i></a></td>';
            }

			echo "<td title=\"".htmlspecialchars($current_upload['NICK'])."\">"
				.htmlspecialchars(subs::cutstring($current_upload['NICK'],30))."</td>\n";
			if(isset($current_upload['LASTCONNECTION'])){
				$ul_timediff=($Uploadlist->cache['TIME']['VALUES']['CDATA']
					-$current_upload['LASTCONNECTION'])
					/1000;
				echo "<td class=\"right\">"
					.sprintf("%dmin %02ds",$ul_timediff/60,$ul_timediff%60)
					."</td>\n";
			}else{
				echo "<td class=\"right\">&nbsp;</td>\n";
			}
			echo "<td></td>\n";
			echo "<td></td>\n";
			echo "<td></td>\n";
			echo "<td class=\"right\">";
			if(isset($current_upload['LOADED']) && $current_upload['LOADED'] != -1){
				echo number_format($current_upload['LOADED']*100,2)."%";
			}else{
				echo "N/A";
			}
			echo "</td>\n";
			$pdlwert="";
			if($current_upload['PRIORITY'] > $current_share['PRIORITY']){
				$pdlwert="("
					.((($current_upload['PRIORITY'] - $current_share['PRIORITY'])-10)/10)
					.") ";
			}
			//Upload prio
			echo "<td class=\"right\">"
				.$pdlwert.$current_upload['PRIORITY']
				."</td>\n";
			echo "<td nowrap>".$icon_img->os[$current_upload['OPERATINGSYSTEM']]." ".$current_upload['OPERATINGSYSTEM'].$current_upload['VERSION']."</td>";
			echo "</tr>\n";
		}
	}
}else{
	echo "<tr><td colspan=\"11\"><a onclick=\"location.href='".$_SERVER['PHP_SELF']."?site=uploads&show_uplds="
		.$_GET['show_uplds']."&amp;show_queue=1"."&amp;".SID."'\">"
		."<i class='fa fa-plus'></i>&nbsp;&nbsp;<b>"
		.$lang->Uploads->queue."</b> ("
		.$Uploadlist->cache['phpaj_queue'].")</a></td></tr>";
}

echo "</tbody></table></div></div></div></div></div>";
}