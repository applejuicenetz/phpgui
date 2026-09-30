<?php

require_once __DIR__ . '/../src/GUI/Permalink.php';
require_once __DIR__ . '/../src/GUI/Router.php';
require_once __DIR__ . '/../src/GUI/subs.php';
require_once __DIR__ . '/../src/appleJuice/Core.php';
require_once __DIR__ . '/../src/appleJuice/Share.php';

use appleJuiceNETZ\GUI\Permalink;
use appleJuiceNETZ\GUI\Router;
use appleJuiceNETZ\appleJuice\Share;

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$host = 'http://127.0.0.1:9851';
$hash = md5('secret');
$link = Permalink::create($host, $hash);
check(Permalink::parse(rawurldecode(substr($link, strlen('index.php?l=')))) === [$host, $hash], 'Permalink round trip failed');
check(Permalink::parse(base64_encode($host . '|secret')) === [$host, 'secret'], 'Legacy permalink failed');
check(Permalink::parse('invalid') === null, 'Invalid permalink accepted');
check(Permalink::parse(base64_encode('file:///tmp|secret')) === null, 'Invalid Core URL accepted');

class CoreHttpFixture
{
    public $context;
    private int $offset = 0;
    private string $response = '<SETTINGS />';

    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        return str_contains($path, '/xml/settings.xml?') && str_contains($path, 'password=' . md5('secret'));
    }

    public function stream_read(int $count): string
    {
        $chunk = substr($this->response, $this->offset, $count);
        $this->offset += strlen($chunk);
        return $chunk;
    }

    public function stream_eof(): bool
    {
        return $this->offset >= strlen($this->response);
    }

    public function stream_stat(): array
    {
        return [];
    }
}

check(stream_wrapper_unregister('http'), 'Could not replace HTTP stream wrapper');
check(stream_wrapper_register('http', CoreHttpFixture::class), 'Could not install HTTP fixture');
try {
    $_SESSION = [];
    $_POST = [];
    $_GET = ['l' => base64_encode($host . '|secret')];
    (new Router())->handle();
    check($_SESSION['core_host'] === $host, 'Permalink did not select the Core');
    check($_SESSION['core_pass'] === $hash, 'Permalink did not restore the Core password hash');

    $_SESSION = [];
    $_GET = ['l' => rawurldecode(substr($link, strlen('index.php?l=')))];
    (new Router())->handle();
    check($_SESSION['core_pass'] === $hash, 'New permalink did not authenticate');
} finally {
    stream_wrapper_restore('http');
}

$_SESSION['SEPARATOR'] = '/';
$_SESSION['cache']['SHARE']['SHARES']['VALUES']['SHARE'] = [
    1 => ['ID' => 1, 'FILENAME' => 'shared/z.txt', 'SHORTFILENAME' => 'z.txt', 'PRIORITY' => 1, 'CHECKSUM' => 'abc', 'SIZE' => 1],
    2 => ['ID' => 2, 'FILENAME' => 'shared/a.txt', 'SHORTFILENAME' => 'a.txt', 'PRIORITY' => 1, 'CHECKSUM' => 'def', 'SIZE' => 1],
];
check((new Share())->get_fileids('shared') === [2, 1], 'Shared files were not sorted or selectable');

echo "Issue #4 checks passed\n";
