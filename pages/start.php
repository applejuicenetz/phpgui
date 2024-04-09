<?php
error_reporting(E_ALL);

require_once "_classes/subs.php";
require_once "_classes/core.php";
require_once "_classes/share.php";
require_once "_classes/server.php";
require_once "_classes/uploads.php";
require_once "_classes/downloads.php";
require_once "_classes/icons.php";

$lang = $_SESSION['language']['START'];

$Servers = new Server();
$core = new Core;
$icon_img = new Icons();

//Info holen
$statusbar_xml=$core->command("xml","modified.xml?filter=informations");
$temp=array_keys($statusbar_xml['INFORMATION']);
$information=&$statusbar_xml['INFORMATION'][$temp[0]];
$temp2=array_keys($statusbar_xml['NETWORKINFO']);
$netinfo=&$statusbar_xml['NETWORKINFO'][$temp2[0]];

//Serververbindung?
$serverconnection=$_SESSION['language']['SERVER']['STATUS_NOT_CONNECTED'];
if($netinfo['CONNECTEDWITHSERVERID']>=0){
	$serverconnection=$_SESSION['language']['SERVER']['STATUS_CONNECTED'];
}else{
	if($netinfo['TRYCONNECTTOSERVER']>=0)
		$serverconnection=$_SESSION['language']['SERVER']['STATUS_CONNECTING'];
}

$_SESSION['phpaj']['core_source_ip']=$netinfo['IP'];
if(empty($_SESSION['phpaj']['core_source_port'])){
	$settings=$core->command("xml","settings.xml");
	$_SESSION['phpaj']['core_source_port']=$settings['PORT']['VALUES']['CDATA'];
}

if(isset($information['MAXUPLOADPOSITIONS'])){
	$_SESSION['cache']['UPLOADS']['phpaj_MAXUPLOADPOSITIONS']=
		$information['MAXUPLOADPOSITIONS'];
}

//Warnungen
	$warnungen=array();
	if($Servers->netstats['firewalled']==='true'){
		$warnungen[] = $_SESSION['language']['SERVER']['FIREWALLED'];
	}

	if(!empty($warnungen)){
		echo "<h2>".$lang['WARNINGS']."</h2>";
		echo "<div style=\"margin-left:0.5cm;background-color:#FF0000;\">";
		foreach($warnungen as $a) {
		    echo "<img src=\"../style/" .$_SESSION['server_warning_icon']."\" alt=\"[!]\" />".$a."<br />";
        }
		echo "</div>";
	}
?>

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">

            <!-- Sales Card -->
            <div class="col-xxl-6 col-md-12">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">aktueller Server</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-server"></i>
                    </div>
                    <div class="ps-3 fw-bold">
                      <?php echo $Servers->netstats['servername']; ?><br>
                      <span class="small pt-1"><?php
                      //server welcome msg
if(!empty($Servers->netstats['welcome'])){
		echo $Servers->netstats['welcome'];
		
} 
?></span>

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->
<!-- Sales Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">aktive Downloads</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-download"></i>
                    </div>
                    <div class="ps-3">
                     <?php
                     $Downloadlist = new Downloads();
                     $counddown = $downloadids=$Downloadlist->ids("name",$subdir);
                     
$Downloadlist->refresh_cache();
$subdircounter=0;
//alle downloads zeigen
foreach(array_keys($Downloadlist->subdirs) as $subdir){
	$subdircounter++;
	$downloadids=$Downloadlist->ids("",$subdir); //ids der downloads sortiert holen
			}
	echo"<h6>".count($downloadids)." Downloads</h6>"; 
?></span>

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->
<!-- Sales Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">aktive Uploads</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-upload"></i>
                    </div>
                    <div class="ps-3">
                      <?php
                     $Uploadlist = new Uploads();
	echo"<h6>".$Uploadlist->cache['phpaj_ul']." Uploads</h6>"; 
	
