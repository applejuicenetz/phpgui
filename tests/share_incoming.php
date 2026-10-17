<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $file = dirname(__DIR__) . '/src/' . str_replace(['appleJuiceNETZ\\', '\\'], ['', '/'], $class . '.php');
    if (is_file($file)) require_once $file;
});

use appleJuiceNETZ\appleJuice\Share;

function expect(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

$_SESSION = [];
$share = new Share();
$settings = new ReflectionProperty(Share::class, 'dirxml');
foreach ([
    ['/', '/data/incoming/', '/data/incoming', false],
    ['/', '/data/incoming/', '/data/incoming///', false],
    ['/', '/data/incoming/', '/data/incoming/child', true],
    ['/', '/data/incoming/', '/data/Incoming', true],
    ['/', '/data/incoming/', '/data/other', true],
    ['\\', 'C:\\Incoming\\', 'c:\\incoming', false],
    ['\\', 'C:\\Incoming\\', 'C:\\Other', true],
] as [$separator, $incoming, $name, $expected]) {
    $share->separator = $separator;
    $settings->setValue($share, ['INCOMINGDIRECTORY' => ['VALUES' => ['CDATA' => $incoming]]]);
    expect($share->canRemoveDirectory($name) === $expected, 'Incoming removal policy: ' . $name);
}

$endpoint = file_get_contents(dirname(__DIR__) . '/src/Api/SharesEndpoint.php');
expect(str_contains($endpoint, "'can_remove' => \$share->canRemoveDirectory"), 'Expose removal policy');
expect(str_contains($endpoint, "\$name === '' || !\$share->canRemoveDirectory(\$name)"), 'Reject incoming removal in API');
$template = file_get_contents(dirname(__DIR__) . '/public/assets/js/pages/shares.js');
expect(str_contains($template, '<button v-if="dir.can_remove"'), 'Hide incoming remove button');
expect(str_contains($template, '<span v-if="!dir.can_remove" class="tag">'), 'Incoming uses temp-style badge');
foreach (['de', 'en'] as $language) {
    $strings = json_decode(file_get_contents(dirname(__DIR__) . '/language/' . $language . '.json'), true, 512, JSON_THROW_ON_ERROR);
    expect(!empty($strings['Share']['incoming']), 'Localized incoming badge');
}
echo "Share incoming: removal policy, API guards and badge contracts passed\n";
