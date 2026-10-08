<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Request;

final class ServerController extends Controller
{
    private const ACTIONS = ['serverlogin', 'removeserver'];

    public function handle(): Page
    {
        $servers = $this->server();
        $action = Request::str('action');
        if ($action !== '' && Request::isPost() && Csrf::valid()) {
            $id = Request::int('serv_id');
            if (in_array($action, self::ACTIONS, true) && $id > 0) {
                $servers->action($action, (string)$id);
            }
            if ($action === 'getservers') {
                $servers->getmore();
            }
            $this->redirect('index.php?site=server');
        }

        $net = $servers->netstats;
        $timeDiffMin = is_numeric($net['timeconnected']) ? $net['timeconnected'] / 60 : 0;
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
                'id' => (int)$s['ID'],
                'name' => (string)($s['NAME'] ?: 'N/A'),
                'host' => (string)$s['HOST'],
                'port' => (string)$s['PORT'],
                'lastseen' => (float)$s['LASTSEEN'] > 0 ? date('d.m.y - H:i:s', (int)((float)$s['LASTSEEN'] / 1000)) : $this->lang->Server->not_yet,
                'tries' => (string)$s['CONNECTIONTRY'],
                'state' => $state,
                'state_text' => match ($state) {
                    'connected' => $this->lang->Server->connectet,
                    'trying' => $this->lang->Server->try_connect,
                    'been' => $this->lang->Server->been_connected,
                    default => $this->lang->Server->no_con,
                },
                'can_login' => (int)$net['connectedwith'] < 0 || $timeDiffMin >= 30,
            ];
        }

        return new Page('pages/server', ['rows' => $rows]);
    }
}
