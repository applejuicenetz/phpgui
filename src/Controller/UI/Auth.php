<?php

namespace applejuiceNETZ\UI;

class Auth
{
    public static function Login()
    {
        if (isset($_POST['host'])) {
            $core_host = $_POST['host'].':'.$_ENV["CORE_PORT"];
            $core_pass = 32 === strlen($_POST['cpass']) ? $_POST['cpass'] : md5($_POST['cpass']);
            $anfrage = "settings.xml";
            $type = "xml";

            // prüfe ob Passwort richtig
            $params['password'] = $core_pass;

            if (!str_contains($anfrage, "?")) {
                $anfrage .= "?";
            }

            $url = $core_host . '/' . $type . '/' . $anfrage . '&' . http_build_query($params);

            $xml_file = file_get_contents($url);

            if (empty($xml_file)) {
                $_SESSION['login']['host'] = "Kann nicht zum Core verbinden";
            } else {
                if (str_contains($xml_file, "wrong password.")) {
                    $_SESSION['login']['wrong_pass'] = "Falsches passwort";
                }
                if (empty($_SESSION['login']['host']) && empty($_SESSION['login']['wrong_pass'])) {
                    $_SESSION['core_pass'] = $core_pass;
                    $_SESSION["core_host"] = $core_host;
                }
            }
        }
        echo"First Login";
    }
    public static function Hidden()
    {
        echo"hallo";
    }
}