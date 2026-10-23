<?php

declare(strict_types=1);

// Avoid loading local deployment configuration during the runtime check.
define('GUI_ROOT', __DIR__ . '/runtime-fixture');
require_once dirname(__DIR__) . '/src/Kernel.php';

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
\appleJuiceNETZ\Kernel::init();
if (session_status() !== PHP_SESSION_ACTIVE) throw new RuntimeException('Session initialization failed');
$composer = json_decode(file_get_contents(dirname(__DIR__) . '/composer.json'), true, flags: JSON_THROW_ON_ERROR);
if ($composer['require']['php'] !== '^8.3') throw new RuntimeException('PHP 8.3 minimum required in Composer');
session_destroy();
print 'PHP runtime initialization passed on ' . PHP_VERSION . "\n";
