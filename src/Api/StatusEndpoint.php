<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\GUI\CoreSettings;

final class StatusEndpoint extends Endpoint
{
    public function get(): array
    {
        $server = new Server();
        $info = $server->info();
        $settings = CoreSettings::read($server->core);
        return CoreData::activeCounts() + [
            'nick' => (string)($settings['nick'] ?? ''),
            'credits' => (float)$info['CREDITS'],
            'download_speed' => (int)$info['DOWNLOADSPEED'],
            'upload_speed' => (int)$info['UPLOADSPEED'],
            'firewalled' => $server->netstats['firewalled'] === 'true',
            'connecting' => (int)$server->netstats['connectedwith'] < 0,
        ];
    }
}
