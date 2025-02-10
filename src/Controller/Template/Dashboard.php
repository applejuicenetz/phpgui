<?php

namespace appleJuiceNETZ\Template;

use appleJuiceNETZ\GUI\Plugins;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\Kernel;
use appleJuiceNETZ\UI\Language;
class Dashboard
{
    
    static function Downloads()
    {
      $language = Language::getLanguage();

        $Downloadlist = new Downloads();
        $Downloadlist->refresh_cache();
        $subdircounter = 0;
        //alle downloads zeigen
        foreach (array_keys($Downloadlist->subdirs) as $a)
        {
            $subdircounter++;
            $downloadids = $Downloadlist->ids("", $a); //ids der downloads sortiert holen
        }
        if($downloadids == NULL)
        {
            $result = "0";
        }else{
            $Downloadlist = new Downloads();
            $Downloadlist->refresh_cache();
            $subdircounter = 0;
            //alle downloads zeigen
            foreach (array_keys($Downloadlist->subdirs) as $a)
            {
                $subdircounter++;
                $downloadids = $Downloadlist->ids("status", $a); //ids der downloads sortiert holen
            }
            $str = array("0", "0_1", "1", "12", "13", "15", "16", "17", "14", "18");
            $str2 = array("14");
            
            $all = count (array_diff($downloadids, $str2)); //laufen
            $load = count (array_diff($downloadids, $str));
            $load_all = count (array_diff($downloadids, $str2)); //laufen
            $result = $load . '/' . $all;
        }
        echo'<div class="col-6 col-lg-5 col-xl-4 col-xxl-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="text-body-secondary text-end">
                              <svg class="icon icon-xxl">
                                <use xlink:href="' . WEBUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-cloud-download"></use>
                              </svg>
                            </div>
                            <div class="fs-4 fw-semibold">' . $result . '</div>
                            <div class="small text-body-secondary text-uppercase fw-semibold text-truncate">' . $language->translate('Start.active_downloads') . '</div>
                            <div class="progress progress-thin mt-3 mb-0">
                              <div class="progress-bar bg-info" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          </div>
                        </div>
                      </div>';
    }

    static function Uploads()
    {
      $language = Language::getLanguage();
        $Uploadlist = new Uploads();
        
        
        echo'<div class="col-6 col-lg-5 col-xl-4 col-xxl-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="text-body-secondary text-end">
                              <svg class="icon icon-xxl">
                                <use xlink:href="' . WEBUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-cloud-upload"></use>
                              </svg>
                            </div>
                            <div class="fs-4 fw-semibold">' . $Uploadlist->cache['phpaj_ul'] . '</div>
                            <div class="small text-body-secondary text-uppercase fw-semibold text-truncate">' . $language->translate('Start.active_uploads') . '</div>
                            <div class="progress progress-thin mt-3 mb-0">
                              <div class="progress-bar bg-success" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          </div>
                        </div>
                      </div>';
    }

    static function Shared()
    {
        $Sharelist = new Share();
        $language = Language::getLanguage();

    	if ($_ENV['GUI_SHOW_SHARE'])
        {
            $Sharelist->refresh_cache(30);
        }
        if (!empty($_SESSION['phpaj']['share_LASTTIMESTAMP']))
        {
            $share_anzahl = 0;
            $share_groesse = 0;
            foreach (array_keys($Sharelist->cache['SHARES']['VALUES']['SHARE']) as $a)
            {
                $share_anzahl++;
                $share_groesse += $Sharelist->cache['SHARES']['VALUES']['SHARE'][$a]['SIZE'];
            }
            echo'<div class="col-6 col-lg-5 col-xl-4 col-xxl-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="text-body-secondary text-end">
                              <svg class="icon icon-xxl">
                                <use xlink:href="' . WEBUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-folder-open"></use>
                              </svg>
                            </div>
                            <div class="fs-4 fw-semibold">' . subs::sizeformat($share_groesse) . '</div>
                            <div class="small text-body-secondary text-uppercase fw-semibold text-truncate">' . number_format($share_anzahl) . ' ' . $language->translate('Start.share_dat') . '</div>
                            <div class="progress progress-thin mt-3 mb-0">
                              <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                          </div>
                        </div>
                      </div>';
        } else {
        }
    }

