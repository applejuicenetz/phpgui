<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

/** Zuordnung von Core-Codes zu Grafiken unter public/assets/img. */
final class Icons
{
    private const OS = [
        '0' => ['os_unknow.svg', 'unbekannt'],
        '1' => ['os_windows.svg', 'Windows'],
        '2' => ['os_linux.svg', 'Linux'],
        '3' => ['os_mac.svg', 'macOS'],
    ];

    private const OS_NAMES = ['N-A' => '0', 'Windows' => '1', 'Linux' => '2', 'Mac' => '3'];

    private const DIRECT = [
        '0' => ['unknow.svg', '?'],
        '1' => ['direct.svg', 'direct'],
        '2' => ['indirect.svg', 'indirect'],
        '3' => ['indirect.svg', 'indirect'],
    ];

    /** @return array{src:string,alt:string} */
    public static function os(string|int $code): array
    {
        [$file, $alt] = self::OS[(string)$code] ?? self::OS['0'];

        return ['src' => 'assets/img/' . $file, 'alt' => $alt];
    }

    /** @return array{src:string,alt:string} */
    public static function osByName(string $name): array
    {
        return self::os(self::OS_NAMES[$name] ?? '0');
    }

    /** @return array{src:string,alt:string} */
    public static function directState(string|int $code): array
    {
        [$file, $alt] = self::DIRECT[(string)$code] ?? self::DIRECT['0'];

        return ['src' => 'assets/img/' . $file, 'alt' => $alt];
    }
}
