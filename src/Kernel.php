<?php

declare(strict_types=1);

namespace appleJuiceNETZ;

use appleJuiceNETZ\GUI\Language;

class Kernel
{
    private static array $instances = [];

    public static function init(): void
    {
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ($_SERVER['HTTPS'] ?? 'off')) === 'https' || ($_SERVER['HTTPS'] ?? 'off') === 'on'),
        ]);
        session_start();

        if (version_compare(PHP_VERSION, '8.5', '<')) {
            throw new \RuntimeException('PHP 8.5 required; running ' . PHP_VERSION);
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

        $_ENV['CORE_HOST'] = $_ENV['CORE_HOST'] ?? '';
        $_ENV['CORE_PORT'] = $_ENV['CORE_PORT'] ?? 9851;
        $_ENV['GUI_LANGUAGE'] = in_array($_ENV['GUI_LANGUAGE'] ?? 'de', ['de', 'en'], true) ? ($_ENV['GUI_LANGUAGE'] ?? 'de') : 'de';
        $_ENV['TZ'] = $_ENV['TZ'] ?? 'Europe/Berlin';

        $_ENV['GUI_SHOW_NEWS'] = $_ENV['GUI_SHOW_NEWS'] ?? 1;

        $_ENV['GUI_SHOW_SHARE'] = $_ENV['GUI_SHOW_SHARE'] ?? 1;

        // Live refresh interval in seconds; invalid or out-of-range values fall back to 2.
        $refresh = filter_var($_ENV['GUI_REFRESH_INTERVAL'] ?? 2, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 3600]]);
        $_ENV['GUI_REFRESH_INTERVAL'] = $refresh === false ? 2 : $refresh;

        $_ENV['TOP_SHOW_PERMALINK'] = $_ENV['TOP_SHOW_PERMALINK'] ?? 1;

        date_default_timezone_set($_ENV['TZ'] ?? 'Europe/Berlin');

        ini_set('error_reporting', $_ENV['PHP_INI_ERROR_REPORTING'] ?? '1');
        ini_set('display_errors', $_ENV['PHP_INI_DISPLAY_ERRORS'] ?? 'On');
    }

    public static function getLanguage(): Language
    {
        return self::$instances[Language::class] ?? self::$instances[Language::class] = new Language(self::language());
    }

    /** Effective UI language: valid per-browser cookie, else the GUI_LANGUAGE default. */
    public static function language(): string
    {
        $cookie = $_COOKIE['aj_lang'] ?? '';
        return in_array($cookie, ['de', 'en'], true) ? $cookie : $_ENV['GUI_LANGUAGE'];
    }
}
