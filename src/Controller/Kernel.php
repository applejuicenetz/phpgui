<?php

namespace appleJuiceNETZ;

use appleJuiceNETZ\UI\Language;

class Kernel
{
    private static array $instances = [];

    public static function init(): void
    {
        // Überprüfen, ob die .env Datei existiert
        $envFile = '.env';

        // Die Datei zeilenweise lesen
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line)
        {
            // Kommentarzeilen oder leere Zeilen überspringen
            if (strpos(trim($line), '#') === 0)
            {
                continue;
            }

            // Splitte die Zeile bei dem ersten '='
            list($key, $value) = explode('=', $line, 2);

            // Entferne unerwünschte Leerzeichen
            $key = trim($key);
            $value = trim($value);

            // Setze die Umgebungsvariable
            putenv("$key=$value");

            // Optional: Setze die Variable auch im $_ENV Array
            $_ENV[$key] = $value;
        }
#
      $_ENV['REAL_IP'] = 'http://' . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);
#
#        $_ENV['FAQ_URL'] = $_ENV['FAQ_URL'] ?? 'https://applejuicenetz.github.io/faq/';
#
#        $_ENV['CHANGELOG_URL'] = $_ENV['CHANGELOG_URL'] ?? 'https://raw.githubusercontent.com/applejuicenetz/phpgui/beta/CHANGELOG.md';
#
#        $_ENV['SERVERLIST_URL'] = $_ENV['SERVERLIST_URL'] ?? 'http://www.applejuicenet.cc/serverlist/xmllist.php';

      $_ENV['ALLOWED_SERVERMSG_TAGS'] = $_ENV['ALLOWED_SERVERMSG_TAGS'] ?? '<a><b><i><u><br>';

#        $_ENV['REL_INFO'] = $_ENV['REL_INFO'] ?? base64_decode('aHR0cHM6Ly93d3cuYXBwbGUtZGVsdXhlLmNvL2luZGV4LnBocD9jdD00MDMmdmE9JXM=');

#        $_ENV['GUI_SHOW_NEWS'] = $_ENV['GUI_SHOW_NEWS'] ?? 1;

#        $_ENV['GUI_SHOW_SHARE'] = $_ENV['GUI_SHOW_SHARE'] ?? 1;

#        date_default_timezone_set($_ENV['TZ'] ?? 'Europe/Berlin');

#        ini_set('error_reporting', $_ENV['PHP_INI_ERROR_REPORTING'] ?? '1');
#        ini_set('display_errors', $_ENV['PHP_INI_DISPLAY_ERRORS'] ?? 'On');
   
    }
   
}