?></span>

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->
<!-- Sales Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Share</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <div class="ps-3">
                      <?php
                     $Sharelist = new Share();
	if($_ENV['GUI_SHOW_SHARE']) {
	    $Sharelist->refresh_cache(30);
    }
	if(!empty($_SESSION['phpaj']['share_LASTTIMESTAMP'])){
		$share_anzahl=0;
		$share_groesse=0;
		foreach(array_keys($Sharelist->cache['SHARES']
				['VALUES']['SHARE']) as $a){
			$share_anzahl++;
			$share_groesse+=$Sharelist->cache['SHARES']
				['VALUES']['SHARE'][$a]['SIZE'];
		}
		echo "<h6>".number_format($share_anzahl)." Dateien</h6><span class='small pt-1 fw-bold'> ".
				sizeformat($share_groesse);
		echo "";
	}else{
		echo $lang['SHAREINFO_MISSING']
			." (<a href=\"".$_SERVER['PHP_SELF']."?".SID."&amp;reloadshare=1\">"
			.$_SESSION['language']['GENERAL']['REFRESH']."</a>)<br />";
	}
	
?></span>

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->
<!-- Sales Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Credits</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-coin"></i>
                    </div>
                    <div class="ps-3">
                      <?php
If($information['CREDITS'] <= 0){
$creditcolor = " class='text-danger'";
}else{
$creditcolor =" class='ext-success'"; 
}
	echo"<h6".$creditcolor." >".sizeformat($information['CREDITS'])." Credits</h6>"; 
	?></span>

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->
<?php
$coreinfo = $Servers->core->getcoreversion();
$coresubversions=explode(".",$_SESSION['cache']['STATUSBAR']['VERSION']);
$info = $Servers->info();
?>
                  </div>
                </div>
<div class="col-lg-4">
          <div class="row">
 <div class="card">
            <div class="card-body">
              <h5 class="card-title">Core & Netzwerk Info</h5>

              <!-- Default Table -->
              <table class="table">
                <thead>
                  <tr>
                  </tr>
                </thead>
                <tbody>
<tr>
<td>Server Zeit</td>
<td>
<?php
echo"".$Servers->time()."   ";
echo "<a href=\"javascript: window.location.href='".$_SERVER['PHP_SELF']."?"
	.SID."'\"><i class=\"bi bi-arrow-clockwise\"></i></a>";
?></td>
                  <tr>
                    <td>phpGUI Version</td>
                    <td><?php echo"".PHP_GUI_VERSION.""; ?></td>
                  </tr>
<tr>
                    <td>Core Version</td>
                    <td><?php echo $coreinfo['VERSION']; ?></td>
                  </tr>
<tr>
                    <td>Betriebssystem</td>
                    <td><?php echo $icon_img->os["2"]." ".$coreinfo['SYSTEM']; ?></td>
                  </tr>
<tr>
                    <td>verbunden seit</td>
                    <td><?php 
$srv_timediff=$Servers->netstats['timeconnected'];
	$srv_timediff=sprintf("%dh %dmin %ds",
		$srv_timediff/3600,($srv_timediff%3600)/60,$srv_timediff%60);
echo"".$srv_timediff."";
	 ?></td>
                  </tr>
<tr>
                    <td>offene Verbindungen</td>
                    <td><?php echo"".$info['OPENCONNECTIONS'].""; ?></td>
                  </tr>
<tr>
                    <td>Anzahl User sharen</td>
                    <td><?php echo"".$Servers->netstats['users'].""; ?></td>
                  </tr>
<tr>
                    <td>gesamte Dateien</td>
                    <td><?php echo"".number_format($Servers->netstats['filecount'])." - ".sizeformat($Servers->netstats['filesize']).""; ?></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Default Table Example -->
            </div>
          </div>

<div class="card">
            <div class="card-body">
              <h5 class="card-title">Connection Infos</h5>

              <!-- Default Table -->
              <table class="table">
                <thead>
                  <tr>
                  </tr>
                </thead>
                <tbody>
<tr>
<tr>
                    <td>Bytes in</td>
                    <td><?php echo"".sizeformat($information['SESSIONDOWNLOAD']).""; ?></td>
                  </tr>
<tr>
                    <td>Bytes out</td>
                    <td><?php echo"".sizeformat($information['SESSIONUPLOAD']).""; ?></td>
                  </tr>
<tr>
                   <td>Downloadspeed</td>
                    <td><?php echo"".sizeformat($information['DOWNLOADSPEED']).""; ?></td>
                  </tr>
<tr>
                   <td>Uploadspeed</td>
                    <td><?php echo"".sizeformat($information['UPLOADSPEED']).""; ?></td>
                  </tr>
                </tbody>
              </table>
              <!-- End Default Table Example -->
            </div>
          </div>
</div>
                </div>

