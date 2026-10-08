<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

/** Loads and caches the dashboard news without holding the session lock during network access. */
final class NewsFeed
{
    private const CACHE_SECONDS = 3600;

    public static function load(string $version): string
    {
        $cache = $_SESSION['phpaj']['news'] ?? null;
        if (is_array($cache) && ($cache['version'] ?? '') === $version && ($cache['time'] ?? 0) > time() - self::CACHE_SECONDS) {
            return $cache['html'];
        }

        // Release the session lock so parallel polling requests are not blocked by a slow news server.
        $wasActive = session_status() === PHP_SESSION_ACTIVE;
        if ($wasActive) {
            session_write_close();
        }
        try {
            $html = self::fetch($version);
        } finally {
            if ($wasActive) {
                session_start(['use_cookies' => false, 'cache_limiter' => '']);
            }
        }
        $_SESSION['phpaj']['news'] = ['version' => $version, 'time' => time(), 'html' => $html];

        return $html;
    }

    private static function fetch(string $version): string
    {
        $body = @file_get_contents(
            sprintf($_ENV['NEWS_URL'], $version ?: '404'),
            false,
            stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]])
        );
        if (!is_string($body)) {
            return '';
        }
        $body = Html::toUtf8($body);
        if (preg_match('~<body\b[^>]*>(.*?)</body\s*>~is', $body, $matches) === 1) {
            $body = $matches[1];
        }

        return Html::sanitize($body);
    }
}
