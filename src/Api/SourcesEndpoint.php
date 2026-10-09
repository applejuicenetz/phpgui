<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\GUI\Request;

final class SourcesEndpoint extends Endpoint
{
    public function get(): array
    {
        $dl = new Downloads();
        $dl->refresh_cache();
        $id = Request::int('dl_id');
        if ($id <= 0 || empty($dl->cache['DOWNLOAD'][$id])) throw new ApiException(404, 'not_found');
        $d = $dl->download($id);
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
                    'direct' => (int)$u['DIRECTSTATE'],
                    'os' => CoreData::osCode($u['OPERATINGSYSTEM'] ?? 0),
                    'status' => (int)$u['STATUS'],
                    'origin' => (int)$u['SOURCE'],
                    'percent' => $active ? round(((float)$u['ACTUALDOWNLOADPOSITION'] - (float)$u['DOWNLOADFROM']) / $span * 100, 2) : null,
                    'speed' => (int)$u['SPEED'],
                    'pdl' => CoreData::pdl($u['POWERDOWNLOAD']),
                    'version' => (string)($u['VERSION'] ?? ''),
                    'queue' => (int)($u['QUEUEPOSITION'] ?? 0),
                ];
            }
            $groups[$key] = $rows;
        }
        return [
            'id' => $id,
            'name' => (string)$d['FILENAME'],
            'size' => (int)$d['SIZE'],
            'status' => CoreData::downloadStatus((string)$d['phpaj_STATUS']),
            'percent' => round((float)$d['phpaj_DONE'], 2),
            'rest' => (int)$d['phpaj_REST'],
            'speed' => (int)$d['phpaj_dl_speed'],
            'pdl' => CoreData::pdl($d['POWERDOWNLOAD']),
            'groups' => $groups,
        ];
    }
}
