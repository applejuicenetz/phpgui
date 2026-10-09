<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

/** Thrown by endpoints to answer with a machine-readable JSON error. */
final class ApiException extends \RuntimeException
{
    public function __construct(public readonly int $status, public readonly string $error)
    {
        parent::__construct($error);
    }
}
