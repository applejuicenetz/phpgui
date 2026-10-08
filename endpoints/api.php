<?php

declare(strict_types=1);

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Format;
use appleJuiceNETZ\GUI\Request;
use appleJuiceNETZ\GUI\ViewData;

/**
 * JSON-API für Live-Updates und Geschwindigkeitslimits.
 *
 * GET  ?site=api&type=header,downloads,uploads,dashboard,search  => Datenblöcke
 * POST ?site=api&action=set_maxdl|set_maxul  (value=<bytes/s>, _csrf)  => {"ok":true}
 */

header('Content-Type: application/json');
header('Cache-Control: no-store');

if (empty($_SESSION['core_host'])) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$core = new Core();

if (Request::get('action') !== '') {
    if (!Request::isPost() || !Csrf::valid()) {
        http_response_code(403);
        echo json_encode(['error' => 'csrf']);
        exit;
    }
    $key = match (Request::get('action')) {
        'set_maxdl' => 'MaxDownload',
        'set_maxul' => 'MaxUpload',
        default => null,
    };
    if ($key === null) {
        http_response_code(400);
        echo json_encode(['error' => 'unknown_action']);
        exit;
    }
    $cur = $core->command('xml', 'settings.xml');
    $v = static fn(string $k) => $cur[$k]['VALUES']['CDATA'];
    $values = [
        'MaxDownload' => $v('MAXDOWNLOAD'),
        'MaxUpload' => $v('MAXUPLOAD'),
        'MaxConnections' => $v('MAXCONNECTIONS'),
        'Speedperslot' => $v('SPEEDPERSLOT'),
        'MaxNewConnectionsPerTurn' => $v('MAXNEWCONNECTIONSPERTURN'),
        'MaxSourcesPerFile' => $v('MAXSOURCESPERFILE'),
        'AutoConnect' => $v('AUTOCONNECT'),
    ];
    $values[$key] = max(0, Request::int('value'));
    $core->command('function', 'setsettings?' . http_build_query($values));
    echo json_encode(['ok' => true]);
    exit;
}

$info = static function () use ($core): array {
    static $cache = null;
    if ($cache === null) {
        $xml = $core->command('xml', 'modified.xml?filter=informations');
        $keys = array_keys($xml['INFORMATION']);
        $cache = $xml['INFORMATION'][$keys[0]];
    }

    return $cache;
};
$maxOf = static function (string $field) use ($core): int {
    $s = $core->command('xml', 'settings.xml');

    return (int)$s[$field]['VALUES']['CDATA'];
};

$result = [];
foreach (explode(',', Request::get('type', 'header')) as $type) {
    switch (trim($type)) {
        case 'header':
            $i = $info();
            $result['header'] = [
                'credits' => Format::bytes($i['CREDITS']),
                'credits_negative' => (float)$i['CREDITS'] < 0,
            ];
            break;

        case 'downloads':
            $rows = ViewData::downloads('name');
            $items = [];
            foreach ($rows as $r) {
                $items[$r['id']] = $r;
            }
            $max = $maxOf('MAXDOWNLOAD');
            $result['downloads'] = [
                'items' => $items,
                'speed_raw' => array_sum(array_column($rows, 'speed_raw')),
                'max_raw' => $max,
                'max_text' => $max > 0 ? Format::bytes($max, 2, true) : '',
                'counts' => ViewData::downloadCounts($rows),
            ];
            break;

        case 'uploads':
            $rows = ViewData::uploads('name');
            $items = [];
            foreach ($rows as $r) {
                $items[$r['id']] = $r;
            }
            $max = $maxOf('MAXUPLOAD');
            $result['uploads'] = [
                'items' => $items,
                'count' => count($rows),
                'speed_raw' => array_sum(array_column($rows, 'speed_raw')),
                'max_raw' => $max,
                'max_text' => $max > 0 ? Format::bytes($max, 2, true) : '',
            ];
            break;

        case 'dashboard':
            $i = $info();
            $servers = new \appleJuiceNETZ\appleJuice\Server();
            $uploads = new \appleJuiceNETZ\appleJuice\Uploads();
            $uploads->refresh_cache();
            $conn = $servers->netstats['timeconnected'];
            $result['dashboard'] = [
                'downloads' => ViewData::downloadCounts(ViewData::downloads()),
                'uploads' => (int)($uploads->cache['phpaj_ul'] ?? 0),
                'credits' => Format::bytes($i['CREDITS']),
                'credits_negative' => (float)$i['CREDITS'] < 0,
                'dl_speed' => Format::speed($i['DOWNLOADSPEED']),
                'ul_speed' => Format::speed($i['UPLOADSPEED']),
                'session_dl' => Format::bytes($i['SESSIONDOWNLOAD']),
                'session_ul' => Format::bytes($i['SESSIONUPLOAD']),
                'connections' => $i['OPENCONNECTIONS'] ?? '',
                'connected' => is_numeric($conn) ? sprintf('%dh %dmin', intdiv((int)$conn, 3600), intdiv((int)$conn % 3600, 60)) : '?',
            ];
            break;

        case 'search':
            $data = ViewData::search();
            $result['search'] = [
                'total' => $data['total'],
                'searches' => $data['searches'],
                'entries' => $data['entries'],
                'running' => (bool)array_filter($data['searches'], static fn($s) => $s['running']),
            ];
            break;
    }
}

echo json_encode($result);
