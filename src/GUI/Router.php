<?php

namespace appleJuiceNETZ\GUI;

use appleJuiceNETZ\appleJuice\Core;

class Router
{
    function handle(): void
    {
        $permalink = isset($_GET['l']) && is_string($_GET['l'])
            ? Permalink::parse($_GET['l'])
            : null;

        if ($permalink !== null || isset($_POST['host'])) {
            $core = new Core();
            $core_host = $permalink[0] ?? $_POST['host'];
            $password = $permalink[1] ?? $_POST['cpass'];
            $core_pass = 32 === strlen($password) ? $password : md5($password);
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
                    if ($permalink !== null) {
                        header('Location: index.php', true, 303);
                        return;
                    }
                }
            }
        }

        if (!empty($_SESSION["core_host"])) {
            require(GUI_ROOT . "/pages/_body.php");
        } else {
            require(GUI_ROOT . "/pages/login.php");
        }
    }
}
