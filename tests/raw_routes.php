<?php

declare(strict_types=1);

require __DIR__ . '/HttpClient.php';
[$base, $core] = testOptions();
$client = new HttpClient($base);
foreach (['live', 'directories', 'news', 'parts&dl_id=105'] as $endpoint) {
    $response = $client->request('api=' . $endpoint);
    check($response['status'] === 401, 'Unauthenticated API status');
    check(str_starts_with($endpoint, 'parts') ? $response['body'] === '' : decodeJson($response['body']) === ['error' => 'unauthorized'], 'Unauthenticated API body');
}
$login = $client->request('', ['host' => $core, 'cpass' => '']);
check($login['status'] === 200, 'Login');
$csrf = csrfToken($login['body']);
$response = $client->request('api=live&type=status,downloads,uploads,dashboard,search');
check($response['status'] === 200 && str_contains($response['headers']['content-type'], 'application/json'), 'JSON response');
check(array_keys(decodeJson($response['body'])) === ['status', 'downloads', 'uploads', 'dashboard', 'search'], 'Live blocks');
$status = $client->live('status');
foreach (['downloads_active', 'uploads_active'] as $field) {
    check(isset($status[$field]) && is_int($status[$field]) && $status[$field] >= 0, 'Active count: ' . $field);
}
foreach (['dl_speed_raw', 'ul_speed_raw'] as $field) {
    check(isset($status[$field]) && is_numeric($status[$field]) && $status[$field] >= 0, 'Current transfer speed: ' . $field);
}
check(array_keys(decodeJson($client->request('api=live')['body'])) === ['status'], 'Default status block');
check((bool)decodeJson($client->request('api=directories&dir=/')['body'])['entries'], 'Directories');
$news = $client->request('api=news');
check($news['status'] === 200 && array_keys(decodeJson($news['body'])) === ['html'], 'News API response');
$response = $client->request('api=parts&dl_id=105');
check($response['status'] === 200 && str_contains($response['headers']['content-type'], 'image/svg+xml') && str_starts_with($response['body'], '<svg'), 'SVG response');
foreach (['api=parts', 'api=parts&dl_id[]=105'] as $query) {
    check($client->request($query)['status'] === 400, 'Invalid part ID');
}
check($client->request('api=limits&action=set_maxdl')['status'] === 403, 'GET limit rejected');
check($client->request('api=limits&action=set_maxdl', ['value' => 42])['status'] === 403, 'CSRF required');
check($client->request('api=limits&action=unknown', ['_csrf' => $csrf])['status'] === 400, 'Unknown action');
foreach (['dl' => 'downloads', 'ul' => 'uploads'] as $kind => $block) {
    $previous = $client->live($block)['max_raw'];
    try {
        $response = $client->request('api=limits&action=set_max' . $kind, ['_csrf' => $csrf, 'value' => 42000]);
        check(decodeJson($response['body']) === ['ok' => true], 'Save limit');
        check($client->live($block)['max_raw'] === 42000, 'Limit roundtrip');
    } finally {
        $client->request('api=limits&action=set_max' . $kind, ['_csrf' => $csrf, 'value' => $previous]);
    }
}
echo "API routes: authentication, JSON, SVG, validation, CSRF and limits passed\n";