    static function Credits()
    {
        $core = new Core();
        $modified = $core->command("xml", "modified.xml?filter=informations");
        $temp = array_keys($modified['INFORMATION']);
        $information =& $modified['INFORMATION'][$temp[0]];
        $language = Language::getLanguage();

        echo'<div class="col-6 col-lg-5 col-xl-4 col-xxl-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="text-body-secondary text-end">
                              <svg class="icon icon-xxl">
                                <use xlink:href="' . WEBUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-bank"></use>
                              </svg>
                            </div>
                            <div class="fs-4 fw-semibold">';

if ($information['CREDITS'] <= 0) {
    $creditcolor = " class='text-danger'";
} else {
    $creditcolor = " class='ext-success'";
}
echo "<span" . $creditcolor . " >" . subs::sizeformat($information['CREDITS']) . "</span>";
echo'</div>
                            <div class="small text-body-secondary text-uppercase fw-semibold text-truncate">' . $language->translate('Start.credits') .'</div>
                            <div class="progress progress-thin mt-3 mb-0">
                            </div>
                          </div>
                        </div>
                      </div>';
    }

    static function Server()
    {
      $Servers = new Server();
      $core = new Core();
      $language = Language::getLanguage();

      echo'<div class="col-12 col-lg-12 col-xl-8 col-xxl-8">
      <div class="card">
        <div class="card-body">
          <div class="text-body-secondary text-end">
            <svg class="icon icon-xxl">
              <use xlink:href="' . WEBUI_THEME . '/vendors/@coreui/icons/svg/free.svg#cil-storage"></use>
            </svg>
          </div>
          <div class="fs-4 fw-semibold">' . $Servers->netstats['servername'] . '</div>
          <div class="small">';
           if (!empty($Servers->netstats['welcome'])) {
                        echo $Servers->netstats['welcome'];
                    }
          echo'</div>';
          $statusbar_xml=$core->command("xml","modified.xml?filter=informations");
        $temp2=array_keys($statusbar_xml['NETWORKINFO']);
        $netinfo=&$statusbar_xml['NETWORKINFO'][$temp2[0]];
        
        if($netinfo['CONNECTEDWITHSERVERID'] < 0)
        {
          echo'<div class="small text-body-secondary text-uppercase fw-semibold text-truncate text-end" style="animation: blinken 1s linear infinite;">
          <p></p>
          <p class="class="blink-text text-center">Verbindung wird hergestellt...</p>
          </div>';
        }else{
          echo'<div class="small text-body-secondary text-uppercase fw-semibold text-truncate text-end">
            ' . $language->translate('Start.connected_since') . ' ';
            $srv_timediff = floor($Servers->netstats['timeconnected']);

// Berechnungen für Stunden und Minuten mit intdiv()
$srv_timediff_formatted = gmdate("H\h i\m", $srv_timediff);

// Ausgabe der formatierten Zeit
echo $srv_timediff_formatted . '
            
          </div>';
        }
        echo'</div>
      </div>
    </div>';
    }

