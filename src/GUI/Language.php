<?php

namespace appleJuiceNETZ\GUI;

class Language
{
    private $data;

    function __construct($lang)
    {
        $data = file_get_contents(filename: GUI_ROOT . "/language/" . $lang . ".json");
        $this->data = json_decode($data);
    }

    function translate()
    {
        return $this->data;
    }
}
