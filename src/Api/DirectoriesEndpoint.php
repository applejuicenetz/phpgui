<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Request;

final class DirectoriesEndpoint extends Endpoint
{
    public function get(): array
    {
        $dir = Request::get('dir');
        $share = new Share();
        $entries = [];
        foreach ($share->directory($dir) as [$path, $name]) $entries[] = ['path' => (string)$path, 'name' => (string)$name];
        return ['dir' => $dir, 'separator' => $share->separator, 'entries' => $entries];
    }
}
