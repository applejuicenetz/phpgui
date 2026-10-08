<?php

declare(strict_types=1);

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\GUI\PartsSvg;

header('Cache-Control: no-cache');
$downloadId = isset($_GET['dl_id']) && ctype_digit((string)$_GET['dl_id']) ? (int)$_GET['dl_id'] : 0;
$userId = isset($_GET['usr_id']) && ctype_digit((string)$_GET['usr_id']) ? (int)$_GET['usr_id'] : 0;
if ($downloadId <= 0 && $userId <= 0) {
    http_response_code(400);
    return;
}
$core = new Core();
$map = $core->command('xml', ($downloadId > 0 ? 'downloadpartlist.xml?id=' . $downloadId : 'userpartlist.xml?id=' . $userId));
$size = (float)array_key_first($map['FILEINFORMATION'] ?? []);
if ($size <= 0) {
    http_response_code(404);
    return;
}
$downloads = new Downloads();
$downloads->refresh_cache();
$transfers = [];
if ($downloadId > 0 && isset($downloads->cache['DOWNLOAD'][$downloadId])) {
    $download = $downloads->download($downloadId);
    $transfers = array_values($download['phpaj_loading_parts'] ?? []);
} elseif ($userId > 0 && isset($downloads->cache['USER'][$userId])) {
    $transfers[] = $downloads->user($userId);
}
header('Content-Type: image/svg+xml; charset=utf-8');
header("Content-Security-Policy: default-src 'none'; sandbox");
echo PartsSvg::render($size, $map['PART'] ?? [], $downloadId > 0, $transfers);
