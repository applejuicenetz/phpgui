<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $file = dirname(__DIR__) . '/src/' . str_replace(['appleJuiceNETZ\\', '\\'], ['', '/'], $class . '.php');
    if (is_file($file)) require_once $file;
});

use appleJuiceNETZ\appleJuice\Share;

function must(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

must(Share::matchesFilter('/music/Rock/Song.mp3', 'rock'), 'Case-insensitive match on full path');
must(Share::matchesFilter('/music/Ärger.mp3', 'ÄRGER'), 'Multibyte case-insensitive match');
must(Share::matchesFilter('/video/film.avi', ''), 'Empty filter matches all');
must(!Share::matchesFilter('/video/film.avi', 'xyz'), 'Non-matching filter');

$_SESSION = [];
$share = new Share();
$inDirectory = new ReflectionMethod(Share::class, 'inDirectory');
$file = ['FILENAME' => '/music/Rock/Song.mp3'];
must(!$inDirectory->invoke($share, $file, '/music'), 'Without filter only direct children');
must($inDirectory->invoke($share, $file, '/music', true), 'Filter searches subtree');
must($inDirectory->invoke($share, $file, '/music/Rock'), 'Direct child');
must(!$inDirectory->invoke($share, $file, '/mus', true), 'Prefix must end at a separator');
must($inDirectory->invoke($share, ['FILENAME' => 'C:\\a\\b\\c.txt'], 'C:\\a', true), 'Windows separators');
echo "Share filter: matching and subtree scope passed\n";
