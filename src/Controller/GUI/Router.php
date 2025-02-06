<?php
session_start();

// Eingabe validieren
$anfrage = filter_input(INPUT_GET, 'anfrage', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$core_host = filter_input(INPUT_POST, 'core_host', FILTER_SANITIZE_URL);
$core_pass = filter_input(INPUT_POST, 'core_pass', FILTER_SANITIZE_STRING);

if (isset($_POST['core_pass'])) {
    $core_pass = filter_input(INPUT_POST, 'core_pass', FILTER_SANITIZE_STRING);
} else {
    echo "Das Passwort wurde nicht übermittelt.";
}

if ($core_pass && $core_host) {
    // URL aufbauen
    if (!str_contains($anfrage, "?")) {
        $anfrage .= "?";
    }

    $url = rtrim($core_host, '/') . '/' . $type . '/' . $anfrage . '&' . http_build_query($params);

    // cURL für den Abruf verwenden
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout setzen
    $xml_file = curl_exec($ch);

    if (curl_errno($ch)) {
        $_SESSION['login']['host'] = "Kann nicht zum Core verbinden: " . curl_error($ch);
    } else {
        if (empty($xml_file)) {
            $_SESSION['login']['host'] = "Keine Daten erhalten";
        } elseif (str_contains($xml_file, "wrong password.")) {
            $_SESSION['login']['wrong_pass'] = "Falsches Passwort";
        } else {
            // Hier sollte eine bessere Überprüfung des XML-Inhalts erfolgen
            $_SESSION['core_pass'] = $core_pass;
            $_SESSION["core_host"] = $core_host;
        }
    }
    curl_close($ch);
}

// Weiterleitung basierend auf den Session-Daten
if (!empty($_SESSION["core_host"])) {
    require(GUI_ROOT . "/pages/_body.php");
} else {
    require(GUI_ROOT . "/pages/login.php");
}