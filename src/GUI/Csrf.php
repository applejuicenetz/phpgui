<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

/** Schutz vor Cross-Site-Request-Forgery für zustandsändernde Aktionen. */
final class Csrf
{
    public static function token(): string
    {
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
    }

    public static function valid(?string $token = null): bool
    {
        $token ??= $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        return is_string($token) && $token !== '' && hash_equals(self::token(), $token);
    }
}
