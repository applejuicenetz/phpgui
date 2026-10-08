<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

/** Einmalige Hinweise (Erfolg, Warnung, Fehler) über Redirects hinweg. */
final class Flash
{
    public const LEVELS = ['success', 'info', 'warning', 'danger'];

    public static function add(string $level, string $text, ?string $title = null): void
    {
        if (!in_array($level, self::LEVELS, true)) {
            $level = 'info';
        }
        $_SESSION['flash'][] = ['level' => $level, 'title' => $title, 'text' => $text];
    }

    /** @return list<array{level:string,title:?string,text:string}> */
    public static function pull(): array
    {
        $items = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        return $items;
    }
}
