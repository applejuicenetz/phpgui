<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Exception;

/** Von Controllern geworfen, um mit 303 weiterzuleiten (Post/Redirect/Get). */
final class RedirectException extends \RuntimeException
{
    public function __construct(public readonly string $url)
    {
        parent::__construct($url);
    }
}
