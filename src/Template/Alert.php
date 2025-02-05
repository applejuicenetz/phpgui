<?php

namespace appleJuiceNETZ\Template;

use appleJuiceNETZ\GUI\Plugins;
use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\Kernel;

class Alert
{
    public static function Notification($alert, $strong, $text)
    {
        // $alert = success, warning, danger, info
        if ($alert == "success") $icon = "check-circle";
        if ($alert == "info") $icon = "info-circle";
        if ($alert == "warning") $icon = "exclamation-triangle";
        if ($alert == "danger") $icon = "exclamation-triangle";
		
		echo '
		
		<div class="alert alert-' . $alert . ' alert-dismissible fade show" role="alert">
				<i class="fa fa-fw fa-' . $icon . '"></i>
				<strong>' . $strong . '</strong> ' . $text . '
				<button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
			  </div>';
    }
}