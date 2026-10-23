<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/Kernel.php';

use appleJuiceNETZ\Kernel;

function check(bool $condition): void
{
    if (!$condition) throw new RuntimeException('Environment validation failed');
}

foreach (['', 'bad', '0', '-1', '65536', '1.5', []] as $port) {
    check(Kernel::validateEnvironment(['CORE_PORT' => $port])['CORE_PORT'] === 9851);
}
foreach ([1, 65535, '19851'] as $port) {
    check(Kernel::validateEnvironment(['CORE_PORT' => $port])['CORE_PORT'] === (int)$port);
}
foreach (['file:///etc/passwd', 'javascript:alert(1)', 'https://user:pass@example.com', 'bad', []] as $url) {
    check(Kernel::validateEnvironment(['FAQ_URL' => $url])['FAQ_URL'] === '');
}
$env = Kernel::validateEnvironment([
    'CORE_HOST' => 'http://core-mock', 'NEWS_URL' => 'https://example.com/%s?q=%20',
    'GUI_SHOW_NEWS' => 'false', 'GUI_SHOW_SHARE' => 'off', 'TOP_SHOW_PERMALINK' => 'yes',
    'TZ' => 'bad', 'GUI_LANGUAGE' => 'bad', 'GUI_REFRESH_INTERVAL' => '0',
    'ALLOWED_SERVERMSG_TAGS' => '<script><B><a onclick="x"><br>',
]);
check($env['CORE_HOST'] === 'http://core-mock');
check($env['NEWS_URL'] === 'https://example.com/%s?q=%20');
check($env['GUI_SHOW_NEWS'] === false && $env['GUI_SHOW_SHARE'] === false && $env['TOP_SHOW_PERMALINK'] === true);
check($env['TZ'] === 'Europe/Berlin' && $env['GUI_LANGUAGE'] === 'de' && $env['GUI_REFRESH_INTERVAL'] === 2);
check($env['ALLOWED_SERVERMSG_TAGS'] === '<b><br>');
check(Kernel::validateEnvironment(['NEWS_URL' => 'https://example.com/%d'])['NEWS_URL'] === '');
check(Kernel::validateEnvironment(['TZ' => 'UTC'])['TZ'] === 'UTC');
check((Kernel::validateEnvironment([])['GUI_REMEMBER_LOGIN'] ?? null) === false);
foreach (['1', 'true', 'on', 'yes', true] as $value) {
    check((Kernel::validateEnvironment(['GUI_REMEMBER_LOGIN' => $value])['GUI_REMEMBER_LOGIN'] ?? null) === true);
}
foreach (['0', 'false', 'off', 'no', '', 'invalid', false, []] as $value) {
    check((Kernel::validateEnvironment(['GUI_REMEMBER_LOGIN' => $value])['GUI_REMEMBER_LOGIN'] ?? null) === false);
}
print "Environment validation passed\n";
