<?php

namespace appleJuiceNETZ\GUI;

use appleJuiceNETZ\Kernel;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Uploads;

class GUI
{
    function check_version($var): void
    {
        $language = Kernel::getLanguage();
        $lang = $language->translate();
        $template = new template();

        $akt_ver = file_get_contents($_ENV['CHANGELOG_URL']);

        foreach ($akt_ver as $line_num => $line) {
            if ($line_num == 4) {
                $version = str_replace("#", "", $line);

                $version = trim($version);

                $_SESSION['phpaj']['akt_version'] = $version;
echo $version;
            }

        }

        if (version_compare($_SESSION['phpaj']['akt_version'], PHP_GUI_VERSION, '>')) {
        	$vnew_version = str_replace("%version%", $_SESSION['phpaj']['akt_version'], $lang->System->version);
        	echo'<div class="row mb-4">
            <div class="col-xl-12 col-xxl-12"><a class="banner-coreui-pro" href="https://github.com/applejuicenetz/phpgui/tree/beta">
                <svg class="banner-coreui-pro-logo d-xl-none d-xxl-block" width="100" height="100" alt="CoreUI Logo">
                  <use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-history"></use>
                </svg>
                <h4 class="fw-bolder">' . $vnew_version . '</h4>
                <p>' . $lang->System->version_akt . '</p>
              </a></div>
          </div>
          ';
        }
    }

    function versions_update($var)
    {
        $akt_ver = file($_ENV['CHANGELOG_URL']);

        foreach ($akt_ver as $line_num => $line) {
            if ($line_num == 4) {
                $version = str_replace("#", "", $line);

                $version = trim($version);

                $_SESSION['phpaj']['akt_version'] = $version;
            }

        }

        if (version_compare($_SESSION['phpaj']['akt_version'], PHP_GUI_VERSION, '>')) {
            return '<span class="col-danger font-bold">' . PHP_GUI_VERSION . '</span>';
        } else {
            return PHP_GUI_VERSION;
        }
    }
    
    public static function refresh()
    {
    	$Downloadlist = new Downloads();
    	$Uploadlist = new Uploads();
    	
    	$Downloadlist->refresh_cache();
    	$Uploadlist->refresh_cache();
    }


}
