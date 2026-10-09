<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $file = dirname(__DIR__) . '/src/' . str_replace(['appleJuiceNETZ\\', '\\'], ['', '/'], $class . '.php');
    if (is_file($file)) require_once $file;
});

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Search;
use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\appleJuice\Share;

function verify(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

$_SESSION = ['core_host' => $argv[1] ?? 'http://127.0.0.1:19861', 'core_pass' => md5('')];
$core = new Core();
verify(is_array($core->command('xml', 'information.xml')), 'XML command');
verify(isset($core->getcoreversion()['VERSION']), 'Core version');
$share = new Share();
$original = [];
foreach ($share->get_shared_dirs() as $id) $original[] = $share->get_shared_dir($id);
$path = '/mock/refactor Ä & test';
try {
    $share->add_share($path, true);
    $found = false;
    foreach ($share->get_shared_dirs() as $id) {
        $directory = $share->get_shared_dir($id);
        if ($directory['NAME'] === $path) {
            $found = true;
            verify($directory['SHAREMODE'] === 'subdirectory', 'Recursive share added');
        }
    }
    verify($found, 'Special-character share added');
    $share->changesub($path, false);
    foreach ($share->get_shared_dirs() as $id) {
        $directory = $share->get_shared_dir($id);
        if ($directory['NAME'] === $path) verify($directory['SHAREMODE'] === 'singledirectory', 'Share mode changed');
    }
    $share->del_share('/mock/nonexistent-refactor-share');
    verify(count($share->get_shared_dirs()) === count($original) + 1, 'Deleting absent path preserves shares');
} finally {
    $share->del_share($path);
}
$current = [];
foreach ($share->get_shared_dirs() as $id) $current[] = $share->get_shared_dir($id);
verify($current === $original, 'Existing share settings preserved');
verify($share->directory('/', true) === [] && $share->separator === '/', 'Separator lookup');
verify($share->directory('/') !== [], 'Directory listing');
verify($share->summary()['count'] > 0, 'Streamed share summary');
$page = $share->page('/mock/incoming', 1, 2);
verify(count($page['files']) <= 2, 'Share pagination');
$server = new Server(true);
$server->server_xml = ['SERVER' => [5 => [], -1 => [], 2 => []]];
verify($server->ids() === [2, 5], 'Disconnected sentinel excluded explicitly');
$_ENV['SERVERLIST_URL'] = 'data:text/plain,' . rawurlencode('No server links');
$server->getmore();
$_ENV['SERVERLIST_URL'] = 'data:text/plain,' . rawurlencode('ajfsp://server|refactor.example|2002/');
$server->getmore();
$servers = $core->command('xml', 'modified.xml?filter=server;informations');
verify(count(array_filter($servers['SERVER'] ?? [], static fn(array $entry): bool => ($entry['HOST'] ?? '') === 'refactor.example')) > 0, 'Server list import');
$search = new Search();
$search->start('refactor service probe');
$search->refresh_cache();
foreach ($search->cache['SEARCH'] ?? [] as $id => $entry) {
    if ($entry['SEARCHTEXT'] === 'refactor service probe') {
        $search->cancel($id);
        $search->delete($id);
        verify(!isset($search->cache['SEARCH'][$id]), 'Search canceled and deleted');
    }
}
echo "Core services: XML, version, share writes, streaming, directories, server IDs and search actions passed\n";
