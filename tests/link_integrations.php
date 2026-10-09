<?php

declare(strict_types=1);
require __DIR__.'/HttpClient.php';
[$base,$core]=testOptions();
$client=new HttpClient($base);
$permalink=base64_encode($core.'|'.md5(''));
foreach(['l='.$permalink,'ajfsp_link='.rawurlencode('ajfsp://file|pending.bin|'.md5('pending').'|5000/'),'site=downloads'] as $query){
    $response=$client->request($query,null,'index.php');
    check($response['status']===200 && str_contains($response['body'],'assets/js/app.js'),'Legacy static entry');
}
$session=$client->get('session');
$login=$client->request('endpoint=session',['_csrf'=>$session['csrf'],'action'=>'login','l'=>$permalink]);
check(decodeJson($login['body'])['authenticated'],'Permalink login');
$link='ajfsp://file|extension-'.bin2hex(random_bytes(4)).'.bin|'.md5(random_bytes(16)).'|5000/';
$extension=new HttpClient($base);
$response=$extension->request('', ['host'=>$core,'cpass'=>md5(''),'ajfsp_link'=>$link],'index.php');
check($response['status']===200 && preg_match('/newlinkinfo(.*)ok/',$response['body'])===1,'Installed extension success parser');
check(($response['headers']['access-control-allow-origin']??'')==='*','Extension CORS');
$wrong=$extension->request('', ['host'=>$core,'cpass'=>'invalid','ajfsp_link'=>$link],'index.php');
check(str_contains($wrong['body'],'wrong password. access denied'),'Installed extension wrong-password parser');
check($extension->request('', ['ajfsp_link'=>$link],'index.php')['status']===400,'Proxy requires explicit credentials');
$script=file_get_contents($base.'/assets/js/app.js');
check(str_contains($script,"registerProtocolHandler('web+ajfsp'") && str_contains($script,'index.php?ajfsp_link=%s'),'Protocol registration');
echo "Legacy entries, permalink authentication and installed extension proxy contracts passed\n";