    static function Informations()
    {
      $Servers = new Server();
      $language = Language::getLanguage();
      $coreinfo = $Servers->core->getcoreversion();
      $core = new Core();
      $settings_xml=$core->command("xml","settings.xml");
      $modified = $core->command("xml", "modified.xml?filter=informations");
      $temp = array_keys($modified['INFORMATION']);
      $information =& $modified['INFORMATION'][$temp[0]];
      $info = $Servers->info();
      $icon_img = new Icons();
      $subs = new subs();

      echo'<div class="card-title fs-4 fw-semibold mb-4">' . $language->translate('Start.core_info') . '</div>
            <div class="row">
                        <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="border-start border-start-4 border-start-info px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">' . $language->translate('Start.server_time') . '</div>
                            <div class="fs-5 fw-semibold">' . $Servers->time() . '</div>
                          </div>
                        </div>
                        <!-- /.col-->
                        <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="border-start border-start-4 border-start-info px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">Core-Version</div>
                            <div class="fs-5 fw-semibold">' . $coreinfo['VERSION'] . '</div>
                          </div>
                        </div>
                        <!-- /.col-->
                        <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="border-start border-start-4 border-start-info px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">' . $language->translate('Start.op_system') .'</div>
                            <div class="fs-5 fw-semibold">' . $icon_img->os_system[$coreinfo['SYSTEM']] . ' ' . $coreinfo['SYSTEM'] . '</div>
                          </div>
                        </div>
                        <!-- /.col-->
                      </div>
            <div class="card-title fs-4 fw-semibold mb-4">' . $language->translate('Start.network_info') . '</div>
            <div class="row">
                        <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="border-start border-start-4 border-start-warning px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">' . $language->translate('Start.open_connections') . '</div>
                            <div class="fs-5 fw-semibold">' . $info['OPENCONNECTIONS'] . ' / ' . $settings_xml["MAXCONNECTIONS"]["VALUES"]["CDATA"] . '</div>
                          </div>
                        </div>
                        <!-- /.col-->
                        <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="border-start border-start-4 border-start-warning px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">' . $language->translate('Start.download_speed') . '</div>
                            <div class="fs-5 fw-semibold">' . subs::sizeformat($information['DOWNLOADSPEED']) . '</div>
                          </div>
                        </div>
                        <!-- /.col-->
                        <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="border-start border-start-4 border-start-warning px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">' . $language->translate('Start.upload_speed') . '</div>
                            <div class="fs-5 fw-semibold">' . subs::sizeformat($information['UPLOADSPEED']) . '</div>
                          </div>
                        </div>
                        <!-- /.col-->
                        <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="border-start border-start-4 border-start-warning px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">' . $language->translate('Start.bytes_in') . '</div>
                            <div class="fs-5 fw-semibold">' . subs::sizeformat($information['SESSIONDOWNLOAD']) . '</div>
                          </div>
                        </div>
                        <!-- /.col-->
                        <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="border-start border-start-4 border-start-warning px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">' . $language->translate('Start.bytes_out') . '</div>
                            <div class="fs-5 fw-semibold">' . subs::sizeformat($information['SESSIONUPLOAD']) . '</div>
                          </div>
                        </div>
                        <!-- /.col-->
                      </div> 
              <div class="card-title fs-4 fw-semibold mb-4">Community</div>
            <div class="row">
                        <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="border-start border-start-4 border-start-success px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">' . $language->translate('Start.shared_users') . '</div>
                            <div class="fs-5 fw-semibold">' . $Servers->netstats['users'] . '</div>
                          </div>
                        </div>
                        <!-- /.col-->
                        <div class="col-12 col-lg-8 col-xl-8 col-xxl-8">
                          <div class="border-start border-start-4 border-start-success px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">' . $language->translate('Start.all_data') . '</div>
                            <div class="fs-5 fw-semibold">' . number_format($Servers->netstats['filecount']) . ' (' . subs::sizeformat($Servers->netstats['filesize']) . ')</div>
                          </div>
                        </div>
                        <!-- /.col-->
                      </div> 
            ';
            
    }

    static function Traffic()
    {
      $subs = new subs();
      $Sharelist = new Share;
      $Sharelist->refresh_cache(2);
      $sfsort = subs::ajsort($Sharelist->cache['SHARES']['VALUES']['SHARE'], 'LASTASKED', SORT_NUMERIC, 1);
      $statsvalue = 'LASTASKED';

      // Erstelle das Array für die Verkehrsdaten
      $array_traffic = [];

      // Iteriere über das sortierte Array
      foreach ($sfsort as $index => $value) {
      $cur_share = $Sharelist->get_file($index);
      $wert = (int)($cur_share[$statsvalue] / 1000);

      // Datum und Monat berechnen
      $date = date("j.n.y", $wert);
      $month = date("n.y", $wert);

      // Füge die Werte dem Array hinzu
      $array_traffic[] = [
          'date' => $date,
         'month' => $month
      ];
    }

// Berechne die aktuellen und vergangenen Daten
$date_h = date("j.n.y", time());
$date_g = date("j.n.y", strtotime("-1 day"));
$date_m = date("n.y", time());
     
      echo'<div class="card-title fs-4 fw-semibold mb-4">Traffic</div>
            <div class="row">
                        <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="border-start border-start-4 border-start-info px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">Heute</div>
                            <div class="fs-5 fw-semibold">' . subs::find_children($array_traffic, 'date', $date_h) . '</div>
                          </div>
                        </div>
                        <!-- /.col-->
                        <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="border-start border-start-4 border-start-info px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">Gestern</div>
                            <div class="fs-5 fw-semibold">' . subs::find_children($array_traffic, 'date', $date_g) . '</div>
                          </div>
                        </div>
                        <!-- /.col-->
                        <div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
                          <div class="border-start border-start-4 border-start-info px-3 mb-3">
                            <div class="small text-body-secondary text-truncate">Monat</div>
                            <div class="fs-5 fw-semibold">' . subs::find_children($array_traffic, 'month', $date_m) . '</div>
                          </div>
                        </div>
                        <!-- /.col-->
                      </div>
           
            ';
            
    }
}