<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Api;

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\NewsFeed;

final class NewsApi implements Endpoint
{
    public function handle(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        if (empty($_ENV['GUI_SHOW_NEWS'])) {
            echo json_encode(['html' => '']);
            return;
        }
        $version = (string)(new Core())->getcoreversion()['VERSION'];
        echo json_encode(['html' => NewsFeed::load($version)]);
    }
}
