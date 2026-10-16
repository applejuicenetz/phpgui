<?php

declare(strict_types=1);

$source = file_get_contents(__DIR__ . '/../public/assets/js/components/login.js');
foreach (['host' => ['hostHelp', 'url_info'], 'password' => ['passwordHelp', 'password_info']] as $field => [$state, $key]) {
    foreach ([
        $state . '=ref(false)',
        ':aria-expanded="' . $state . '"',
        'aria-controls="login-' . $field . '-help"',
        '@click="' . $state . '=!' . $state . '"',
        'id="login-' . $field . '-help" v-show="' . $state . '"',
        "{{t('Login." . $key . "')}}",
    ] as $contract) {
        if (!str_contains($source, $contract)) {
            throw new RuntimeException('Missing login help contract: ' . $contract);
        }
    }
    if (str_contains($source, ':title="t(\'Login.' . $key . '\')"')) {
        throw new RuntimeException('Login help must not rely on native title tooltips');
    }
}
echo "Login help: independent toggles, linked help text and accessibility contracts passed\n";
