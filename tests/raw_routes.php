<?php

declare(strict_types=1);
require __DIR__ . '/HttpClient.php';
[$base,$core]=testOptions();
$client=new HttpClient($base);
foreach(['status','downloads','uploads','dashboard','search','directories','news','parts','settings','files','shares','statistics','servers','sources','links','limits'] as $endpoint){
    $response=$client->request('endpoint='.$endpoint);
    check($response['status']===401 && decodeJson($response['body'])===['error'=>'unauthorized'],'Authentication '.$endpoint);
}
check($client->request('endpoint=unknown')['status']===404,'Unknown endpoint');
$session=$client->login($core);$csrf=$session['csrf'];
$status=$client->get('status');
foreach(['downloads_active','uploads_active','download_speed','upload_speed'] as $field)check(is_int($status[$field]) && $status[$field]>=0,'Numeric '.$field);
foreach(['downloads','uploads'] as $endpoint)check(array_is_list($client->get($endpoint)['items']),'Stable lists '.$endpoint);
check(isset($client->get('news')['html']),'News');
check(is_int($client->get('parts',['dl_id'=>105])['size']),'JSON parts');
foreach(['endpoint=parts','endpoint=parts&dl_id[]=105'] as $query)check($client->request($query)['status']===400,'Invalid part ID');
check($client->request('endpoint=limits')['status']===405,'GET write rejected');
foreach(['session','downloads','shares','files','settings','limits','servers','search','links'] as $endpoint)check($client->request('endpoint='.$endpoint,['action'=>'unknown'])['status']===403,'CSRF '.$endpoint);
check($client->request('endpoint=limits',['_csrf'=>$csrf,'action'=>'unknown'])['status']===400,'Unknown action');
check($client->request('endpoint=limits',['_csrf'=>$csrf,'action'=>'set_maxdl','value'=>'-1'])['status']===400,'Negative limit');
$settings=$client->get('settings')['values'];
check(!isset($settings['password'],$settings['xmlpassword'],$settings['core_pass']),'No settings credentials');
foreach(['dl'=>'downloads','ul'=>'uploads'] as $kind=>$endpoint){
    $previous=$client->get($endpoint)['max'];
    try{$client->post('limits',['action'=>'set_max'.$kind,'value'=>42000]);check($client->get($endpoint)['max']===42000,'Limit roundtrip');}
    finally{$client->post('limits',['action'=>'set_max'.$kind,'value'=>$previous]);}
}
check($client->get('settings')['values']===$settings,'Unrelated settings preserved');
echo "API contracts: auth, JSON types, validation, CSRF, limits and settings preservation passed\n";
