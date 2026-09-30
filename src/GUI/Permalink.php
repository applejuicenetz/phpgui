<?php

namespace appleJuiceNETZ\GUI;

class Permalink
{
    public static function create(string $host, string $passwordHash): string
    {
        return 'index.php?l=' . rawurlencode(base64_encode($host . '|' . $passwordHash));
    }

    public static function parse(string $value): ?array
    {
        // Older links did not URL-encode base64, so PHP turns their + into spaces.
        $decoded = base64_decode(str_replace(' ', '+', trim($value)), true);
        if ($decoded === false) {
            return null;
        }

        $credentials = explode('|', $decoded, 2);
        if (count($credentials) !== 2 || $credentials[0] === '' || $credentials[1] === '') {
            return null;
        }

        $scheme = parse_url($credentials[0], PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'], true) || !filter_var($credentials[0], FILTER_VALIDATE_URL)) {
            return null;
        }

        return $credentials;
    }
}
