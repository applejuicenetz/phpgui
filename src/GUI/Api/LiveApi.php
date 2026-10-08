<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Api;

use appleJuiceNETZ\GUI\Request;
use appleJuiceNETZ\GUI\ViewData;

final class LiveApi implements Endpoint
{
    public function handle(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        $result = [];
        foreach (explode(',', Request::get('type', 'status')) as $type) {
            $type = trim($type);
            if (in_array($type, ['status', 'downloads', 'uploads', 'dashboard', 'search'], true)) {
                $result[$type] = ViewData::live($type);
            }
        }
        echo json_encode($result);
    }
}
