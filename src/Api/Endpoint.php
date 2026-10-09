<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

/** One JSON route. Unsupported methods answer 405. */
abstract class Endpoint
{
    /** Public endpoints work without a Core login. */
    public const PUBLIC = false;

    /** @return array<string,mixed> */
    public function get(): array
    {
        throw new ApiException(405, 'method_not_allowed');
    }

    /** @return array<string,mixed> */
    public function post(): array
    {
        throw new ApiException(405, 'method_not_allowed');
    }
}
