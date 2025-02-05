<?php

namespace appleJuiceNETZ\UI;

use appleJuiceNETZ\UI\View;



class Router
{
    function handle()
    {
        $View = new View();
        if (!empty($_SESSION['core_host']))
        {
            $View->Template("Body");
        }
        else
        {
echo "hallo"; 
            if (isset($_POST['host'])) {
                
                $core_host = $_POST['host'].":". $_ENV['CORE_PORT'];
                $core_pass = 32 === strlen($_POST['cpass']) ? $_POST['cpass'] : md5($_POST['cpass']);
                $anfrage = "c2V0dGluZ3MueG1s";
                $type = "eG1s";
    
                // prüfe ob Passwort richtig
                $params['password'] = $core_pass;
    
                if (!str_contains($anfrage, "?")) {
                    $anfrage .= "?";
                }
    
                $url = $core_host . '/' . base64_decode($type) . '/' . base64_decode($anfrage) . '?' . http_build_query($params);
    echo $url;
                $xml_file = file_get_contents($url);
    
                if (empty($xml_file)) {
                    $_SESSION['core_host'] = "Kann nicht zum Core verbinden";
                } else {
                    if (str_contains($xml_file, "wrong password.")) {
                        $_SESSION['wrong_pass'] = "Falsches passwort";
                    }
                    if (empty($_SESSION['host']) && empty($_SESSION['wrong_pass'])) {
                        $_SESSION['core_pass'] = $core_pass;
                        $_SESSION["core_host"] = $core_host;
                    }
                }
            }

            $View->Content("Auth");
        }
    }
    public static function ContentLoaded($var)
    {
        $View = new View();
        if (!empty($_SESSION['core_host']))
        {
            $View->Content($_GET['site']);
        }
        

    }
}
