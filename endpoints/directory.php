<?php

declare(strict_types=1);

use appleJuiceNETZ\appleJuice\Share;

/**
 * Verzeichnisliste des Core als JSON für die Verzeichniswahl.
 * GET dir=<pfad> => {"dir": "...", "separator": "/", "entries": [{"path": "...", "name": "..."}]}
 */
header('Content-Type: application/json; charset=utf-8');

$dir = isset($_GET['dir']) && is_string($_GET['dir']) ? $_GET['dir'] : '';
$share = new Share();
$entries = [];
foreach ($share->directory($dir) as [$path, $name]) {
    $entries[] = ['path' => (string)$path, 'name' => (string)$name];
}
echo json_encode(['dir' => $dir, 'separator' => $_SESSION['SEPARATOR'] ?? '/', 'entries' => $entries]);
