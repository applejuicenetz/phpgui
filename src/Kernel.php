<?php

declare(strict_types=1);

namespace appleJuiceNETZ;

use appleJuiceNETZ\GUI\Language;

class Kernel
{
    private static array $instances = [];

    public static function init(): void
    {
        session_start();

        if (!version_compare(PHP_VERSION, '8.2')) {
            die('PHP 8.2 required, used: -> ' . PHP_VERSION);
        }

        if (file_exists(GUI_ROOT . '/.env')) {
            $ini_array = parse_ini_file(GUI_ROOT . '/.env', true);
            $_ENV = array_merge($_ENV, $ini_array);
        }

        $_ENV['REAL_IP'] = 'http://' . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);

        $_ENV['NEWS_URL'] = $_ENV['NEWS_URL'] ?? 'https://applejuicenetz.github.io/news/%s.html';

        $_ENV['FAQ_URL'] = $_ENV['FAQ_URL'] ?? 'https://applejuicenetz.github.io/faq/';

        $_ENV['CHANGELOG_URL'] = $_ENV['CHANGELOG_URL'] ?? 'https://raw.githubusercontent.com/applejuicenetz/phpgui/beta/CHANGELOG.md';

        $_ENV['SERVERLIST_URL'] = $_ENV['SERVERLIST_URL'] ?? 'http://www.applejuicenet.cc/serverlist/xmllist.php';

        $_ENV['ALLOWED_SERVERMSG_TAGS'] = $_ENV['ALLOWED_SERVERMSG_TAGS'] ?? '<a><b><i><u><br>';

        $_ENV['REL_INFO'] = $_ENV['REL_INFO'] ?? base64_decode('aHR0cHM6Ly93d3cuYXBwbGUtZGVsdXhlLmNvL2luZGV4LnBocD9jdD00MDMmdmE9JXM=');

        $_ENV['GUI_SHOW_NEWS'] = $_ENV['GUI_SHOW_NEWS'] ?? 1;

        $_ENV['GUI_SHOW_SHARE'] = $_ENV['GUI_SHOW_SHARE'] ?? 1;

        date_default_timezone_set($_ENV['TZ'] ?? 'Europe/Berlin');

        ini_set('error_reporting', $_ENV['PHP_INI_ERROR_REPORTING'] ?? '1');
        ini_set('display_errors', $_ENV['PHP_INI_DISPLAY_ERRORS'] ?? 'On');
    }

    public static function getLanguage(): Language
    {
        return self::$instances[Language::class] ?? self::$instances[Language::class] = new Language($_ENV['GUI_LANGUAGE']);
    }
}
