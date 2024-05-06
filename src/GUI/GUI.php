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
            $template->alert("warning", $lang->System->version_1, $lang->System->version_akt . $_SESSION['phpaj']['akt_version']);
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
