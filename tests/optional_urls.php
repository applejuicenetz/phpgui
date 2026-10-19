<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $file = dirname(__DIR__) . '/src/' . str_replace(['appleJuiceNETZ\\', '\\'], ['', '/'], $class . '.php');
    if (is_file($file)) require_once $file;
});

use appleJuiceNETZ\GUI\NewsFeed;
use appleJuiceNETZ\GUI\VersionCheck;
use appleJuiceNETZ\appleJuice\Server;

const PHP_GUI_VERSION = '0.0.1';

set_error_handler(static function (int $severity, string $message): never {
    throw new RuntimeException($message);
});
foreach (['', '   '] as $empty) {
    $_ENV = ['NEWS_URL' => $empty, 'RELEASE_URL' => $empty, 'SERVERLIST_URL' => $empty];
    $_SESSION = ['phpaj' => [
        'news' => ['version' => 'test', 'time' => time(), 'html' => 'stale news'],
        'version_check' => ['time' => time(), 'version' => '999.0'],
    ]];
    if (NewsFeed::load('test') !== '') throw new RuntimeException('Disabled news returned cached content');
    if (VersionCheck::newer() !== null) throw new RuntimeException('Disabled version check returned cached content');
    // No constructor/Core connection needed: a disabled import must return before using Core.
    $server = (new ReflectionClass(Server::class))->newInstanceWithoutConstructor();
    $server->getmore();
}
restore_error_handler();
print "Optional URL checks passed\n";
