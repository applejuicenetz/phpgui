<?php

declare(strict_types=1);

require __DIR__ . '/HttpClient.php';
[$base, $core] = testOptions();
$client = new HttpClient($base);
check($client->request('', ['host' => $core, 'cpass' => ''])['status'] === 200, 'Login failed');
foreach (['start', 'downloads', 'uploads', 'search', 'shares', 'sharefiles&dir=/mock/incoming', 'server', 'settings', 'help', 'dl_users&dl_id=105', 'dl_parts&dl_id=105', 'extras&show=ajl/ajl.php', 'extras&show=sharestats/sharestats.php', 'extras&show=phpinfo/phpinfo.php'] as $site) {
    $page = $client->page($site);
    check(preg_match('/data-refresh="(\d+)"/', $page, $refresh) === 1 && (int)$refresh[1] >= 1, 'Refresh interval');
    foreach (['download', 'upload'] as $direction) {
        check(preg_match('/id="aj-status-' . $direction . '">([^<]+)<\/span>/', $page, $matches) === 1 && str_contains($matches[1], '/s'), 'Initial topbar speed: ' . $direction);
    }
}
$started = microtime(true);
$start = $client->page('start');
check(microtime(true) - $started < 2.0, 'Dashboard must not wait for the news server');
check(preg_match('/id="aj-news"[^>]*hidden>.*?<div class="content" id="aj-news-content"><\/div>/s', $start) === 1, 'News container is rendered empty and hidden');
check(preg_match('/<dt>[^<]*(?:Server Zeit|Server time)[^<]*<\/dt><dd>(\d{2}:\d{2}:\d{2})<\/dd>/u', $start) === 1, 'Dashboard server time shows time only');
check(str_starts_with($client->request('api=parts&dl_id=105')['body'], '<svg'), 'SVG');
$downloads = $client->live('downloads');
check(count($downloads['items']) >= 7, 'Download count');
foreach ($downloads['items'] as $item) {
    check(isset($item['loaded'], $item['size'], $item['eta']), 'Download progress fields');
}
foreach ([
    ['pausedownload', [], 'status', 'paused'],
    ['resumedownload', [], 'status', 'loading'],
    ['renamedownload', ['action_value' => 'Ä & <probe>.iso'], 'name', 'Ä & <probe>.iso'],
    ['settargetdir', ['action_value' => 'Probe'], 'target', 'Probe'],
    ['setpowerdownload', ['action_value' => '3.2'], 'pdl', '3.2'],
] as [$action, $fields, $key, $expected]) {
    $client->post('downloads', ['action' => $action, 'dl_id' => ['105']] + $fields);
    check($client->live('downloads')['items'][105][$key] === $expected, $action);
}
$downloadsPage = $client->page('downloads');
check(str_contains($downloadsPage, 'data-row-action="target"'), 'Row menu target directory action');
check(preg_match('/data-aj="target">[^<]+<\/span>/', $downloadsPage) === 1, 'Target directory is displayed');
$client->post('downloads', ['action' => 'settargetdir', 'dl_id' => ['105'], 'action_value' => 'RowOnly']);
$items = $client->live('downloads')['items'];
check($items[105]['target'] === 'RowOnly', 'Row target directory set');
foreach ($items as $id => $item) {
    check($id === 105 || $item['target'] !== 'RowOnly', 'Row target must not change other downloads');
}
$client->post('shares', ['action' => 'add', 'name' => '/mock/new', 'subs' => '1']);
check(str_contains($client->page('shares'), '/mock/new'), 'Add share');
$client->post('shares', ['action' => 'remove', 'name' => '/mock/new']);
check(!str_contains($client->page('shares'), '/mock/new'), 'Remove share');
$client->post('sharefiles&dir=/mock/incoming', ['exportlinks' => '1']);
check(str_contains($client->page('sharefiles&dir=/mock/incoming'), 'ajfsp://file|'), 'Export');
$client->post('settings', ['change' => 'connection', 'maxcon' => '250', 'maxul' => '256', 'maxdl' => '1234', 'uls' => '30', 'conturn' => '50', 'maxdlsrc' => '500', 'autoconnect' => 'true']);
check($client->live('downloads')['max_raw'] === 1234 * 1024, 'Settings');
$client->post('search', ['searchstring' => 'probe']);
$searchPage = $client->page('search');
check(str_contains($searchPage, 'probe'), 'Search');
check(preg_match('/<th[^>]*>\s*(?:Format)\s*<\/th>/u', $searchPage) === 0, 'Search table has no format column');
check(!str_contains($searchPage, 'search-format-dropdown'), 'Format button is gone');
check(preg_match('/<th[^>]*>\s*Quellen\s*<\/th>\s*<th[^>]*>\s*Größe\s*<\/th>/u', strip_tags($searchPage, '<th>')) === 1, 'Sources column precedes size column');
$uploads = $client->page('uploads');
check(!str_contains($uploads, 'speed-panel-note') && preg_match('/data-speed-label[^>]*>[^<]*Slots: \d+%/u', $uploads) === 1, 'Upload slot percentage inside speed bar');
check(!str_contains($client->page('downloads'), 'speed-panel-note'), 'No empty download speed note');
$marker = $client->request('site=downloads', ['ajfsp_link' => 'ajfsp://file|smoke.bin|0123456789abcdef0123456789abcdef|5000/'])['body'];
check(str_contains($marker, 'newlinkinfo') && str_contains($marker, 'smoke.bin'), 'Link marker');
check((bool)decodeJson($client->request('api=directories&dir=/')['body'])['entries'], 'Directories');
printf("%d HTTP requests: pages, SVG, actions, settings, shares, export, search and link marker passed\n", $client->count);
