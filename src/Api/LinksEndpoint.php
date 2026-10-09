<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\LinkProcessor;
use appleJuiceNETZ\GUI\Request;

final class LinksEndpoint extends Endpoint
{
    public function post(): array
    {
        $input = Request::str('ajfsp_link');
        $target = LinkProcessor::targetDirectory(Request::str('ajfsp_target'));
        if ($target === null) throw new ApiException(400, 'invalid_target');
        if (strlen($input) > 1048576 || LinkProcessor::parse($input) === []) throw new ApiException(400, 'invalid_link');
        return ['results' => LinkProcessor::submit($input, new Core(), $target)];
    }
}
