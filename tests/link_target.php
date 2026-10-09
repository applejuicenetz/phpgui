<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $file = dirname(__DIR__) . '/src/' . str_replace(['appleJuiceNETZ\\', '\\'], ['', '/'], $class . '.php');
    if (is_file($file)) require_once $file;
});

use appleJuiceNETZ\GUI\LinkProcessor;

function expectTarget(?string $expected, string $input): void
{
    $actual = LinkProcessor::targetDirectory($input);
    if ($actual !== $expected) {
        throw new RuntimeException('Target directory ' . var_export($input, true) . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

expectTarget('', '');
expectTarget('', '  / ');
expectTarget('Linux', 'Linux');
expectTarget('Linux/ISOs', ' /Linux//ISOs/ ');
expectTarget('Linux/ISOs', 'Linux\\ISOs');
expectTarget('Ärger & Ü', 'Ärger & Ü');
expectTarget(null, '../etc');
expectTarget(null, 'a/../b');
expectTarget(null, 'C:evil');
expectTarget(null, "a\0b");
expectTarget(null, "a\nb");
expectTarget(null, str_repeat('a', 256));
echo "Link target directory: normalization and rejection passed\n";
