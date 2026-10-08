<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

/** Typisierter Zugriff auf Request-Parameter (GET/POST). */
final class Request
{
    public static function str(string $key, string $default = ''): string
    {
        $v = $_POST[$key] ?? $_GET[$key] ?? null;

        return is_string($v) ? $v : $default;
    }

    public static function get(string $key, string $default = ''): string
    {
        $v = $_GET[$key] ?? null;

        return is_string($v) ? $v : $default;
    }

    public static function int(string $key, int $default = 0): int
    {
        $v = $_POST[$key] ?? $_GET[$key] ?? null;

        return is_numeric($v) ? (int)$v : $default;
    }

    /** @return list<string> */
    public static function list(string $key): array
    {
        $v = $_POST[$key] ?? $_GET[$key] ?? [];
        if (is_string($v) && $v !== '') {
            return [$v];
        }

        return is_array($v) ? array_values(array_filter($v, 'is_string')) : [];
    }

    public static function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /** Wahrer Wert, wenn der Request per XHR kommt und JSON erwartet. */
    public static function wantsJson(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }
}
