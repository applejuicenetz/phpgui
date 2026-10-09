<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\CoreSettings;
use appleJuiceNETZ\GUI\Request;

final class LimitsEndpoint extends Endpoint
{
    public function post(): array
    {
        $key = match (Request::str('action')) {
            'set_maxdl' => 'MaxDownload', 'set_maxul' => 'MaxUpload', default => null,
        };
        if ($key === null) throw new ApiException(400, 'unknown_action');
        $value = Request::str('value');
        if (!ctype_digit($value) || (float)$value > PHP_INT_MAX) throw new ApiException(400, 'invalid_value');
        CoreSettings::setLimit(new Core(), $key, (int)$value);
        return ['ok' => true];
    }
}
