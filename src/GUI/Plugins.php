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
        $dirlist = opendir("../plugins");
        while (false !== ($file = readdir($dirlist))) {
            if (is_dir("../plugins/$file")
                && file_exists("../plugins/$file/info.php")) {
                include("../plugins/$file/info.php");
            }
        }
    }

    function register($name, $icon = "", $filename)
    {
        $this->liste[] = [$name, $icon, $filename];
    }
}	
