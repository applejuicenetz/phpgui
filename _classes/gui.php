<?php
class gui {

   public $data;

   function __construct() {
     }

   function check() {
      $data = file_get_contents("http://www.applejuicenet.cc/inprog/news.php?version=0.31.149.111",
    false,
    stream_context_create(
        [
            'http' => [
                'ignore_errors' => true,
            ],
        ]
    ));
      $this->data = json_decode($data);
      return $data;
   }
}
$gui = new gui();
$check = $gui->check();

echo $check;
if(function_exists("fsockopen")) {
 echo "Function Exists";
 }
 else
 echo "function not exists";
 
$fp = @fsockopen("ssl://api.github.com/repos/kddk22/phpgui/releases/latest", 80, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    $out = "GET / HTTP/1.1\r\n";
    $out .= "Host: www.example.com\r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $out);
    while (!feof($fp)) {
        echo fgets($fp, 128);
    }
    fclose($fp);
}
?>