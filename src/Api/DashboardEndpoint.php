<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\CoreSettings;

final class DashboardEndpoint extends Endpoint
{
    public function get(): array
    {
        $servers = new Server();
        $info = $servers->info();
        $net = $servers->netstats;
        $version = $servers->core->getcoreversion();
        $network = $servers->server_xml['NETWORKINFO'] ?? [];
        $ip = $network[array_key_first($network)]['IP'] ?? '';
        $downloads = CoreData::downloads();
        return [
            'server_name' => $net['servername'],
            'welcome' => $net['welcome'],
            'downloads_active' => count(array_filter($downloads, static fn(array $d): bool => $d['status'] === 'loading')),
            'downloads_total' => count(array_filter($downloads, static fn(array $d): bool => $d['status'] !== 'done')),
            'uploads_active' => CoreData::activeCounts()['uploads_active'],
            'credits' => (float)$info['CREDITS'],
            'share' => !empty($_ENV['GUI_SHOW_SHARE']) ? (new Share())->summary() : null,
            'time' => (int)(float)$servers->server_xml['TIME']['VALUES']['CDATA'],
            'core_version' => $version['VERSION'],
            'core_os' => $version['SYSTEM'],
            'core_os_code' => CoreData::osCode($version['SYSTEM']),
            'connected_since' => is_numeric($net['timeconnected']) ? (int)$net['timeconnected'] : null,
            'connections' => (int)($info['OPENCONNECTIONS'] ?? 0),
            'max_connections' => (int)(CoreSettings::read($servers->core)['maxconnections'] ?? 0),
            'session_dl' => (int)$info['SESSIONDOWNLOAD'],
            'session_ul' => (int)$info['SESSIONUPLOAD'],
            'dl_speed' => (int)$info['DOWNLOADSPEED'],
            'ul_speed' => (int)$info['UPLOADSPEED'],
            'public_ip' => (string)$ip,
            'users' => (int)$net['users'],
            'filecount' => (int)$net['filecount'],
            'filesize' => (int)$net['filesize'],
        ];
    }
}
