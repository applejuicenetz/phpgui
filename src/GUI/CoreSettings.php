<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

use appleJuiceNETZ\appleJuice\Core;

/** Shared Core settings access for pages and live requests. */
final class CoreSettings
{
    public static function read(Core $core): array
    {
        $xml = $core->command('xml', 'settings.xml');
        $flat = [];
        foreach ($xml as $key => $value) {
            if (isset($value['VALUES']['CDATA'])) {
                $flat[strtolower((string)$key)] = $value['VALUES']['CDATA'];
            }
        }

        return $flat + ['share' => $xml['SHARE']['VALUES']['DIRECTORY'] ?? []];
    }

    public static function saveConnection(Core $core, array $values): void
    {
        $core->command('function', 'setsettings?' . http_build_query($values));
    }

    public static function setLimit(Core $core, string $key, int $value): void
    {
        $settings = self::read($core);
        $values = [];
        foreach (['MaxDownload', 'MaxUpload', 'MaxConnections', 'Speedperslot', 'MaxNewConnectionsPerTurn', 'MaxSourcesPerFile', 'AutoConnect'] as $field) {
            $values[$field] = $settings[strtolower($field)];
        }
        $values[$key] = max(0, $value);
        self::saveConnection($core, $values);
    }
}
