<?php

namespace appleJuiceNETZ\GUI;

use appleJuiceNETZ\GUI\Plugins;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\Kernel;
use appleJuiceNETZ\UI\Language;

class template
{
    public static function bread($a, $b)
    {
        // Start des Breadcrumbs
        $breadcrumb = '<nav aria-label="breadcrumb">
                        <ol class="breadcrumb my-0">
                          <li class="breadcrumb-item">Dashboard</li>';
    
        // Wenn $a nicht "Dashboard" ist
        if ($a != "Dashboard") {
            // Überprüfen, ob $a den Unterstrich enthält (z.B. "Downlados_User")
            if (strpos($a, '_') !== false) {
                // Den Wert von $a an der Stelle des Unterstrichs teilen
                list($part1, $part2) = explode('_', $a);
                
                // Füge den ersten Teil des Wertes hinzu
                $breadcrumb .= '<li class="breadcrumb-item">' . $part1 . '</li>';
                
                // Füge den zweiten Teil des Wertes hinzu
                $breadcrumb .= '<li class="breadcrumb-item active"><span>' . $part2 . '</span></li>';
            } else {
                // Wenn kein Unterstrich vorhanden ist, normalen Breadcrumb ausgeben
                $breadcrumb .= '<li class="breadcrumb-item active"><span>' . $a . '</span></li>';
            }
        }
    
        // Schließen des Breadcrumbs
        $breadcrumb .= '</ol>
                        </nav>';
    
        // Ausgabe des kompletten Breadcrumbs
        echo $breadcrumb;
    }

    static function active($a)
    {
        echo ($a == $_GET['site']) ? ' active' : '';
    }

