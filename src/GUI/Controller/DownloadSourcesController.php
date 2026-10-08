<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\GUI\Format;
use appleJuiceNETZ\GUI\Icons;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Request;
use appleJuiceNETZ\GUI\subs;

/** Quellen eines Downloads (aktiv, Warteschlange, Rest). */
final class DownloadSourcesController extends Controller
{
    public function handle(): Page
    {
        $dl = new Downloads();
        $dl->refresh_cache();
        $id = Request::int('dl_id');
        if ($id <= 0 || empty($dl->cache['DOWNLOAD'][$id])) {
            $this->redirect('index.php?site=downloads');
        }
        $d = $dl->download($id);
        $size = (float)$d['SIZE'];
        $rest = (float)$d['phpaj_REST'];
        [$statusKey, $statusText] = Format::downloadStatus((string)$d['phpaj_STATUS']);

        $groups = [];
        foreach (['active' => 'phpaj_ids_quellen_dl', 'queue' => 'phpaj_ids_quellen_queue', 'rest' => 'phpaj_ids_quellen_rest'] as $key => $field) {
            $rows = [];
            foreach ($d[$field] as $uid) {
                $u = $dl->user($uid);
                $span = (float)$u['DOWNLOADTO'] - (float)$u['DOWNLOADFROM'];
                $active = $key === 'active' && $span > 0;
                $rows[] = [
                    'id' => (int)$uid,
                    'nick' => (string)$u['NICKNAME'],
                    'direct' => Icons::directState($u['DIRECTSTATE']),
                    'os' => Icons::os($u['OPERATINGSYSTEM'] ?? 0),
                    'status' => $active ? $this->lang->Downloads->dl_users->status_7 : Format::sourceStatus($u['STATUS']),
                    'origin' => Format::sourceOrigin($u['SOURCE']),
                    'percent' => $active ? round(((float)$u['ACTUALDOWNLOADPOSITION'] - (float)$u['DOWNLOADFROM']) / $span * 100, 2) : null,
                    'speed' => Format::speed($u['SPEED']),
                    'pdl' => Format::pdl($u['POWERDOWNLOAD']),
                    'version' => (string)($u['VERSION'] ?? ''),
                    'queue' => (int)($u['QUEUEPOSITION'] ?? 0),
                ];
            }
            $groups[$key] = $rows;
        }

        return new Page('pages/dl_users', [
            'id' => $id,
            'name' => (string)$d['FILENAME'],
            'size' => Format::bytes($size),
            'part' => trim((string)subs::parts((string)$d['FILENAME']), ' |'),
            'status' => $statusKey,
            'status_text' => $statusText,
            'percent' => round((float)$d['phpaj_DONE'], 2),
            'rest' => Format::bytes($rest),
            'eta' => Format::eta($rest, (float)$d['phpaj_dl_speed']),
            'speed' => Format::speed($d['phpaj_dl_speed']),
            'pdl' => Format::pdl($d['POWERDOWNLOAD']),
            'groups' => $groups,
        ], $this->lang->System->pagetitle->dl_users);
    }
}
