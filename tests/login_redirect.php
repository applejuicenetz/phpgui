<?php

declare(strict_types=1);
require __DIR__ . '/HttpClient.php';
[$base, $core] = testOptions();
$curl = curl_init(rtrim($base, '/') . '/index.php');
curl_setopt_array($curl, [CURLOPT_POSTFIELDS => http_build_query(['host' => $core, 'cpass' => '']), CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30]);
check(curl_exec($curl) !== false, 'Login request');
check(curl_getinfo($curl, CURLINFO_RESPONSE_CODE) === 303, 'Successful login must redirect to GET');
echo "Login POST redirects to GET: passed\n";