    public static function alert($alert, $strong, $text)
    {
        // Array mit den Zuordnungen von Alert-Typen zu Icons
        $icons = [
            "success" => "check-circle",
            "info" => "info-circle",
            "warning" => "exclamation-triangle",
            "danger" => "exclamation-triangle"
        ];

        // Prüfen, ob der Alert-Typ existiert und das Icon zuweisen
        $icon = isset($icons[$alert]) ? $icons[$alert] : "question-circle"; // Standard-Icon wenn keiner der Werte zutrifft

        // HTML-Ausgabe
        echo '<div class="alert alert-' . $alert . ' alert-dismissible fade show" role="alert">
                <i class="fa fa-fw fa-' . $icon . '"></i>
                <strong>' . $strong . '</strong> ' . $text . '
                <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    
    function status($wert)
    {
        // Definiere die Zuordnungen in einem Array
        $icons = [
            "0"   => ['cil-search', 'primary'],
            "0_1" => ['cil-search', 'primary'],
            "14"  => ['cil-check', 'success'],
            "18"  => ['cil-media-pause', 'warning'],
            "17"  => ['cil-ban', 'danger']
        ];

        // Prüfen, ob der Wert im Array existiert, andernfalls Standardwerte setzen
        if ($wert != "0_2")
        {
            // Überprüfe, ob der Wert im Array enthalten ist, sonst Standardwerte setzen
            if (isset($icons[$wert]))
            {
                list($icon, $color) = $icons[$wert];
            }
            else
            {
                // Standardwerte, wenn keiner der Bedingungen zutrifft
                $icon = 'cil-check';  // Standard-Icon
                $color = 'gray';      // Standard-Farbe
            }
            // HTML-Ausgabe
            return '<div class="col-6 text-end text-' . $color . '">
                        <svg class="icon icon-xxl">
                        <use xlink:href="' . WEBUI_THEME . '/vendors/@coreui/icons/svg/free.svg#' . $icon . '"></use>
                        </svg>
                    </div>';
        }
        else
        {
                return '<div class="col-xs-4 col-sm-4 col-md-1 col-lg-1">
                    <div class="spinner-border text-info" role="status">
                    <span class="visually-hidden">Loading...</span>
                    </div>
                </div>';
        }
    }


    // Überarbeiten!!
    static function dashboard($var)
    {
        $language = Language::getLanguage();
       

    	if($var == "download")
    	{
			$Downloadlist = new Downloads();
            $Downloadlist->refresh_cache();
            $subdircounter = 0;
            //alle downloads zeigen
            foreach (array_keys($Downloadlist->subdirs) as $a) {
                $subdircounter++;
                $downloadids = $Downloadlist->ids("", $a); //ids der downloads sortiert holen
            			    
            }
            if($downloadids == NULL){
            	echo"0";
            }else{
        
    	    $Downloadlist = new Downloads();
            $Downloadlist->refresh_cache();
            $subdircounter = 0;
            //alle downloads zeigen
            foreach (array_keys($Downloadlist->subdirs) as $a) {
                $subdircounter++;
                $downloadids = $Downloadlist->ids("status", $a); //ids der downloads sortiert holen
            			    
            }
            $str = array("0", "0_1", "1", "12", "13", "15", "16", "17", "14", "18");
            $str2 = array("14");
            
            $all = count (array_diff($downloadids, $str2)); //laufen
            $load = count (array_diff($downloadids, $str));
            $load_all = count (array_diff($downloadids, $str2)); //laufen
            echo $load . '/' . $all;
            
                        	
    	}}
    	if($var == "download_finish")
    	{	$Downloadlist = new Downloads();
            $Downloadlist->refresh_cache();
            $subdircounter = 0;
            //alle downloads zeigen
            foreach (array_keys($Downloadlist->subdirs) as $a) {
                $subdircounter++;
                $downloadids = $Downloadlist->ids("", $a); //ids der downloads sortiert holen
            			    
            }
            if($downloadids == NULL){
            	echo"0";
            }else{
        
    	    $Downloadlist = new Downloads();
            $Downloadlist->refresh_cache();
            $subdircounter = 0;
            //alle downloads zeigen
            foreach (array_keys($Downloadlist->subdirs) as $a) {
                $subdircounter++;
                $downloadids = $Downloadlist->ids("status", $a); //ids der downloads sortiert holen
            			    
            }
            $str = array("0", "0_1", "0_2", "1", "12", "13", "15", "16", "17", "18");
             $str2 = array("14");
            
            $all = count (array_diff($downloadids, $str2)); //laufen;
            $finish = count (array_diff($downloadids, $str)); // fertig
            
            $count = $finish / $all * 100;
            
            echo $count;
            
            }             	
    	}
    	if($var == "share")
    	{
    		$Sharelist = new Share();
    		if ($_ENV['GUI_SHOW_SHARE']) {
                    $Sharelist->refresh_cache(30);
                }

                if (!empty($_SESSION['phpaj']['share_LASTTIMESTAMP'])) {
                    $share_anzahl = 0;
                    $share_groesse = 0;
                    foreach (array_keys($Sharelist->cache['SHARES']['VALUES']['SHARE']) as $a) {
                        $share_anzahl++;
                        $share_groesse += $Sharelist->cache['SHARES']['VALUES']['SHARE'][$a]['SIZE'];
                    }
                    echo '
                    	<div class="fs-4 fw-semibold">' . subs::sizeformat($share_groesse) . '</div>
                		<div class="text-body-secondary text-uppercase small">' . number_format($share_anzahl) . ' ' . $language->translate('Start.share_dat') . '</div>
                	';
                } else {
        }
                
    	}
    }
    
    
	
	static function uploads()
	{
		$Uploadlist = new Uploads();
		
		if( $Uploadlist->cache['phpaj_ul'] > 0 )
		{
			echo'<span class="badge badge-sm bg-info ms-auto">' . $Uploadlist->cache['phpaj_ul'] . '</span>';
		}else{
		
		}
	}
	static function upload_sm()
	{
		$Uploadlist = new Uploads();
		
		if( $Uploadlist->cache['phpaj_ul'] > 0 )
		{
			echo'<span class="position-absolute top-5 start-20 translate-middle badge rounded-pill bg-danger">
    			' . $Uploadlist->cache['phpaj_ul'] . '</span>';
		}else{
		
		}
  
	}
	public static function js_file($site)
	{
		echo'<script src="public/assets/js/' . $site . '.js"></script>';
    	
	}
	public static function toast($action, $alert)
	{
        $language = Language::getLanguage();
   
		if($action == "resumedownload") $text = "Download wurde fortgesetzt.";
		if($action == "pausedownload") $text = "Download wurde pausiert.";
		if($action == "cleandownloadlist") $text = $language->translate('Downloads.cleardownloadlist');
		if($action == "canceldownload") $text = $language->translate('Downloads.canceldownload');
        if($action == "serverlogin") $text = "Serverwechsel wird beauftragt. Dies kann einige Zeit in Anspruch nehmen.";
		
		
		return '<div style="position: fixed;
  top: 120px;
  right: 5px;
  z-index: 300;
  opacity: 0.9;"><div class="toast align-items-center text-white bg-' . $alert . ' border-0 fade show" role="alert" aria-live="assertive" aria-atomic="true">
                      <div class="d-flex">
                        <div class="toast-body">' . $text . '</div>
                        <button class="btn-close btn-close-white me-2 m-auto" type="button" data-coreui-dismiss="toast" aria-label="Close"></button>
                      </div>
                    </div></div>';	
	}

    
}