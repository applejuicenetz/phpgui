<?php

declare(strict_types=1);
require __DIR__ . '/HttpClient.php';
[$base] = testOptions();
$client = new HttpClient($base);
check(str_contains($client->request()['body'], 'rel="manifest"'), 'Manifest linked on login');
$body = file_get_contents($base . '/manifest.json');
check($body !== false, 'Manifest accessible');
$manifest = decodeJson($body);
check($manifest['display'] === 'standalone' && $manifest['start_url'] === './index.php', 'Manifest launch');
$protocol = $manifest['protocol_handlers'][0] ?? [];
check(($protocol['protocol'] ?? '') === 'web+ajfsp' && str_contains($protocol['url'] ?? '', 'index.php?ajfsp_link=%s'), 'Manifest protocol handler');
$fileHandler = $manifest['file_handlers'][0] ?? [];
check(($fileHandler['action'] ?? '') === './index.php?site=downloads' && in_array('.ajl', array_merge(...array_values($fileHandler['accept'] ?? [[]])), true), 'Manifest file handler');
foreach ($manifest['icons'] as $icon) {
    $data = file_get_contents($base . '/' . $icon['src']);
    check($data !== false && $data !== '', 'Icon accessible');
    if ($icon['type'] === 'image/png') {
        $size = getimagesizefromstring($data);
        check($size !== false && $size[0] . 'x' . $size[1] === $icon['sizes'], 'Icon dimensions');
    }
}
echo "Manifest and app icons passed\n";
