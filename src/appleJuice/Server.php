<?php

declare(strict_types=1);

namespace appleJuiceNETZ\appleJuice;

use appleJuiceNETZ\GUI\Html;
use appleJuiceNETZ\Kernel;

class Server
{
    public array $server_xml = [];
    public array $netstats = [];
    public Core $core;

    public function __construct(int|bool $noload = false)
    {
        $this->core = new Core();
        if ($noload) return;
        $this->server_xml = $this->core->command('xml', 'modified.xml?filter=server;informations');
        // Core uses -1 to identify a disconnected client.
        $this->server_xml['SERVER'][-1]['NAME'] = html_entity_decode(Kernel::getLanguage()->translate()->Server->no_server, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $this->netstats = $this->netstats();
    }

    public function time(): string
    {
        return date('j.n.y - H:i:s', (int)((float)($this->server_xml['TIME']['VALUES']['CDATA'] ?? 0) / 1000));
    }

    public function netstats(): array
    {
        $network = $this->server_xml['NETWORKINFO'] ?? [];
        $info = $network[array_key_first($network)] ?? [];
        $server = $this->server_xml['SERVER'][$info['CONNECTEDWITHSERVERID'] ?? -1] ?? [];
        $connectedSince = (float)($info['CONNECTEDSINCE'] ?? 0);
        return [
            'servername' => !empty($server['NAME']) ? $server['NAME'] : ($server['HOST'] ?? ''),
            'timeconnected' => $connectedSince != 0
                ? ((float)$this->server_xml['TIME']['VALUES']['CDATA'] - $connectedSince) / 1000 : '?',
            'firewalled' => $info['FIREWALLED'] ?? 'false',
            'servercount' => count($this->ids()),
            'users' => $info['USERS'] ?? '0',
            'filecount' => $info['FILES'] ?? '0',
            'filesize' => (float)($info['FILESIZE'] ?? 0) * 1024 * 1024,
            'connectedwith' => $info['CONNECTEDWITHSERVERID'] ?? '-1',
            'trytoconnectto' => $info['TRYCONNECTTOSERVER'] ?? '-1',
            'welcome' => Html::sanitize(trim($network['WELCOMEMESSAGE']['VALUES']['CDATA'] ?? ''), $_ENV['ALLOWED_SERVERMSG_TAGS']),
        ];
    }

    public function ids(): array
    {
        $ids = array_keys($this->server_xml['SERVER'] ?? []);
        $ids = array_filter($ids, static fn(int|string $id): bool => (string)$id !== '-1');
        asort($ids);
        return array_values($ids);
    }

    public function serverinfo(int|string $id): array
    {
        return $this->server_xml['SERVER'][$id];
    }

    public function getmore(): void
    {
        $list = @file_get_contents($_ENV['SERVERLIST_URL'], false,
            stream_context_create(['http' => ['timeout' => 15]]));
        if ($list === false || $list === '') return;
        preg_match_all('~ajfsp://server\|[^\r\n]+?/~', $list, $matches);
        $links = array_values(array_unique($matches[0]));
        shuffle($links);
        foreach (array_slice($links, 0, 10) as $link) {
            $this->core->command('function', 'processlink?link=' . rawurlencode($link));
        }
    }

    public function info(): array
    {
        $information = $this->server_xml['INFORMATION'] ?? [];
        return $information[array_key_first($information)] ?? [];
    }

    public function action(string $action, int|string $id): string
    {
        return $action . ' &rArr; ' . $this->core->command('function', $action . '?id=' . rawurlencode((string)$id));
    }
}
