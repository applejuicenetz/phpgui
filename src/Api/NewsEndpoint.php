<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\NewsFeed;
use appleJuiceNETZ\GUI\VersionCheck;

final class NewsEndpoint extends Endpoint
{
    public function get(): array
    {
        $html = '';
        if (!empty($_ENV['GUI_SHOW_NEWS'])) $html = NewsFeed::load((string)(new Core())->getcoreversion()['VERSION']);
        return ['html' => $html, 'new_version' => VersionCheck::newer()];
    }
}
