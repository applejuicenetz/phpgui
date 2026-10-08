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
foreach ($manifest['icons'] as $icon) {
    $data = file_get_contents($base . '/' . $icon['src']);
    check($data !== false && $data !== '', 'Icon accessible');
    if ($icon['type'] === 'image/png') {
        $size = getimagesizefromstring($data);
        check($size !== false && $size[0] . 'x' . $size[1] === $icon['sizes'], 'Icon dimensions');
    }
}
echo "Manifest and app icons passed\n";
