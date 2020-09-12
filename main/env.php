<?php
/**
 * HIER KEINE ÄNDERUNG DURCHFÜHREN!
 * Kopiere die ".env.dist" Datei zu ".env" und konfiguriere dort das GUI!
 */
if (!version_compare(PHP_VERSION, '7.4')) {
    die('PHP 7.4 required, used: -> ' . PHP_VERSION);
}

if (file_exists('.env')) {
    $config = parse_ini_file('.env');
    if (false === $config) {
        die('.env file parse error?!');
    } else {
        $_ENV = array_merge($_ENV, $config);
    }
}

$_ENV['REAL_IP'] = 'http://' . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);

$_ENV['GUI_REFRESH_STATUS'] = $_ENV['GUI_REFRESH_STATUS'] ?? 10;
$_ENV['GUI_REFRESH_DOWNLOADS'] = $_ENV['GUI_REFRESH_DOWNLOADS'] ?? 30;
$_ENV['GUI_REFRESH_UPLOADS'] = $_ENV['GUI_REFRESH_UPLOADS'] ?? 30;
$_ENV['GUI_REFRESH_SEARCH'] = $_ENV['GUI_REFRESH_SEARCH'] ?? 30;

$_ENV['ALLOWED_SERVERMSG_TAGS'] = $_ENV['ALLOWED_SERVERMSG_TAGS'] ?? '<a><b><i><u><br>';
$_ENV['GUI_SHOW_NEWS'] = $_ENV['GUI_SHOW_NEWS'] ?? 1;
$_ENV['GUI_SHOW_SHARE'] = $_ENV['GUI_SHOW_SHARE'] ?? 1;
$_ENV['TOP_SHOW_PERMALINK'] = $_ENV['TOP_SHOW_PERMALINK'] ?? 1;
$_ENV['REL_INFO'] = $_ENV['REL_INFO'] ?? base64_decode('aHR0cDovL3d3dy5hcHBsZS1kZWx1eGUuY2MvaW5kZXgucGhwP2N0PTQwMyZ2YT0lcw==');
$_ENV['GUI_STYLE'] = ($_ENV['GUI_STYLE'] ?? 'new') . '.php';

date_default_timezone_set($_ENV['TZ'] ?? 'Europe/Berlin');
