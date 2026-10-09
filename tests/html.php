<?php

declare(strict_types=1);
require __DIR__ . '/../src/GUI/Html.php';
use appleJuiceNETZ\GUI\Html;

$input = '<b id="aj-root" onclick="alert(1)" style="color:red">Title</b><p onmouseover="alert(1)">Text</p><a href="javascript:alert(1)">Bad</a><a href="https://example.org" onclick="alert(1)">Good</a>';
$output = Html::sanitize($input);
foreach (['onclick', 'onmouseover', 'style=', 'javascript:', 'id='] as $unsafe) {
    if (str_contains($output, $unsafe)) throw new RuntimeException('Unsafe HTML attribute: ' . $unsafe);
}
if (!str_contains($output, '<b>Title</b>') || !str_contains($output, 'noopener noreferrer')) throw new RuntimeException('Safe markup lost');
echo "HTML sanitization: attributes, duplicate root IDs and unsafe links passed\n";
