<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Request;

/** Teile-Ansicht eines Downloads oder einer Quelle (Bild stammt aus showparts). */
final class DownloadPartsController extends Controller
{
    public function handle(): Page
    {
        $dl = new Downloads();
        $dl->refresh_cache();
        $dlId = Request::int('dl_id');
        $usrId = Request::int('usr_id');
        if ($dlId > 0 && !empty($dl->cache['DOWNLOAD'][$dlId])) {
            $d = $dl->download($dlId);
            $title = $d['TEMPORARYFILENUMBER'] . '.data - ' . $d['FILENAME'];
            $image = 'index.php?api=parts&dl_id=' . $dlId;
            $back = 'index.php?site=downloads';
        } elseif ($usrId > 0 && !empty($dl->cache['USER'][$usrId])) {
            $u = $dl->user($usrId);
            $title = $u['NICKNAME'] . ' - ' . $u['FILENAME'];
            $image = 'index.php?api=parts&usr_id=' . $usrId;
            $back = 'index.php?site=dl_users&dl_id=' . (int)$u['DOWNLOADID'];
        } else {
            $this->redirect('index.php?site=downloads');
        }

        return new Page('pages/dl_parts', ['heading' => $title, 'image' => $image, 'back' => $back], $this->lang->System->pagetitle->dl_parts);
    }
}
