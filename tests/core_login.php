<?php

declare(strict_types=1);
require __DIR__ . '/../src/Api/CoreLogin.php';
use appleJuiceNETZ\Api\CoreLogin;

$_SESSION = ['core_host' => 'http://old.example', 'core_pass' => 'old', 'cache' => ['old' => true], 'phpaj' => ['old' => true], 'SEPARATOR' => '\\'];
CoreLogin::bind('http://new.example/', 'new');
if (isset($_SESSION['cache'], $_SESSION['phpaj'], $_SESSION['SEPARATOR'])) throw new RuntimeException('Old Core cache retained');
if ($_SESSION['core_host'] !== 'http://new.example') throw new RuntimeException('Core URL normalization');
$_SESSION['cache'] = ['current' => true];
CoreLogin::bind('http://new.example', 'new');
if ($_SESSION['cache'] !== ['current' => true]) throw new RuntimeException('Same Core cache lost');
CoreLogin::bind('http://new.example', 'changed');
if (isset($_SESSION['cache'])) throw new RuntimeException('Changed credentials retain cache');
echo "Core login: cache isolation and URL normalization passed\n";
