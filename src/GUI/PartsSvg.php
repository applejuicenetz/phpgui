<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

/** Render Core byte ranges as a responsive SVG, without GD or raster buffers. */
final class PartsSvg
{
    private const WIDTH = 500;
    private const ROWS = 14;
    private const ROW_HEIGHT = 14;
    private const BLOCK = 1048576;

    public static function render(float $size, array $parts, bool $download = false, array $transfers = []): string
    {
        if ($size <= 0 || !is_finite($size)) throw new \InvalidArgumentException('Invalid part-map size');
        ksort($parts, SORT_NUMERIC);
        $starts = array_keys($parts);
        $ranges = [];
        foreach ($starts as $index => $start) {
            $end = (float)($starts[$index + 1] ?? $size);
            $type = (int)$parts[$start]['TYPE'];
            $color = $type === -1 ? '#000000' : ($type === 0 ? '#ff0000' : sprintf('#%02x%02xff', 250 - 25 * min(10, max(1, $type)), 250 - 25 * min(10, max(1, $type))));
            $ranges[] = [(float)$start, $end, $color];
        }
        // Contiguous downloaded ranges contain checked whole MiB blocks.
        if ($download) {
            $receivedStart = null;
            foreach ($starts as $index => $start) {
                if ((int)$parts[$start]['TYPE'] === -1) {
                    $receivedStart ??= (float)$start;
                } elseif ($receivedStart !== null) {
                    self::checkedRange($ranges, $receivedStart, (float)$start, $size);
                    $receivedStart = null;
                }
            }
            if ($receivedStart !== null) self::checkedRange($ranges, $receivedStart, $size, $size);
        }
        foreach ($transfers as $transfer) {
            $start = (float)$transfer['DOWNLOADFROM'];
            $end = (float)$transfer['DOWNLOADTO'];
            if ($start < 0 || $end <= $start) continue;
            $percent = max(0, min(10, (int)floor(((float)$transfer['ACTUALDOWNLOADPOSITION'] - $start) / ($end - $start) * 10)));
            $shade = 255 - 12 * $percent;
            $ranges[] = [$start, $end, sprintf('#%02x%02x00', $shade, $shade)];
        }
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 210" width="500" height="210">';
        $svg .= '<rect width="500" height="210" fill="#c8c8c8"/>';
        foreach ($ranges as [$start, $end, $color]) {
            $start = max(0, min($size, $start));
            $end = max($start, min($size, $end));
            for ($row = 0; $row < self::ROWS; $row++) {
                $rowStart = $size * $row / self::ROWS;
                $rowEnd = $size * ($row + 1) / self::ROWS;
                $left = max($start, $rowStart);
                $right = min($end, $rowEnd);
                if ($right <= $left) continue;
                $x = ($left - $rowStart) / ($rowEnd - $rowStart) * self::WIDTH;
                $width = ($right - $left) / ($rowEnd - $rowStart) * self::WIDTH;
                $svg .= sprintf('<rect x="%.4F" y="%d" width="%.4F" height="14" fill="%s"/>', $x, $row * (self::ROW_HEIGHT + 1), $width, $color);
            }
        }
        return $svg . '</svg>';
    }

    private static function checkedRange(array &$ranges, float $start, float $end, float $size): void
    {
        $checkedStart = ceil($start / self::BLOCK) * self::BLOCK;
        $checkedEnd = $end >= $size ? $size : floor($end / self::BLOCK) * self::BLOCK;
        // A complete small file includes its final partial block.
        if ($start === 0.0 && $end >= $size) $checkedStart = 0;
        if ($checkedEnd > $checkedStart) $ranges[] = [$checkedStart, $checkedEnd, '#00ff00'];
    }
}
