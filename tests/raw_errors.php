<?php

declare(strict_types=1);
use appleJuiceNETZ\Api\Router;
const GUI_ROOT=__DIR__.'/..';
spl_autoload_register(static function(string $class):void{$file=GUI_ROOT.'/src/'.str_replace(['appleJuiceNETZ\\','\\'],['','/'],$class.'.php');if(is_file($file))require_once $file;});
$_SERVER['REQUEST_METHOD']='GET';
$_ENV=['GUI_LANGUAGE'=>'de','GUI_SHOW_NEWS'=>1,'ALLOWED_SERVERMSG_TAGS'=>'<b><a>'];
$core=$argv[1]??'http://127.0.0.1:19871';
foreach(['status','downloads','directories','news','parts','settings','files','statistics','sources','servers','dashboard'] as $endpoint){
    foreach([401=>$core,503=>'http://127.0.0.1:1'] as $status=>$host){
        $_SESSION=['core_host'=>$host,'core_pass'=>md5('invalid-test-password')];
        $_GET=['endpoint'=>$endpoint,'dl_id'=>'105'];http_response_code(200);
        ob_start();(new Router())->handle();$body=ob_get_clean();
        if(http_response_code()!==$status || $body!==json_encode(['error'=>$status===401?'unauthorized':'core_unavailable']))throw new RuntimeException($endpoint.': unexpected error response');
    }
}
echo "Core authentication and connection errors: JSON responses passed\n";
