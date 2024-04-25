<?php
require_once "_classes/subs.php";
require_once "_classes/downloads.php";
require_once "_classes/subs.php";
require_once "_classes/icons.php";

$Downloadlist = new Downloads;
$Downloadlist->refresh_cache();
$subs = new subs();
$icon_img = new Icons();
$anguage = new language($_ENV['GUI_LANGUAGE']);
$lang = $language->translate();

//default anzeige
	if(empty($_GET['show_dls'])){$_GET['show_dls']=1;}
	if(empty($_GET['show_queue'])){$_GET['show_queue']=-1;}
	if(empty($_GET['show_rest'])){$_GET['show_rest']=-1;}

echo'<div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="table-responsive">
			  <table class="table table-striped">
				<thead>
					<tr>
						<th width="10">&nbsp;</th>
						<th>'.$lang->Downloads->source.'</th>
						<th>'.$lang->Downloads->filename.'</th>
						<th>'.$lang->Downloads->statuss.'</th>
						<th>'.$lang->Downloads->speed.'</th>
						<th>'.$lang->Downloads->pdl.'</th>
						<th>'.$lang->Downloads->size.'</th>
						<th width="100">'.$lang->Downloads->finished.'</th>
						<th>'.$lang->Downloads->clientversion.'</th>
					</tr>
				<thead>
				<tbody>';

//download zeigen
if(!empty($Downloadlist->cache['DOWNLOAD'])){
	$a = $Downloadlist->download($_GET['dl_id']);
	$astatus = $a['phpaj_STATUS'];
	echo'<tr>
						<td width="10">&nbsp;</th>
						<td>'.($a['phpa_quellen_quee']+$a['phpaj_quellen_dl']).'/'.$a['phpaj_quellen_gesamt'].'('.$a['phpaj_quellen_dl'].')</td>
						<td>'.htmlspecialchars($a['FILENAME']).'</td>
						<td>'.$a['phpaj_STATUS'].'</td>
						<td>'.sizeformat($a['phpaj_dl_speed']).'</td>
						<td>'.((($a['POWERDOWNLOAD'])+10)/10).'</td>
						<td>'.sizeformat($a['SIZE']).'</td>
						<td>'.$subs->prozess($a['phpaj_DONE']).'</td>
						<td></td>
					</tr>';

//Einzelne Quellen
	//Uebertrage
	if($_GET['show_dl'] == "1"){
		echo'<tr>
				<td colspan="9">
					<a href="?site=dl_users&dl_id='.$a['ID'].'&show_dl=0&show_queue='.$_GET['show_queue'].'&show_rest='.$_GET['show_rest'].'">
						<i class="fa fa-minus"></i> <b>'.$lang->Downloads->transferring.'</b> ('.$a['phpaj_quellen_dl'].')
					</a>
				</td>
			</tr>';
		if(!empty($Downloadlist->cache['USER'])){
			foreach($a['phpaj_ids_quellen_dl'] as $b){
				$current_user = $Downloadlist->user($b);		
				$fortschritt = (($current_user['ACTUALDOWNLOADPOSITION'] - $current_user['DOWNLOADFROM']) / ($current_user['DOWNLOADTO'] - $current_user['DOWNLOADFROM'])) *100;
		
				$current_user = $Downloadlist->user($b);
		
				echo'<tr><td>'.$icon_img->directstate[$current_user['DIRECTSTATE']].'</td>
						<td>
							<a href="index.php?site=dl_parts&usr_id='.$b.'">
								'.htmlspecialchars($current_user['NICKNAME']).'
							</a>
						</td>
						<td>
							'.htmlspecialchars($current_user['FILENAME']).'
						</td>
						<td>
							'.$lang->Downoads->dl_status->status_7.'
						</td>
						<td>
							'.sizeformat($current_user['SPEED']).'/s
						</td>
						<td>
							'.(($current_user['POWERDOWNLOAD'] +10)/10).'
						</td>
						<td>
							'.sizeformat($current_user['DOWNLOADTO'] - $current_user['DOWNLOADFROM']).'
						</td>
						<td>
							'.$subs->prozess($fortschritt).'
						</td>
						<td>
							'.$icon_img->os[$current_user['OPERATINGSYSTEM']].$current_user['VERSION'].'
						</td>
					</tr>';
			}
		}
	}else{
	echo'<tr>
				<td colspan="9">
					<a href="?site=dl_users&dl_id='.$a['ID'].'&show_dl=1&show_queue='.$_GET['show_queue'].'&show_rest='.$_GET['show_rest'].'">
						<i class="fa fa-plus"></i> <b>'.$lang->Downloads->transferring.'</b> ('.$a['phpaj_quellen_dl'].')
					</a>
				</td>
			</tr>';
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
				<td colspan="3"></td>
				<td colspan="6">
					'.$lang->Downloads->user_source.'
				</td>
			</tr>';
		
		if(!empty($Downloadlist->cache['USER'])){
			foreach($a['phpaj_ids_quellen_queue'] as $b){
				$current_user = $Downloadlist->user($b);
				
				echo'<tr>
						<td>'.$icon_img->directstate[$current_user['DIRECTSTATE']].'</td>
						<td>
							<a href="index.php?site=dl_parts&usr_id='.$b.'">
								'.htmlspecialchars($current_user['NICKNAME']).'
							</a>
						</td>
						<td>'.htmlspecialchars(cutstring($current_user['FILENAME'],30)).'</td>
						<td>'.$current_user['STATUS'].'</td>
						<td>'.$current_user['SOURCE'].'</td>
						<td>'.$subs->dl_status($current_user['QUEUEPOSITION']).'</td>
						<td>'.(($current_user['POWERDOWNLOAD'] +10)/10).'</td>
						<td></td>
						
						<td>'.$icon_img->os[$current_user['OPERATINGSYSTEM']].$current_user['VERSION'].'</td>
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
				<td colspan="3"></td>
				<td colspan="6">
					'.$lang->Downloads->user_source.'
				</td>
			</tr>';
		
		if(!empty($Downloadlist->cache['USER'])){
			foreach($a['phpaj_ids_quellen_rest'] as $b){
				$current_user = $Downloadlist->user($b);
				
				echo'<tr>
						<td>'.$icon_img->directstate[$current_user['DIRECTSTATE']].'</td>
						<td>
							<a href="index.php?site=dl_parts&usr_id='.$b.'">
								'.cutstring($current_user['NICKNAME'], 25).'
							</a>
						</td>
						<td>'.htmlspecialchars(cutstring($current_user['FILENAME'],30)).'</td>
						<td>'.$subs->dl_status($current_user['STATUS']).'</td>
						<td>'.$subs->dl_source($current_user['SOURCE']).'</td>
						<td>'.(($current_user['POWERDOWNLOAD'] +10)/10).'</td>
						<td></td>
						<td></td>
						<td>'.$icon_img->os[$current_user['OPERATINGSYSTEM']].$current_user['VERSION'].'</td>
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
echo "</tbody></table>";
