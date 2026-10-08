<?php

declare(strict_types=1);

use appleJuiceNETZ\GUI\Router;

const GUI_ROOT = __DIR__ . '/..';
spl_autoload_register(static function (string $class): void {
    $file = GUI_ROOT . '/src/' . str_replace(['appleJuiceNETZ\\', '\\'], ['', '/'], $class . '.php');
    if (is_file($file)) {
        require_once $file;
    }
});
$_SERVER['REQUEST_METHOD'] = 'GET';
$_ENV['GUI_LANGUAGE'] = 'de';

$core = $argv[1] ?? 'http://127.0.0.1:19861';
$router = new Router();
$dispatch = new ReflectionMethod(Router::class, 'raw');
foreach (['live', 'directories', 'news', 'parts'] as $site) {
    foreach ([401 => $core, 503 => 'http://127.0.0.1:1'] as $status => $host) {
        $_SESSION = ['core_host' => $host, 'core_pass' => md5('invalid-test-password')];
        $_GET = ['site' => $site, 'dl_id' => '105', 'type' => 'status'];
        ob_start();
        $dispatch->invoke($router, $site);
        $body = ob_get_clean();
        if (http_response_code() !== $status) {
            throw new RuntimeException($site . ': unexpected error status');
        }
        $expected = $site === 'parts' ? '' : json_encode(['error' => $status === 401 ? 'unauthorized' : 'core_unavailable']);
        if ($body !== $expected) {
            throw new RuntimeException($site . ': unexpected error response: ' . $body);
        }
    }
}
echo "Raw routes: Core authentication and connection errors passed\n";
