<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\GUI\Request;

final class SourcesEndpoint extends Endpoint
{
    /** Every source costs one Core request, so the share of the file a source has is only read for this many. */
    private const AVAILABILITY_LIMIT = 100;

    /** Share of the file (percent) the source has, from userpartlist: type -1 = available, 0 = missing. */
    private function availability(Core $core, int $userId): ?float
    {
        try {
            $map = $core->command('xml', 'userpartlist.xml?id=' . $userId);
        } catch (\Throwable) {
            return null;
        }
        $size = (float)array_key_first($map['FILEINFORMATION'] ?? []);
        if ($size <= 0) return null;
        $parts = $map['PART'] ?? [];
        ksort($parts, SORT_NUMERIC);
        $starts = array_keys($parts);
        $have = 0.0;
        foreach ($starts as $i => $start) {
            if ((int)$parts[$start]['TYPE'] === -1) $have += (float)($starts[$i + 1] ?? $size) - (float)$start;
        }
        return round($have / $size * 100, 1);
    }

    public function get(): array
    {
        $dl = new Downloads();
        $dl->refresh_cache();
        $id = Request::int('dl_id');
        if ($id <= 0 || empty($dl->cache['DOWNLOAD'][$id])) throw new ApiException(404, 'not_found');
        $d = $dl->download($id);
        $groups = [];
        $core = new Core();
        $lookups = 0;
        foreach (['active' => 'phpaj_ids_quellen_dl', 'queue' => 'phpaj_ids_quellen_queue', 'rest' => 'phpaj_ids_quellen_rest'] as $key => $field) {
            $rows = [];
            foreach ($d[$field] as $uid) {
                $u = $dl->user($uid);
                $span = (float)$u['DOWNLOADTO'] - (float)$u['DOWNLOADFROM'];
                $active = $key === 'active' && $span > 0;
                $status = (int)$u['STATUS'];
                $available = $status !== 1 && $lookups++ < self::AVAILABILITY_LIMIT ? $this->availability($core, (int)$uid) : null;
                $rows[] = [
                    'id' => (int)$uid,
                    'nick' => (string)$u['NICKNAME'],
                    'direct' => (int)$u['DIRECTSTATE'],
                    'os' => CoreData::osCode($u['OPERATINGSYSTEM'] ?? 0),
                    'status' => (int)$u['STATUS'],
                    'origin' => (int)$u['SOURCE'],
                    'available' => $available,
                    'chunk_size' => $u['DOWNLOADFROM'] >= 0 && $u['DOWNLOADTO'] >= 0 ? max(0, (int)$u['DOWNLOADTO'] - (int)$u['DOWNLOADFROM']) : 0,
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
