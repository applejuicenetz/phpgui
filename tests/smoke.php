<?php

declare(strict_types=1);

require __DIR__ . '/HttpClient.php';
[$base, $core] = testOptions();
$client = new HttpClient($base);
check($client->request('', ['host' => $core, 'cpass' => ''])['status'] === 200, 'Login failed');
foreach (['start', 'downloads', 'uploads', 'search', 'shares', 'sharefiles&dir=/mock/incoming', 'server', 'settings', 'help', 'dl_users&dl_id=105', 'dl_parts&dl_id=105', 'sharestats'] as $site) {
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
$stats = $client->page('sharestats');
check(str_contains($stats, 'class="tabs is-boxed share-tabs"') && str_contains($stats, 'index.php?site=shares'), 'Share statistics tab bar links to shared folders');
check(preg_match('/<li class="is-active"[^>]*><a href="index.php\?site=sharestats"/', $stats) === 1, 'Statistics tab is active');
foreach (['shares', 'sharestats', 'sharefiles&dir=/mock/incoming', 'shares&q=debian'] as $tabPage) {
    $tabHtml = $tabPage === 'sharestats' ? $stats : $client->page($tabPage);
    check(preg_match('/<a href="index.php\?site=shares" role="tab">\s*(?:geteilte Ordner|shared folders)\s*<\/a>/u', $tabHtml) === 1, 'Folders tab is labelled on ' . $tabPage);
    check(preg_match('/<a href="index.php\?site=sharestats" role="tab">\s*(?:Statistik|Statistics)\s*<\/a>/u', $tabHtml) === 1, 'Statistics tab is labelled on ' . $tabPage);
}
foreach (['most', '-most', 'last', '-last', 'search', '-search'] as $mode) {
    $page = $client->page('sharestats&stats=' . $mode);
    check(substr_count($page, '<tr>') > 1 && str_contains($page, 'ajfsp://file|'), 'Share statistics mode ' . $mode);
}
check(substr_count($client->page('sharestats&stats=bogus'), '<tr>') > 1, 'Unknown statistics mode falls back');
check(preg_match('/<li class="is-active"[^>]*><a href="index.php\?site=shares"/', $client->page('sharefiles&dir=/mock/incoming')) === 1, 'Folder view keeps the shared-folders tab active');
foreach (['extras', 'extras&show=phpinfo/phpinfo.php', 'extras&show=ajl/ajl.php'] as $removed) {
    $response = $client->request('site=' . $removed);
    check($response['status'] === 404 && !str_contains($response['body'], 'phpinfo'), 'Plugin page is gone: ' . $removed);
}
$linksDialog = $client->page('downloads');
check(str_contains($linksDialog, 'id="ajfsp-file-input"') && str_contains($linksDialog, 'accept=".ajl"') && str_contains($linksDialog, 'data-text-invalid='), 'Links dialog offers .ajl file selection');
check(str_contains($linksDialog, 'js/links.js') && !str_contains($linksDialog, 'extras'), 'Links script loaded, plugin menu gone');
$shares = $client->page('shares');
check(str_contains($shares, 'id="share-search"') && str_contains($shares, 'name="site" value="shares"'), 'Search field is on the shares page');
check(!str_contains($shares, 'name="sharefile[]"'), 'Shares page without query lists directories, not files');
$filtered = $client->page('shares&q=' . rawurlencode('DEBIAN'));
check(str_contains($filtered, 'debian-13.7.0') && !str_contains($filtered, 'ajcore.jar'), 'Share search is case-insensitive');
check(str_contains($filtered, 'value="DEBIAN"') && !str_contains($filtered, 'class="dir-row"'), 'Share search keeps query and lists flat results');
check(str_contains($filtered, '/mock/incoming/') || str_contains($filtered, '/mock/isos/'), 'Search results show their path');
check(str_contains($client->page('shares&q=zzz-no-match'), 'id="share-search"'), 'Share search without matches renders');
$isos = $client->page('shares&q=ubuntu-24.04');
check(substr_count($isos, 'name="sharefile[]"') === 9 && str_contains($isos, '/mock/isos/ubuntu/24.04.3/'), 'Share search covers all shared folders and nested ISO folders');
$scoped = $client->page('sharefiles&dir=/mock/isos/ubuntu&q=24.04');
check(substr_count($scoped, 'name="sharefile[]"') === 4 && !str_contains($scoped, 'debian-'), 'Folder view still searches only its subtree');
$client->post('shares&q=' . rawurlencode('ubuntu-24.04'), ['exportlinks' => '1']);
$export = $client->page('shares&q=' . rawurlencode('ubuntu-24.04'));
check(substr_count($export, 'ajfsp://file|ubuntu-24.04') >= 9, 'Export from global search covers all pages of matches');
$client->post('shares&q=' . rawurlencode('ubuntu-24.04'), ['clear_list' => '1']);
check(str_contains($client->page('sharefiles&dir=/mock/isos'), 'dir=%2Fmock%2Fisos%2Fubuntu'), 'ISO distribution folders are listed');
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
check(str_contains($linksDialog, 'name="ajfsp_target"') && str_contains($linksDialog, 'maxlength="255"'), 'Links dialog has a target directory field');
$run = bin2hex(random_bytes(4));
$targetedChecksum = md5('targeted' . $run);
$targeted = $client->request('site=downloads', ['ajfsp_link' => 'ajfsp://file|targeted-' . $run . '.iso|' . $targetedChecksum . '|5000/', 'ajfsp_target' => '/Linux//ISOs/'])['body'];
check(str_contains($targeted, 'targeted-' . $run . '.iso'), 'Link with target directory accepted');
$targetedItems = array_filter($client->live('downloads')['items'], static fn(array $item): bool => $item['name'] === 'targeted-' . $run . '.iso');
check(count($targetedItems) === 1 && reset($targetedItems)['target'] === 'Linux/ISOs', 'Link target directory reaches the Core');
$rejected = $client->request('site=downloads', ['ajfsp_link' => 'ajfsp://file|rejected.iso|2123456789abcdef0123456789abcdef|5000/', 'ajfsp_target' => '../outside'])['body'];
check(preg_match('/Ungültiges Zielverzeichnis|Invalid target directory/u', $rejected) === 1 && !str_contains($rejected, 'rejected.iso'), 'Invalid target directory is rejected visibly');
check(array_filter($client->live('downloads')['items'], static fn(array $item): bool => $item['name'] === 'rejected.iso') === [], 'Rejected links are not added');
$serverLink = $client->request('site=downloads', ['ajfsp_link' => 'ajfsp://server|target.example|9855/', 'ajfsp_target' => 'Ignored'])['body'];
check(str_contains($serverLink, 'data-site='), 'Server links ignore the target directory');
$marker = $client->request('site=downloads', ['ajfsp_link' => 'ajfsp://file|smoke.bin|0123456789abcdef0123456789abcdef|5000/'])['body'];
check(str_contains($marker, 'newlinkinfo') && str_contains($marker, 'smoke.bin'), 'Link marker');
check((bool)decodeJson($client->request('api=directories&dir=/')['body'])['entries'], 'Directories');
printf("%d HTTP requests: pages, SVG, actions, settings, shares, export, search and link marker passed\n", $client->count);
