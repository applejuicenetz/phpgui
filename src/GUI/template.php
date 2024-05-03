<?php

namespace appleJuiceNETZ\GUI;

use appleJuiceNETZ\GUI\Plugins;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\Kernel;

class template
{
    public static function bread($a, $b)
    {
       echo' <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0">
              <li class="breadcrumb-item">Dashboard
              </li>';
              if($a != "start"){
              echo '<li class="breadcrumb-item active"><span>' . $b .'</span>
              </li>';
              }
              
          echo '  </ol>
          </nav>
        ';
    }

    public static function alert($alert, $strong, $text)
    {
        // $alert = success, warning, danger, info
        if ($alert == "success") $icon = "check-circle";
        if ($alert == "info") $icon = "info-circle";
        if ($alert == "warning") $icon = "exclamation-triangle";
        if ($alert == "danger") $icon = "exclamation-triangle";
		
		echo '<div class="alert alert-' . $alert . ' alert-dismissible fade show" role="alert">
				<i class="fa fa-fw fa-' . $icon . '"></i>
				<strong>' . $strong . '</strong> ' . $text . '
				<button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
			  </div>';
    }

    function errors()
    {

    }
    
    static function dashboard($var)
    {
        $language = Kernel::getLanguage();
        $lang = $language->translate();

    	if($var == "download")
    	{
    	    $Downloadlist = new Downloads();
            $Downloadlist->refresh_cache();
            $subdircounter = 0;
            //alle downloads zeigen
            foreach (array_keys($Downloadlist->subdirs) as $a) {
                $subdircounter++;
                $downloadids = $Downloadlist->ids("status", $a); //ids der downloads sortiert holen
            			    
            }
            $str = array("0", "0_1", "1", "12", "13", "15", "16", "17");
            $str2 = array("14");
            
            $all = count (array_diff($downloadids, $str2)); //laufen
            $load = count (array_diff($downloadids, $str));
            
            echo $load . '/' .$all;
           
                        	
    	}
    	if($var == "download_finish")
    	{
    	    $Downloadlist = new Downloads();
            $Downloadlist->refresh_cache();
            $subdircounter = 0;
            //alle downloads zeigen
            foreach (array_keys($Downloadlist->subdirs) as $a) {
                $subdircounter++;
                $downloadids = $Downloadlist->ids("status", $a); //ids der downloads sortiert holen
            			    
            }
            $str = array("0", "0_1", "0_2", "1", "12", "13", "15", "16", "17");
             $str2 = array("14");
            
            $all = count (array_diff($downloadids, $str2)); //laufen;
            $finish = count (array_diff($downloadids, $str)); // fertig
            
            $count = $finish / $all * 100;
            
            echo $count;
            
                        	
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
                		<div class="text-body-secondary text-uppercase small">' . number_format($share_anzahl) . ' ' . $lang->Start->share_dat . '</div>
                	';
                } else {
        }
                
    	}
    }
    
    static function active($a)
    {
     if($a == $_GET['site'])
     {
    	echo ' active';
     }else{}
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
	public static function js_file($site)
	{
		if (file_exists(GUI_ROOT . "/vendor/js/" . $site . ".js"))
		{
    		echo'<script src="vendor/js/' . $site . '.js"></script>';
    	}	
	}
}