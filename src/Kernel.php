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

        if (version_compare(PHP_VERSION, '8.3', '<')) {
            throw new \RuntimeException('PHP 8.3 required; running ' . PHP_VERSION);
        }

        if (file_exists(GUI_ROOT . '/.env')) {
            $ini_array = parse_ini_file(GUI_ROOT . '/.env', true);
            if (is_array($ini_array)) $_ENV = array_merge($_ENV, $ini_array);

        }

        $_ENV['REAL_IP'] = 'http://' . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);

        $_ENV['REPOSITORY_URL'] = $_ENV['REPOSITORY_URL'] ?? 'https://github.com/applejuicenetz/phpgui';
        $_ENV['NEWS_URL'] = $_ENV['NEWS_URL'] ?? 'https://applejuicenetz.github.io/news/%s.html';
        $_ENV['FAQ_URL'] = $_ENV['FAQ_URL'] ?? 'https://applejuicenetz.github.io/faq/';
        $_ENV['RELEASE_URL'] = $_ENV['RELEASE_URL'] ?? 'https://api.github.com/repos/applejuicenetz/phpgui/releases/latest';
        $_ENV['SERVERLIST_URL'] = $_ENV['SERVERLIST_URL'] ?? 'https://www.applejuicenet.cc/serverlist/xmllist.php';

        $_ENV['ALLOWED_SERVERMSG_TAGS'] = $_ENV['ALLOWED_SERVERMSG_TAGS'] ?? '<a><b><i><u><br>';

        $_ENV['REL_INFO'] = $_ENV['REL_INFO'] ?? base64_decode('aHR0cHM6Ly93d3cuYXBwbGUtZGVsdXhlLmNvL2luZGV4LnBocD9jdD00MDMmdmE9JXM=');

        $_ENV['CORE_HOST'] = $_ENV['CORE_HOST'] ?? '';
        $_ENV['CORE_PORT'] = $_ENV['CORE_PORT'] ?? 9851;
        $_ENV['GUI_LANGUAGE'] = in_array($_ENV['GUI_LANGUAGE'] ?? 'de', ['de', 'en'], true) ? ($_ENV['GUI_LANGUAGE'] ?? 'de') : 'de';
        $_ENV['TZ'] = trim((string)($_ENV['TZ'] ?? '')) ?: 'Europe/Berlin';

        $_ENV['GUI_SHOW_NEWS'] = $_ENV['GUI_SHOW_NEWS'] ?? 1;

        $_ENV['GUI_SHOW_SHARE'] = $_ENV['GUI_SHOW_SHARE'] ?? 1;

        // Live refresh interval in seconds; invalid or out-of-range values fall back to 2.
        $refresh = filter_var($_ENV['GUI_REFRESH_INTERVAL'] ?? 2, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 3600]]);
        $_ENV['GUI_REFRESH_INTERVAL'] = $refresh === false ? 2 : $refresh;

        $_ENV['TOP_SHOW_PERMALINK'] = $_ENV['TOP_SHOW_PERMALINK'] ?? 1;

        $_ENV = self::validateEnvironment($_ENV);
        date_default_timezone_set($_ENV['TZ']);


        ini_set('error_reporting', $_ENV['PHP_INI_ERROR_REPORTING'] ?? '1');
        ini_set('display_errors', $_ENV['PHP_INI_DISPLAY_ERRORS'] ?? 'On');
    }

    /** Normalize configuration without network access; invalid optional URLs disable their feature. */
    public static function validateEnvironment(array $env): array
    {
        $text = static fn(mixed $value): string => is_scalar($value) ? trim((string)$value) : '';
        $url = static function (mixed $value, bool $template = false) use ($text): string {
            $value = $text($value);
            if ($value === '') return '';
            // Only a single %s placeholder is supported; reject other sprintf directives.
            if ($template && (substr_count($value, '%s') > 1 || preg_match('/%(?!s|[0-9a-fA-F]{2})/', $value))) return '';
            $probe = $template ? str_replace('%s', 'test', $value) : $value;
            $parts = parse_url($probe);
            if (!filter_var($probe, FILTER_VALIDATE_URL) || !is_array($parts)
                || !in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
                || isset($parts['user']) || isset($parts['pass'])) return '';
            return $value;
        };
        foreach (['REPOSITORY_URL', 'NEWS_URL', 'FAQ_URL', 'RELEASE_URL', 'SERVERLIST_URL', 'REL_INFO', 'CORE_HOST'] as $key) {
            $env[$key] = $url($env[$key] ?? '', in_array($key, ['NEWS_URL', 'REL_INFO'], true));
        }
        foreach (['CORE_PORT' => [9851, 1, 65535], 'GUI_REFRESH_INTERVAL' => [2, 1, 3600], 'PHP_INI_ERROR_REPORTING' => [1, 0, E_ALL]] as $key => [$default, $min, $max]) {
            $value = filter_var($text($env[$key] ?? ''), FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]);
            $env[$key] = $value === false ? $default : $value;
        }
        foreach (['GUI_SHOW_NEWS', 'GUI_SHOW_SHARE', 'TOP_SHOW_PERMALINK'] as $key) {
            $value = filter_var($text($env[$key] ?? '1'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            $env[$key] = $value ?? true;
        }
        $env['GUI_REMEMBER_LOGIN'] = filter_var($text($env['GUI_REMEMBER_LOGIN'] ?? '0'), FILTER_VALIDATE_BOOLEAN);
        $display = filter_var($text($env['PHP_INI_DISPLAY_ERRORS'] ?? 'On'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $env['PHP_INI_DISPLAY_ERRORS'] = ($display ?? true) ? 'On' : 'Off';
        $env['PHP_INI_ERROR_REPORTING'] = (string)$env['PHP_INI_ERROR_REPORTING'];
        $env['GUI_LANGUAGE'] = in_array($env['GUI_LANGUAGE'] ?? '', ['de', 'en'], true) ? $env['GUI_LANGUAGE'] : 'de';
        $zone = $text($env['TZ'] ?? '');
        try {
            new \DateTimeZone($zone);
            $env['TZ'] = $zone;
        } catch (\Exception) {
            $env['TZ'] = 'Europe/Berlin';
        }
        // Limit configurable markup to tags understood by the existing sanitizer.
        $tags = $text($env['ALLOWED_SERVERMSG_TAGS'] ?? '<a><b><i><u><br>');
        preg_match_all('/<(a|b|i|u|br)>/i', $tags, $matches);
        $env['ALLOWED_SERVERMSG_TAGS'] = implode('', array_unique(array_map('strtolower', $matches[0])));
        return $env;
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
