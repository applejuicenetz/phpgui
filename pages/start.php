<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$language = Kernel::getLanguage();
$lang = $language->translate();

//Classes abrufen
$Servers = new Server();
$core = new Core();
$icon_img = new Icons();
$subs = new subs();
$template = new template();
$Uploadlist = new Uploads();
$Sharelist = new Share();

//Cache laden
$subs::refresh_cache();
//Info holen
$modified = $core->command("xml", "modified.xml?filter=informations");
$temp = array_keys($modified['INFORMATION']);
$information =& $modified['INFORMATION'][$temp[0]];
$temp2 = array_keys($modified['NETWORKINFO']);
$netinfo =& $modified['NETWORKINFO'][$temp2[0]];


$_SESSION['phpaj']['core_source_ip'] = $netinfo['IP'];
if (empty($_SESSION['phpaj']['core_source_port'])) {
    $settings = $core->command("xml", "settings.xml");
    $_SESSION['phpaj']['core_source_port'] = $settings['PORT']['VALUES']['CDATA'];
}

if (isset($information['MAXUPLOADPOSITIONS'])) {
    $_SESSION['cache']['UPLOADS']['phpaj_MAXUPLOADPOSITIONS'] =
        $information['MAXUPLOADPOSITIONS'];
}

//Warnungen
if ($Servers->netstats['firewalled'] === 'true') {
    $template->alert("danger", $lang->System->warnung, $lang->System->firewall);
}
?>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
	<div class="card col-xs-12 col-sm-12 col-md-12 col-lg-12 g-4 mb-4">
		<div class="card-header"><svg width="16" height="16">
              <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-storage"></use>
            </svg> <?php echo $lang->Start->current_server; ?></div>
			<div class="card-body">
    			<h4></h4>
    			<h3><?php echo $Servers->netstats['servername']; ?></h3>
                    <?php
                    //server welcome msg
                    if (!empty($Servers->netstats['welcome'])) {
                        echo $Servers->netstats['welcome'];
                    }
                    ?>
			</div>
		</div>
		<!-- Statistik -->
		<div class="row g-4">
        	<div class="col-12 col-sm-6 col-xl-6">
            <div class="card overflow-hidden" onclick="location.href='index.php?site=downloads'">
            	<div class="card-body p-0 d-flex align-items-center">
                	<div class="bg-warning text-white p-4 me-2">
                    	<svg class="icon icon-xxl">
                        	<use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-cloud-download"></use>
                        </svg>
                    </div>
                	<div>
                		<div class="fs-4 fw-semibold"><?php template::dashboard("download"); ?></div>
                		<div class="text-body-secondary text-uppercase small"><?php echo $lang->Start->active_downloads; ?></div>
                		
                		
                	</div>
                	
                </div>
        	</div>
        </div>
        <!-- end-->
        	<div class="col-12 col-sm-6 col-xl-6">
            <div class="card overflow-hidden" onclick="location.href='index.php?site=uploads'">
            	<div class="card-body p-0 d-flex align-items-center">
                	<div class="bg-primary text-white p-4 me-2">
                    	<svg class="icon icon-xxl">
                        	<use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-cloud-upload"></use>
                        </svg>
                    </div>
                <div>
                <div class="fs-4 fw-semibold"><?php echo $Uploadlist->cache['phpaj_ul']; ?></div>
                <div class="text-body-secondary text-uppercase small"><?php echo $lang->Start->active_uploads; ?></div>
                </div>
                </div>
        	</div>
        </div>
        <!-- end -->
        	<div class="col-12 col-sm-6 col-xl-6">
            <div class="card overflow-hidden" onclick="location.href='index.php?site=shares'">
            	<div class="card-body p-0 d-flex align-items-center">
                	<div class="bg-warning text-white p-4 me-2">
                    	<svg class="icon icon-xxl">
                        	<use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-folder-open"></use>
                        </svg>
                    </div>
                	<div>
                		<?php template::dashboard("share"); ?>
                	</div>
                </div>
        	</div>
        </div>
        <!-- end-->
        	<div class="col-12 col-sm-6 col-xl-6">
            <div class="card overflow-hidden" onclick="location.href='index.php?site=extras&show=credits/credits.php'">
            	<div class="card-body p-0 d-flex align-items-center">
                	<div class="bg-primary text-white p-4 me-2">
                    	<svg class="icon icon-xxl">
                        	<use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-diamond"></use>
                        </svg>
                    </div>
                <div>
                <div class="fs-4 fw-semibold"><?php

                    if ($information['CREDITS'] <= 0) {
                        $creditcolor = " class='text-danger'";
                    } else {
                        $creditcolor = " class='ext-success'";
                    }
                    echo "<span" . $creditcolor . " >" . subs::sizeformat($information['CREDITS']) . "</span>";
                    ?></div>
                <div class="text-body-secondary text-uppercase small"><?php echo $lang->Start->credits; ?></div>
                </div>
                </div>
        	</div>
        </div>
        <!-- end -->
        </div>
      <div class="row mt-4 mb-4">        
    <?php
    $coreinfo = $Servers->core->getcoreversion();
    $coresubversions = explode(".", $_SESSION['cache']['STATUSBAR']['VERSION']);
    $info = $Servers->info();
    //AJ News
    $subs->appleJuiceNews(90, $coreinfo['VERSION']);


    ?>
	</div>

	</div>
	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
	<div class="card mb-4">
            <div class="card-header"><svg class="icon icon-l">
                        	<use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-globe-alt"></use>
                        </svg> <?php echo $lang->Start->core_info; ?></div>
            <div class="card-body p-0">
                <table class="table">
                    <tbody>
                    <tr>
                        <td><?php echo $lang->Start->server_time; ?></td>
                        <td>
                            <?php
                            echo $Servers->time();
                            ?>
                        </td>
                    <tr>
                        <td nowrap>Core Version</td>
                        <td><?php echo $coreinfo['VERSION']; ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->op_system; ?></td>
                        <td nowrap><?php echo $icon_img->os_system[$coreinfo['SYSTEM']] . " " . $coreinfo['SYSTEM']; ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->connected_since; ?></td>
                        <td nowrap>
                            <?php
                            $srv_timediff = $Servers->netstats['timeconnected'];
                            $srv_timediff = sprintf("%dh %dmin", $srv_timediff / 3600, ($srv_timediff % 3600) / 60, $srv_timediff % 60);
                            echo $srv_timediff;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->open_connections; ?></td>
                        <td nowrap><?php echo $info['OPENCONNECTIONS']; ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->bytes_in; ?></td>
                        <td nowrap><?php echo subs::sizeformat($information['SESSIONDOWNLOAD']); ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->bytes_out; ?></td>
                        <td nowrap><?php echo subs::sizeformat($information['SESSIONUPLOAD']); ?></td>
                    </tr>
                    
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header"><svg class="icon icon-l">
                        	<use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-fork"></use>
                        </svg> <?php echo $lang->Start->network_info; ?></div>
            <div class="card-body p-0">
                <table class="table">
                    <tbody>
                    <tr>
                        <td nowrap><?php echo $lang->Start->download_speed; ?></td>
                        <td nowrap><?php echo subs::sizeformat($information['DOWNLOADSPEED']); ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->upload_speed; ?></td>
                        <td nowrap><?php echo subs::sizeformat($information['UPLOADSPEED']); ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->shared_users; ?></td>
                        <td nowrap><?php echo $Servers->netstats['users']; ?></td>
                    </tr>
                    <tr>
                        <td nowrap><?php echo $lang->Start->all_data; ?></td>
                        <td nowrap><?php echo number_format($Servers->netstats['filecount']) . "<br>" . subs::sizeformat($Servers->netstats['filesize']); ?></td>
                    </tr>
                    
                    </tbody>
                </table>
            </div>
        </div>

	</div>
	<!-- Satistik -->	
	</div>
	