<?php

namespace appleJuiceNETZ\appleJuice;

class Core
{
    var $depth;
    var $lastname;
    var $lastsubname;
    var $xml_array;
	var $lastcdata;

    // XML-Parser funktionen
    //-----------------------
    function startElement($parser, $name, $attrs)
    {
        $keys = array_keys($attrs);
        $array_name = '[$name][$attrs[$keys[0]]]';
        if ($this->depth > 1) {
            for ($h = 0; $h <= ($this->depth - 2); $h++) {
                if ($h == 0) $array_name = '[$this->lastsubname[$this->depth]]' . $array_name;
                $array_name = '[$this->lastname[$this->depth-' . $h . ']]' . $array_name;
            }
        }
        if (!empty($keys[0])) {
            eval('$this->xml_array' . $array_name . '=array();'); // TODO eval is evil
            $evaltext = '$this->xml_array' . $array_name . '[$a]=$attrs[$a];';
            foreach ($keys as $a) {
                eval($evaltext); // TODO eval is evil
            }
        }
        $this->depth++;
        $this->lastname[$this->depth] = $name;
        if (!empty($keys[0])) {
            $this->lastsubname[$this->depth] = $attrs[$keys[0]];
        } else {
            $this->lastsubname[$this->depth] = 'VALUES';
        }
    }

    function endElement($parser, $name)
    {
        unset($this->lastname[$this->depth]);
        unset($this->lastsubname[$this->depth]);
        $this->depth--;
    }

    function characterData($parser, $data)
    {
        if (strlen(trim($data)) > 0 || $this->lastcdata == $this->lastname[$this->depth]
            . "*" . $this->lastsubname[$this->depth]
            . "*" . $this->depth) {
            $array_name = '[$this->lastsubname[$this->depth]]';
            if ($this->depth > 1) {
                for ($g = 0; $g <= ($this->depth - 2); $g++) {
                    $array_name = '[$this->lastname[$this->depth-' . $g . ']]'
                        . $array_name;
                }
            }
            if ($this->lastcdata == $this->lastname[$this->depth]
                . "*" . $this->lastsubname[$this->depth]
                . "*" . $this->depth)
                eval('$this->xml_array' . $array_name . '[\'CDATA\'].=$data;');
            else
                eval('$this->xml_array' . $array_name . '=array();
					$this->xml_array' . $array_name . '[\'CDATA\']=$data;');
            $this->lastcdata = $this->lastname[$this->depth]
                . "*" . $this->lastsubname[$this->depth]
                . '*' . $this->depth;
        }
    }

    //-----------------------

    function command($type, $anfrage, $update = "0")
    {
        if ($update == "0") {
            $this->xml_array = array();
        } else {
            if (empty($update)) $update = [];
            $this->xml_array = $update;    //daten aus xml in altes array schreiben
        }
        $this->depth = 0;
        $this->lastname = array();
        $this->lastsubname = array();
        $this->lastcdata = '';

        $params['password'] = $_SESSION['core_pass'];

        if (strpos($anfrage, "?") === false) $anfrage .= "?";

        $url = $_SESSION['core_host'] . '/' . $type . '/' . $anfrage . '&' . http_build_query($params);

        $xml_file = file_get_contents($url);

        if (str_contains($xml_file, "wrong password.")) {
            echo "Falsches passwort";
            session_destroy();
            exit;
        }

        if ($type === "xml") {
            if (empty($xml_file)) {
            	include_once("pages/500.php");
            	session_destroy();
                exit;
            }

            $xml_parser = xml_parser_create("UTF-8");
            xml_set_element_handler($xml_parser, array(&$this, "startElement"), array(&$this, "endElement"));
            xml_set_character_data_handler($xml_parser, array(&$this, "characterData"));

            $chunks = str_split($xml_file, 4096);

            foreach ($chunks as $chunk) {
                if (!xml_parse($xml_parser, $chunk)) {
                    echo "<br/><b>XML-Parser Fehler:</b> ";
                    echo xml_error_string(xml_get_error_code($xml_parser));
                    echo "<br/><br/>";
                }
            }

            xml_parse($xml_parser, '', true);

            xml_parser_free($xml_parser);
            unset($xml_file);   //weg damit...
            return ($this->xml_array);
        } else {
            if (!empty($xml_file)) return ($xml_file);
        }
    }

    function getcoreversion()
    {
        //Core version + betriebssystem holen
        if (empty($_SESSION['cache']['STATUSBAR']['VERSION'])) {
            $core_info = $this->command("xml", "information.xml");
            $_SESSION['cache']['STATUSBAR']['VERSION'] =
                $core_info['GENERALINFORMATION']['VERSION']['VALUES']['CDATA'];
            $_SESSION['cache']['STATUSBAR']['SYSTEM'] =
                $core_info['GENERALINFORMATION']['SYSTEM']['VALUES']['CDATA'];
        }
        return array("VERSION" => $_SESSION['cache']['STATUSBAR']['VERSION'],
            "SYSTEM" => $_SESSION['cache']['STATUSBAR']['SYSTEM']);
    }

}
