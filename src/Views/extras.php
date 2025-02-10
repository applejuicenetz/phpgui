<?php

use appleJuiceNETZ\UI\Plugin;

if(!isset($_GET['plugin']))
{
    Plugin::list_plugins_with_info(GUI_ROOT . '/plugins/'); // Ersetze mit dem tatsächlichen Ordner
}
else
{
    Plugin::show_plugin($_GET['plugin']); 
}