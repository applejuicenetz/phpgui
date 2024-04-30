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

    function bread2($a)
    {
        $tab = ucfirst($a);
        foreach ($_GET as $key => $value) {
            $sub = substr($_GET["$key"], 0, strpos($_GET["$key"], '/'));
            return '<li class="breadcrumb-item active">' . ucfirst($sub) . '</li>';
        }
    }

    function alert($alert, $strong, $text)
    {
        // $alert = success, warning, danger, info
        if ($alert == "success") $icon = "check-circle";
        if ($alert == "info") $icon = "info-circle";
        if ($alert == "warning") $icon = "exclamation-triangle";
        if ($alert == "danger") $icon = "exclamation-triangle";

        echo ' <div class="alert alert-' . $alert . '" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <i class="fa fa-fw fa-' . $icon . '"></i>
                                    <strong>' . $strong . '</strong> ' . $text . '
                                </div>
                               ';
    }

    function errors()
    {

    }
    public static function plugins()
    {
    	//PLugins auslesen
		$Plugin = new Plugin();
		$Plugin->Find_Plugins();
		
		foreach($Plugin->liste as $a)
		{
            echo '<li class="nav-item">
            		<a class="nav-link" href="index.php?site=extras&show=' . $a[2] . '">
            		<span class="nav-icon"><span class="nav-icon-bullet"></span></span> 
            		' . $a[0] . '</a></li>
            ';
        }

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
            foreach (array_keys($Downloadlist->subdirs) as $subdir) {
                $subdircounter++;
                $downloadids = $Downloadlist->ids("", $subdir); //ids der downloads sortiert holen
            }
            echo count($downloadids);
                        	
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

}