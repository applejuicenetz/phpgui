<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\GUI\Page;

/** Beendet den Core. Nur per POST mit CSRF-Token. */
final class KickCoreController extends Controller
{
    public function handle(): Page
    {
        if ($this->guardPost()) {
            $this->core->command('function', 'exitcore');
            session_unset();
            $this->redirect('index.php');
        }
        $this->redirect('index.php');
    }
}
