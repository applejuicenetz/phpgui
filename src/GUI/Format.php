<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

use appleJuiceNETZ\Kernel;

/** Reine Formatierungs- und Zuordnungsfunktionen für Anzeigewerte (kein HTML). */
final class Format
{
    public static function lang(): object
    {
        return Kernel::getLanguage()->translate();
    }

    public static function bytes(int|float|string $bytes, int $precision = 2, bool $stripTrailing = false): string
    {
        return subs::sizeformat((float)$bytes, $precision, $stripTrailing);
    }

    public static function speed(int|float|string $bytesPerSecond): string
    {
        return self::bytes($bytesPerSecond) . '/s';
    }

    public static function eta(float $restBytes, float $speed): string
    {
        if ($speed <= 0) {
            return '';
        }
        $seconds = (int)($restBytes / $speed);
        $hours = $seconds / 3600;

        return $hours < 24
            ? sprintf('%02d:%02d:%02d', (int)$hours, intdiv($seconds % 3600, 60), $seconds % 60)
            : sprintf('%.1fd', $hours / 24);
    }

    /** Anzeige-PDL aus dem Core-Wert (Core: 0 = 1.0). */
    public static function pdl(int|string $coreValue): string
    {
        return (string)(((int)$coreValue + 10) / 10);
    }

    /**
     * Status eines Downloads als [css-Klasse, Schlüssel, Text].
     * Schlüssel sind stabil und werden von CSS und JS genutzt.
     */
    public static function downloadStatus(string $status): array
    {
        $t = self::lang()->Downloads->state;

        return match ($status) {
            '0_1' => ['searching', $t->searching],
            '0_2' => ['loading', $t->loading],
            '14' => ['done', $t->done],
            '18' => ['paused', $t->paused],
            '17' => ['canceled', $t->canceled],
            default => ['unknown', $t->unknown],
        };
    }

    /** Status eines Uploads als [Schlüssel, Text]. */
    public static function uploadStatus(string|int $status): array
    {
        $t = self::lang()->Uploads->ul_status;
        $n = (int)$status;
        $text = match ($n) {
            1 => $t->status_1,
            2 => $t->status_2,
            5 => $t->status_5,
            6 => $t->status_6,
            7 => $t->status_7,
            default => (string)$status,
        };
        $key = match ($n) {
            1 => 'active',
            2 => 'queue',
            5, 6 => 'connecting',
            7 => 'failed',
            default => 'unknown',
        };

        return [$key, $text];
    }

    /** Anzeigename der Quelle eines Downloads (Core-Statuscode 1..16). */
    public static function sourceStatus(string|int $status): string
    {
        $t = self::lang()->Downloads->dl_status;
        $key = 'status_' . (int)$status;

        return $t->$key ?? (string)$status;
    }

    public static function sourceOrigin(string|int $origin): string
    {
        $t = self::lang()->Downloads->dl_source;
        $key = 'src_' . (int)$origin;

        return $t->$key ?? (string)$origin;
    }
}
