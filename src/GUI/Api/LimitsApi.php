<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Api;

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\CoreSettings;
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Request;

final class LimitsApi implements Endpoint
{
    public function handle(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        if (!Request::isPost() || !Csrf::valid()) {
            http_response_code(403);
            echo json_encode(['error' => 'csrf']);
            return;
        }
        $key = match (Request::get('action')) {
            'set_maxdl' => 'MaxDownload',
            'set_maxul' => 'MaxUpload',
            default => null,
        };
        if ($key === null) {
            http_response_code(400);
            echo json_encode(['error' => 'unknown_action']);
            return;
        }
        CoreSettings::setLimit(new Core(), $key, Request::int('value'));
        echo json_encode(['ok' => true]);
    }
}
