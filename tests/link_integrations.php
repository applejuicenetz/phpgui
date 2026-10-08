<?php

declare(strict_types=1);

require __DIR__ . '/HttpClient.php';
[$base, $core] = testOptions();
$client = new HttpClient($base);
$permalink = base64_encode($core . '|' . md5(''));
check(str_contains($client->request(http_build_query(['l' => $permalink]))['body'], 'data-site='), 'Permalink login');
$link = 'ajfsp://file|protocol-probe.bin|0123456789abcdef0123456789abcdef|5000/';
check(str_contains($client->request(http_build_query(['ajfsp_link' => $link]))['body'], 'protocol-probe.bin'), 'Authenticated handler delivery');
$client->request('site=logout');
$login = $client->request(http_build_query(['ajfsp_link' => $link]))['body'];
check(str_contains($login, 'ajfsp_link') && str_contains($login, 'protocol-probe.bin'), 'Handler delivery before login');
$body = $client->request('', ['host' => $core, 'cpass' => '', 'ajfsp_link' => $link])['body'];
check(str_contains($body, 'protocol-probe.bin'), 'Handler delivery after login');
foreach (['login.js', 'app.js'] as $asset) {
    $script = file_get_contents(rtrim($base, '/') . '/assets/js/' . $asset);
    check($script !== false && str_contains($script, "registerProtocolHandler('web+ajfsp'") && str_contains($script, 'index.php?ajfsp_link=%s'), 'Protocol registration: ' . $asset);
}
echo "Link integrations: permalink login and protocol-handler delivery before/after login passed\n";
