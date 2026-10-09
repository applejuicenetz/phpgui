<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

/** Prüft (mit Session-Cache), ob eine neuere phpGUI-Version veröffentlicht ist. */
final class VersionCheck
{
    private const TTL = 21600;

    /** @return ?string neuere Version oder null */
    public static function newer(): ?string
    {
        $cache = $_SESSION['phpaj']['version_check'] ?? null;
        if (!is_array($cache) || ($cache['time'] ?? 0) < time() - self::TTL) {
            $cache = ['time' => time(), 'version' => self::fetch()];
            $_SESSION['phpaj']['version_check'] = $cache;
        }
        $latest = $cache['version'] ?? null;

        return $latest !== null && version_compare($latest, PHP_GUI_VERSION, '>') ? $latest : null;
    }

    private static function fetch(): ?string
    {
        $url = $_ENV['RELEASE_URL'] ?? '';
        if ($url === '') {
            return null;
        }
        // The GitHub API rejects requests without a User-Agent.
        $body = @file_get_contents($url, false, stream_context_create(['http' => [
            'timeout' => 3,
            'header' => "Accept: application/vnd.github+json\r\nUser-Agent: phpGUI/" . PHP_GUI_VERSION . "\r\n",
        ]]));
        if (!is_string($body)) {
            return null;
        }
        $release = json_decode($body, true);
        $tag = is_array($release) ? ($release['tag_name'] ?? null) : null;

        return is_string($tag) && preg_match('/^v?(\d+(?:\.\d+)+)$/', $tag, $m) === 1 ? $m[1] : null;
    }
}
