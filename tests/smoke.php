<?php

declare(strict_types=1);
require __DIR__ . '/HttpClient.php';
[$base, $core] = testOptions();
$client = new HttpClient($base);
$session = $client->login($core);
check($session['refresh'] >= 1, 'Refresh interval');
foreach (['status','dashboard','downloads','uploads','search','shares','servers','settings','statistics'] as $endpoint) check($client->get($endpoint) !== [], $endpoint);
$started = microtime(true);
$dashboard = $client->get('dashboard');
check(microtime(true)-$started < 2, 'Dashboard independent of news');
check(is_int($dashboard['time']), 'Raw server timestamp');
check($client->get('parts',['dl_id'=>105])['parts'] !== [], 'Download parts');
$sources = $client->get('sources',['dl_id'=>105]);
check(isset($sources['groups']['active']), 'Source groups');
$sourceId = $sources['groups']['active'][0]['id'] ?? null;
if ($sourceId !== null) check($client->get('parts',['usr_id'=>$sourceId])['download'] === false, 'Source parts');
function items(HttpClient $client): array { return array_column($client->get('downloads')['items'], null, 'id'); }
check(count(items($client)) >= 7, 'Download count');
foreach (items($client) as $item) check(isset($item['loaded'],$item['size'],$item['rest'],$item['speed']), 'Raw progress fields');
foreach ([['pausedownload','','status','paused'],['resumedownload','','status','loading'],['renamedownload','Ä & <probe>.iso','name','Ä & <probe>.iso'],['settargetdir','Probe','target','Probe'],['setpowerdownload','3.2','pdl',3.2]] as [$action,$value,$key,$expected]) {
    $client->post('downloads',['action'=>$action,'dl_id'=>['105'],'action_value'=>$value]);
    check(items($client)[105][$key] === $expected, $action);
}
$client->post('downloads',['action'=>'settargetdir','dl_id'=>['105'],'action_value'=>'RowOnly']);
foreach (items($client) as $id=>$item) check($id===105 ? $item['target']==='RowOnly' : $item['target']!=='RowOnly', 'Row target isolation');
foreach (['most','-most','last','-last','search','-search'] as $mode) check(count($client->get('statistics',['stats'=>$mode])['rows'])>0, 'Statistics '.$mode);
check($client->get('statistics',['stats'=>'bogus'])['mode']==='most', 'Default statistics mode');
$files=$client->get('files',['q'=>'DEBIAN']);
check($files['filter']==='DEBIAN' && $files['folders']===[], 'Flat filtered results');
check(count(array_filter($files['files'],static fn($r)=>str_contains(strtolower($r['name']),'debian')))===count($files['files']), 'Case-insensitive search');
check($client->get('files',['q'=>'zzz-no-match'])['files']===[], 'Empty search');
$isos=$client->get('files',['q'=>'ubuntu-24.04']);
check(count($isos['files'])===9, 'Global subtree search');
$scoped=$client->get('files',['dir'=>'/mock/isos/ubuntu','q'=>'24.04']);
check(count($scoped['files'])===4, 'Scoped subtree search');
check(count($client->post('files',['action'=>'export'],['q'=>'ubuntu-24.04'])['links'])===9, 'Filtered export');
check(count($client->get('files',['dir'=>'/mock/isos'])['folders'])>0, 'Distribution folders');
$client->post('shares',['action'=>'add','name'=>'/mock/new','subs'=>'1']);
check(in_array('/mock/new',array_column($client->get('shares')['dirs'],'name'),true), 'Add share');
$client->post('shares',['action'=>'remove','name'=>'/mock/new']);
check(!in_array('/mock/new',array_column($client->get('shares')['dirs'],'name'),true), 'Remove share');
check($client->post('files',['action'=>'export'],['dir'=>'/mock/incoming'])['links']!==[], 'Folder export');
$client->post('settings',['change'=>'connection','maxcon'=>'250','maxul'=>'262144','maxdl'=>'1263616','uls'=>'30','conturn'=>'50','maxdlsrc'=>'500','autoconnect'=>'true']);
check($client->get('downloads')['max']===1263616, 'Settings bytes');
$client->post('search',['action'=>'start','searchstring'=>'probe']);
check(in_array('probe',array_column($client->get('search')['searches'],'text'),true), 'Search start');
$run=bin2hex(random_bytes(4));
$link='ajfsp://file|targeted-'.$run.'.iso|'.md5($run).'|5000/';
check($client->post('links',['ajfsp_link'=>$link,'ajfsp_target'=>'/Linux//ISOs/'])['results'][0]['ok'], 'Targeted link');
$found=array_filter(items($client),static fn($r)=>$r['name']==='targeted-'.$run.'.iso');
check(count($found)===1 && reset($found)['target']==='Linux/ISOs', 'Link target reaches Core');
$rejected=$client->request('endpoint=links',['_csrf'=>$session['csrf'],'ajfsp_link'=>'ajfsp://file|rejected.iso|'.md5('rejected').'|5000/','ajfsp_target'=>'../outside']);
check($rejected['status']===400 && decodeJson($rejected['body'])['error']==='invalid_target', 'Invalid target');
check(array_filter(items($client),static fn($r)=>$r['name']==='rejected.iso')===[], 'No rejected download');
check($client->post('links',['ajfsp_link'=>'ajfsp://server|target.example|9855/','ajfsp_target'=>'Ignored'])['results'][0]['ok'], 'Server link');
check($client->get('directories',['dir'=>'/'])['entries']!==[], 'Directories');
printf("%d HTTP requests: JSON data, actions, settings, shares, export, search and links passed\n",$client->count);
