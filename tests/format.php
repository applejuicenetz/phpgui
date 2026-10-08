<?php

declare(strict_types=1);

use appleJuiceNETZ\GUI\Format;

const GUI_ROOT = __DIR__ . '/..';
spl_autoload_register(static function (string $class): void {
    $file = GUI_ROOT . '/src/' . str_replace(['appleJuiceNETZ\\', '\\'], ['', '/'], $class . '.php');
    if (is_file($file)) {
        require_once $file;
    }
});

// The language is cached per process, so each language runs in its own child process.
if (!isset($argv[1])) {
    foreach (['de', 'en'] as $language) {
        passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' ' . $language, $status);
        if ($status !== 0) {
            exit($status);
        }
    }
    echo "Format::compactCount passed\n";
    exit(0);
}

$language = $argv[1];
$_ENV['GUI_LANGUAGE'] = $language;
$cases = [
    'de' => [[0, '0'], [999, '999'], [1000, '1 Tausend'], [12345, '12,35 Tausend'], [999999, '1 Millionen'], [4191120, '4,19 Millionen'], [1500000000, '1,5 Milliarden'], ['4191120', '4,19 Millionen'], [2.5e12, '2.500 Milliarden']],
    'en' => [[999, '999'], [12345, '12.35 thousand'], [4191120, '4.19 million'], [1500000000, '1.5 billion'], [2.5e12, '2,500 billion']],
][$language];
foreach ($cases as [$input, $expected]) {
    $actual = Format::compactCount($input);
    if ($actual !== $expected) {
        throw new RuntimeException(sprintf('%s: compactCount(%s) returned "%s", expected "%s"', $language, $input, $actual, $expected));
    }
}
