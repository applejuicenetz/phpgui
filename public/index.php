<?php

declare(strict_types=1);

// Compatibility entry point for old bookmarks and the browser extension.
// Ordinary GETs serve exactly the same static frontend as index.html.
header('Referrer-Policy: no-referrer');
header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    readfile(__DIR__ . '/index.html');
    return;
}

const GUI_ROOT = __DIR__ . '/..';
require_once GUI_ROOT . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
try {
    // Extension explicitly supplies Core credentials, never ambient session authority.
    $host = \appleJuiceNETZ\GUI\Request::str('host');
    $hash = \appleJuiceNETZ\Api\CoreLogin::verify($host, \appleJuiceNETZ\GUI\Request::str('cpass'));
    \appleJuiceNETZ\Api\CoreLogin::bind($host, $hash);
    $result = (new \appleJuiceNETZ\Api\LinksEndpoint())->post();
    $result['markers'] = array_values(array_map(
        static fn(array $r): string => 'newlinkinfo ' . $r['link']['raw'] . ' ok',
        array_filter($result['results'], static fn(array $r): bool => $r['ok'])
    ));
    // The extension expects literal pipes, slashes and non-ASCII filenames.
    echo json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
} catch (\appleJuiceNETZ\Api\ApiException $error) {
    http_response_code($error->status);
    echo json_encode(['error' => $error->error, 'reply' => $error->error === 'wrong_password' ? 'wrong password. access denied' : $error->error]);
} catch (\Throwable) {
    http_response_code(503);
    echo json_encode(['error' => 'core_unavailable']);
}
