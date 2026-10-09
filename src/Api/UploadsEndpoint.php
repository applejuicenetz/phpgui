<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\CoreSettings;

final class UploadsEndpoint extends Endpoint
{
    public function get(): array
    {
        $items = CoreData::uploads();
        $info = CoreData::information();
        return [
            'items' => $items,
            'speed' => (int)$info['UPLOADSPEED'],
            'max' => (int)(CoreSettings::read(new Core())['maxupload'] ?? 0),
            'slots_used' => count($items),
            'slots_max' => (int)($info['MAXUPLOADPOSITIONS'] ?? 0),
        ];
    }
}
