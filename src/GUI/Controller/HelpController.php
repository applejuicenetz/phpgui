<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\GUI\Page;

final class HelpController extends Controller
{
    public function handle(): Page
    {
        return new Page('pages/help', [], $this->lang->System->pagetitle->help);
    }
}
