<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Plugins;
use appleJuiceNETZ\GUI\Request;
use appleJuiceNETZ\GUI\View;

/**
 * Lädt ein registriertes Plugin. Plugins liefern ihr HTML per echo; die Ausgabe wird
 * in die Seite eingebettet. Sie sehen $phpaj_show, $phpaj_ownurl und $lang.
 */
final class ExtrasController extends Controller
{
    public function handle(): Page
    {
        $show = Request::str('show');
        $plugins = new Plugins();
        $file = $show !== '' ? $plugins->resolve($show) : null;
        if ($file === null) {
            return new Page('pages/error404', [], $this->lang->System->pagetitle->{'404'}, status: 404);
        }

        $title = $show;
        foreach ($plugins->liste as $p) {
            if ($p[2] === $show) {
                $title = $p[0];
            }
        }

        $html = (static function (string $__file, string $phpaj_show, object $lang): string {
            $phpaj_ownurl = 'index.php?site=extras&show=' . rawurlencode($phpaj_show);
            ob_start();
            try {
                include $__file;

                return (string)ob_get_clean();
            } catch (\Throwable $t) {
                ob_end_clean();
                throw $t;
            }
        })($file, $show, $this->lang);

        return new Page('pages/extras', ['html' => $html, 'plugin_title' => $title], $title, ['extras.js']);
    }
}
