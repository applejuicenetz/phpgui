<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

/** Verifies Core credentials; shared by the JSON login and the browser-extension endpoint. */
final class CoreLogin
{
    public static function validHost(string $host): bool
    {
        $scheme = parse_url($host, PHP_URL_SCHEME);

        return in_array($scheme, ['http', 'https'], true) && filter_var($host, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * @param string $secret plain password or a 32 character MD5 hash
     * @return string MD5 hash of the password
     * @throws ApiException invalid_host, core_unavailable or wrong_password
     */
    public static function verify(string $host, string $secret): string
    {
        if (!self::validHost($host)) {
            throw new ApiException(400, 'invalid_host');
        }
        $hash = strlen($secret) === 32 ? $secret : md5($secret);
        $url = rtrim($host, '/') . '/xml/settings.xml?' . http_build_query(['password' => $hash]);
        $body = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 8, 'ignore_errors' => true]]));
        if (empty($body)) {
            throw new ApiException(503, 'core_unavailable');
        }
        if (str_contains($body, 'wrong password.')) {
            throw new ApiException(401, 'wrong_password');
        }

        // Reject HTML/error pages instead of treating any non-empty reply as a valid Core.
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $body);
        rewind($stream);
        try {
            $settings = (new \appleJuiceNETZ\appleJuice\XmlParser())->parse($stream);
            if (!isset($settings['NICK']['VALUES']['CDATA'])) throw new ApiException(503, 'core_unavailable');
        } catch (\UnexpectedValueException) {
            throw new ApiException(503, 'core_unavailable');
        } finally {
            fclose($stream);
        }
        return $hash;
    }

    /** Binds a verified Core to the session. */
    public static function bind(string $host, string $hash): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }
        // Never reuse incremental data from a different Core or different credentials.
        if (($_SESSION['core_host'] ?? '') !== rtrim($host, '/') || ($_SESSION['core_pass'] ?? '') !== $hash) {
            unset($_SESSION['cache'], $_SESSION['phpaj'], $_SESSION['SEPARATOR']);
        }
        $_SESSION['core_host'] = rtrim($host, '/');
        $_SESSION['core_pass'] = $hash;
    }
}
