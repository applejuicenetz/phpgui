<?php

namespace appleJuiceNETZ\UI;

class View
{
     // Mach $dir statisch, damit es in statischen Methoden genutzt werden kann
     static $dir;

     public function __construct()
     {
        // Wenn du eine Instanz erstellst, setzt du den Wert von $dir
        self::$dir = 'src/Views/';
     }

    // Jetzt sind beide Methoden statisch
    public static function Template($var)
    {
        require_once GUI_ROOT . self::$dir . '_' . $var . '.php';
    }
    
    public static function Content($var)
    {
        require_once GUI_ROOT . self::$dir . $var . '.php';
    }
}
