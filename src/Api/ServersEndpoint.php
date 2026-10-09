<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\GUI\Request;

final class ServersEndpoint extends Endpoint
{
    public function get(): array
    {
        $servers = new Server();
        $net = $servers->netstats;
        $minutes = is_numeric($net['timeconnected']) ? $net['timeconnected'] / 60 : 0;
        $now = (float)$servers->server_xml['TIME']['VALUES']['CDATA'];
        $rows = [];
        foreach ($servers->ids() as $id) {
            $s = $servers->serverinfo($id);
            $state = match (true) {
                (string)$net['connectedwith'] === (string)$s['ID'] => 'connected',
                (string)$net['trytoconnectto'] === (string)$s['ID'] => 'trying',
                (($now - (float)$s['LASTSEEN']) / 1000) <= 86400 && (float)$s['LASTSEEN'] > 0 => 'been',
                default => 'none',
            };
            $rows[] = [
                'id' => (int)$s['ID'], 'name' => (string)($s['NAME'] ?: $s['HOST']),
                'host' => (string)$s['HOST'], 'port' => (int)$s['PORT'], 'lastseen' => (int)(float)$s['LASTSEEN'],
                'tries' => (int)$s['CONNECTIONTRY'], 'state' => $state,
                'can_login' => (int)$net['connectedwith'] < 0 || $minutes >= 30,
            ];
        }
        return ['items' => $rows];
    }

    public function post(): array
    {
        $action = Request::str('action');
        $servers = new Server();
        if ($action === 'getservers') {
            $servers->getmore();
        } elseif (in_array($action, ['serverlogin', 'removeserver'], true)) {
            $id = Request::int('serv_id');
            if ($id <= 0) throw new ApiException(400, 'invalid_id');
            $servers->action($action, $id);
        } else throw new ApiException(400, 'unknown_action');
        return ['ok' => true];
    }
}
