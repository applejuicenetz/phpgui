<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Api;

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Request;

final class ShareDirectoryApi implements Endpoint
{
    public function handle(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        $dir = Request::get('dir');
        $entries = [];
        foreach ((new Share())->directory($dir) as [$path, $name]) {
            $entries[] = ['path' => (string)$path, 'name' => (string)$name];
        }
        echo json_encode(['dir' => $dir, 'separator' => $_SESSION['SEPARATOR'] ?? '/', 'entries' => $entries]);
    }
}
