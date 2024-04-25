<?php

class Plugin
{
    var $liste;

    function __construct()
    {
        $this->liste = [];
    }

    function Find_Plugins()
    {
        $dirlist = opendir(GUI_ROOT . "/plugins");
        while (false !== ($file = readdir($dirlist))) {
            if (is_dir("plugins/$file")
                && file_exists(GUI_ROOT . "/plugins/$file/info.php")) {
                include(GUI_ROOT . "/plugins/$file/info.php");
            }
        }
    }

    function register($name, $icon = "", $filename)
    {
        $this->liste[] = [$name, $icon, $filename];
    }
}	
