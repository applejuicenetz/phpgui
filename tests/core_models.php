<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $file = dirname(__DIR__) . '/src/' . str_replace(['appleJuiceNETZ\\', '\\'], ['', '/'], $class . '.php');
    if (is_file($file)) require_once $file;
});

use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Search;
use appleJuiceNETZ\appleJuice\Uploads;

function expect(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

$_SESSION = [];
$downloads = new Downloads();
$downloads->cache = [
    'IDS' => ['VALUES' => ['DOWNLOADID' => [1 => true]], 'DOWNLOADID' => [1 => ['USERID' => [2 => true]]]],
    'DOWNLOAD' => [1 => ['STATUS' => '0', 'READY' => '10', 'SIZE' => '100', 'FILENAME' => 'file', 'HASH' => 'hash', 'TARGETDIRECTORY' => '', 'POWERDOWNLOAD' => '0']],
    'USER' => [2 => ['DOWNLOADID' => '1', 'STATUS' => '7', 'SPEED' => '20', 'DOWNLOADFROM' => '10', 'DOWNLOADTO' => '50', 'ACTUALDOWNLOADPOSITION' => '30']],
];
$downloads->process_sources();
expect($downloads->download(1)['phpaj_READY'] == 30, 'Source progress added');
expect($downloads->download(1)['phpaj_dl_speed'] == 20, 'Source speed aggregated');
expect($downloads->download(1)['phpaj_STATUS'] === '0_2', 'Active status');
expect(count($downloads->ids()) === 1, 'Download sorting');
$downloads->process_sources();
expect($downloads->download(1)['phpaj_READY'] == 30, 'Repeated aggregation resets counters');
$downloads->cache['DOWNLOAD'][1]['SIZE'] = '0';
$downloads->cache['DOWNLOAD'][1]['STATUS'] = '14';
$downloads->cache['USER'] = [];
$downloads->process_sources();
expect($downloads->download(1)['phpaj_DONE'] === 0.0, 'Empty file avoids division by zero');
$downloads->cache['IDS'] = [];
$downloads->process_sources();
expect($downloads->subdirs === [] && empty($downloads->cache['DOWNLOAD']), 'Deleted downloads clear directory groups');
expect($downloads->time() !== '', 'Missing timestamp remains renderable');

$uploads = new Uploads();
$uploads->cache = ['IDS' => ['VALUES' => ['UPLOADID' => [1 => true, 2 => true]]], 'UPLOAD' => [
    1 => ['ID' => '1', 'STATUS' => '1', 'PRIORITY' => '2'],
    2 => ['ID' => '2', 'STATUS' => '5', 'PRIORITY' => '1'],
    3 => ['ID' => '3', 'STATUS' => '1', 'PRIORITY' => '1'],
]];
$uploads->process_uploads();
expect($uploads->cache['phpaj_ul'] === 1 && $uploads->cache['phpaj_queue'] === 1, 'Upload counts');
expect(!isset($uploads->cache['UPLOAD'][3]), 'Stale upload removed');

$search = new Search();
$search->cache = ['SEARCH' => [1 => ['RUNNING' => 'canceled']], 'SEARCHENTRY' => [
    5 => ['SEARCHID' => '1', 'SIZE' => '10', 'FILENAME' => ['small.txt' => ['USER' => '1'], 'big.zip' => ['USER' => '3']]],
]];
$search->process_results();
expect($search->cache['SEARCHENTRY'][5]['phpaj_FILENAME'] === 'big.zip', 'Most common filename selected');
expect($search->cache['SEARCHENTRY'][5]['phpaj_COUNT'] == 4, 'Search source counts');
expect($search->cache['SEARCH'][1]['phpaj_FOUNDFILES'] === 1, 'Result count');
$_GET['deleteid'] = '99';
$search->delete(1);
expect(empty($search->cache['SEARCH']) && empty($search->cache['SEARCHENTRY']), 'Delete uses explicit ID, independent of HTTP request');

echo "Core models: aggregation, sorting, stale IDs and explicit search deletion passed\n";
