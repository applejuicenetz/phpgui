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
	if(empty($_GET['show_queue'])) $_GET['show_queue']=-1;

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
//
echo'<div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="align-right">
                                	'. strtr($lang->Uploads->limit, array("%percent"=>$uploaduserpercent)).'
                                </div>';
//Tabellen�berschrift
echo'<div class="table-responsive">
			  <table class="table table-striped">
				<thead>
                  <tr>
                    <th scope="col">'.$lang->Uploads->files.'</th>
					<th scope="col">'.$lang->Uploads->username.'</th>
					<th><i class="text-warning material-icons">info</i></th>
					<th scope="col">'.$lang->Uploads->statuss.'</th>
                    <th scope="col">'.$lang->Uploads->speed.'</th>
                    <th scope="col">'.$lang->Uploads->size.'</th>
                    <th scope="col">'.$lang->Uploads->finish.'</th>
                    <th scope="col">'.$lang->Uploads->finished.'</th>
                    <th scope="col">'.$lang->Uploads->pdl.'</th>
                    <th scope="col">'.$lang->Uploads->clientversion.'</th>
                  </tr>
                </thead>
                <tbody>';	
// Uebertrage
if($_GET['show_uplds']==1){
	echo "<tr>
			<td colspan=\"10\">
				<a href=\"".$_SERVER['PHP_SELF']."?site=uploads&show_uplds=-1&show_queue=".$_GET['show_queue']."&amp;".SID."\">
					<i class='fa fa-minus'></i>&nbsp;&nbsp;<b>".$lang->Uploads->transferring."</b> (".$Uploadlist->cache['phpaj_ul'].")</a>
			</td>
		 </tr>";
	if(!empty($Uploadlist->cache['UPLOAD'])){
		foreach($Uploadlist->cache['phpaj_ids_ul'] as $a){
			$current_upload=$Uploadlist->get_upload($a);
			echo "<tr>";
			$current_shareid=&$current_upload['SHAREID'];
			$current_share=$Sharelist->get_file($current_shareid);
			echo "<td>".$icon_img->directstate[$current_upload['DIRECTSTATE']];
			
			//dateiname
			echo '<a href="'.$current_share['LINK'].'">' . htmlspecialchars($current_share['SHORTFILENAME']).'</a></td>';

            //Nick des Users
			echo "<td title=\"".htmlspecialchars($current_upload['NICK'])."\">"
				.htmlspecialchars(subs::cutstring($current_upload['NICK'],30))."</td>\n";
			
			//RelInfo
			if (!empty($_ENV['REL_INFO'])) {
                echo '<td align="center"><a target="_blank" href="' . sprintf($_ENV['REL_INFO'], $current_share['LINK']) . '"><i class="fa fa-info"></i></a></td>';
            }
			
			//Status
			echo "<td>".$lang->Uploads->dl_status->$current_upload['STATUS']."</td>\n";
			
			//Geschwindigkeit
			echo "<td class=\"right\">"
				.subs::sizeformat($current_upload['SPEED'])
				."/s</td>\n";
			
			//Groesse des parts + wieviel davon schon geladen ist
			$fortschritt=(
				(($current_upload['ACTUALUPLOADPOSITION'])
					-($current_upload['UPLOADFROM']))/
				(($current_upload['UPLOADTO'])
					-($current_upload['UPLOADFROM'])))*100;
			$geladen=subs::sizeformat(
				($current_upload['ACTUALUPLOADPOSITION'])
				-($current_upload['UPLOADFROM']));
			echo "<td class=\"right\">".subs::sizeformat(
				($current_upload['UPLOADTO'])
				-($current_upload['UPLOADFROM']))."</td>";
			echo "<td width=\"100\">";
			echo $subs->prozess_bar($fortschritt);
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
					.((($current_upload['PRIORITY']-$current_share['PRIORITY'])-10)/10)
					.") ";
			}
			//Upload prio
			echo "<td class=\"right\">".$pdlwert.$current_upload['PRIORITY']."</td>\n";
			//Betriebssystem + aj-version
			echo "<td>".$icon_img->os[$current_upload['OPERATINGSYSTEM']]." ".$current_upload['VERSION']."</td>";
			echo "</tr>\n";
		}
	}
}else{
	echo "<tr><td colspan=\"10\"><a href=\"".$_SERVER['PHP_SELF']."?site=uploads&show_uplds=1&amp;"
		."show_queue=".$_GET['show_queue']."&amp;"
		.SID."\"><i class='fa fa-plus'></i>&nbsp;&nbsp;<b>"
		.$lang->Uploads->transferring."</b> ("
		.$Uploadlist->cache['phpaj_ul'].")</a></td></tr>";
}

//Warteschlange
if($_GET['show_queue']==1){
	echo "<tr><td colspan=\"10\"><a href=\"".$_SERVER['PHP_SELF']."?site=uploads&show_uplds="
		.$_GET['show_uplds']."&amp;show_queue=-1"."&amp;"
		.SID."\"><i class='fa fa-minus'></i>&nbsp;&nbsp;<b>"
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
			echo "<td>".$icon_img->os[$current_upload['OPERATINGSYSTEM']]." ".$current_upload['OPERATINGSYSTEM'].$current_upload['VERSION']."</td>";
			echo "</tr>\n";
		}
	}
}else{
	echo "<tr><td colspan=\"10\"><a href=\"".$_SERVER['PHP_SELF']."?site=uploads&show_uplds="
		.$_GET['show_uplds']."&amp;show_queue=1"."&amp;".SID."\">"
		."<i class='fa fa-plus'></i>&nbsp;&nbsp;<b>"
		.$lang->Uploads->queue."</b> ("
		.$Uploadlist->cache['phpaj_queue'].")</a></td></tr>";
}

echo "</tbody></table></div></div></div></div></div>";
