<?php

namespace appleJuiceNETZ\GUI;

/** Shared helpers still used by the Core data classes. */
class subs
{
    /** Sort source keys by a field in each nested record. */
    public static function ajsort($source, $field, $type, $reverse)
    {
        $values = [];
        foreach (array_keys($source) as $key) {
            $values[$key] = $source[$key][$field];
        }
        if (empty($reverse)) {
            asort($values, $type);
        } else {
            arsort($values, $type);
        }
        return $values;
    }

    /** Format a byte count using binary unit steps. */
    public static function sizeformat($bytes, $precision = 2, $stripTrailing = false)
    {
        $index = 0;
        while (abs($bytes) >= 1024 && $index < 6) {
            $bytes /= 1024;
            $index++;
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];
        if ($index > 0) {
            $value = number_format($bytes, $precision);
            if ($stripTrailing) {
                $value = rtrim(rtrim($value, '0'), '.');
                if (strpos($value, '.') === false) $value .= '.0';
            }
        } else {
            $value = (int)$bytes;
        }
        return "$value $units[$index]";
    }

    /** Extract a split archive part number from its filename. */
    public static function parts($filename)
    {
        if (preg_match('/\.part(\d+)\./i', $filename, $matches)
            || preg_match('/\.(\d+)$/i', $filename, $matches)) {
            return ' | Part: ' . (int)$matches[1];
        }
        return null;
    }
}
