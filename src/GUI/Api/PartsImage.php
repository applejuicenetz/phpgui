<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Api;

use appleJuiceNETZ\appleJuice\PartMapService;
use appleJuiceNETZ\GUI\PartsSvg;
use appleJuiceNETZ\GUI\Request;

final class PartsImage implements Endpoint
{
    public function handle(): void
    {
        header('Cache-Control: no-cache');
        $download = Request::get('dl_id');
        $user = Request::get('usr_id');
        $downloadId = ctype_digit($download) ? (int)$download : 0;
        $userId = ctype_digit($user) ? (int)$user : 0;
        if ($downloadId <= 0 && $userId <= 0) {
            http_response_code(400);
            return;
        }
        $map = (new PartMapService())->load($downloadId, $userId);
        if ($map === null) {
            http_response_code(404);
            return;
        }
        header('Content-Type: image/svg+xml; charset=utf-8');
        header("Content-Security-Policy: default-src 'none'; sandbox");
        echo PartsSvg::render($map['size'], $map['parts'], $map['download'], $map['transfers']);
    }
}
