<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Search;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\appleJuice\Uploads;

/**
 * Bereitet Core-Daten als flache Anzeigearrays auf. Templates und die JSON-API
 * nutzen dieselben Strukturen, damit Serverausgabe und Live-Updates übereinstimmen.
 */
final class ViewData
{
    public static function information(): array
    {
        static $cache = null;
        if ($cache === null) {
            $xml = (new \appleJuiceNETZ\appleJuice\Core())->command('xml', 'modified.xml?filter=informations');
            $cache = $xml['INFORMATION'][array_key_first($xml['INFORMATION'])];
        }
        return $cache;
    }

    /** Data blocks shared by live JSON requests. */
    public static function live(string $type): array
    {
        return match ($type) {
            'status' => self::liveStatus(),
            'downloads' => self::liveTransfers(false),
            'uploads' => self::liveTransfers(true),
            'dashboard' => self::liveDashboard(),
            'search' => self::liveSearch(),
        };
    }

    /** Number of running transfers, used by the navigation badges. */
    public static function activeCounts(): array
    {
        $downloads = new Downloads();
        $downloads->refresh_cache();
        $loading = 0;
        foreach ($downloads->cache['DOWNLOAD'] ?? [] as $download) {
            $loading += ($download['phpaj_STATUS'] ?? '') === '0_2' ? 1 : 0;
        }
        $uploads = new Uploads();
        $uploads->refresh_cache();

        return ['downloads_active' => $loading, 'uploads_active' => (int)($uploads->cache['phpaj_ul'] ?? 0)];
    }

    private static function liveStatus(): array
    {
        $info = self::information();
        return self::activeCounts() + [
            'credits' => Format::bytes($info['CREDITS']),
            'credits_negative' => (float)$info['CREDITS'] < 0,
            'dl_speed_raw' => (float)$info['DOWNLOADSPEED'],
            'ul_speed_raw' => (float)$info['UPLOADSPEED'],
        ];
    }

    private static function liveTransfers(bool $upload): array
    {
        static $settings = null;
        $settings ??= CoreSettings::read(new \appleJuiceNETZ\appleJuice\Core());
        $rows = $upload ? self::uploads('name') : self::downloads('name');
        $items = [];
        foreach ($rows as $row) {
            $items[$row['id']] = $row;
        }
        $max = (int)$settings[$upload ? 'maxupload' : 'maxdownload'];
        $note = '';
        if ($upload) {
            $uploads = new Uploads();
            $used = count($uploads->cache['IDS']['VALUES']['UPLOADID'] ?? []);
            $slots = (int)(self::information()['MAXUPLOADPOSITIONS'] ?? 0);
            $percent = $slots > 0 ? (string)(int)round($used / $slots * 100) : '?';
            $note = strtr(Format::lang()->Uploads->limit, ['{percent}' => $percent]);
        }
        return [
            'items' => $items,
            'speed_raw' => array_sum(array_column($rows, 'speed_raw')),
            'max_raw' => $max,
            'max_text' => $max > 0 ? Format::bytes($max, 2, true) : '',
        ] + ($upload ? ['count' => count($rows), 'slot_text' => $note] : ['counts' => self::downloadCounts($rows)]);
    }

    private static function liveDashboard(): array
    {
        $info = self::information();
        $servers = new \appleJuiceNETZ\appleJuice\Server();
        $uploads = new Uploads();
        $uploads->refresh_cache();
        $connected = $servers->netstats['timeconnected'];
        return self::liveStatus() + [
            'downloads' => self::downloadCounts(self::downloads()),
            'uploads' => (int)($uploads->cache['phpaj_ul'] ?? 0),
            'dl_speed' => Format::speed($info['DOWNLOADSPEED']),
            'ul_speed' => Format::speed($info['UPLOADSPEED']),
            'session_dl' => Format::bytes($info['SESSIONDOWNLOAD']),
            'session_ul' => Format::bytes($info['SESSIONUPLOAD']),
            'connections' => $info['OPENCONNECTIONS'] ?? '',
            'connected' => is_numeric($connected) ? sprintf('%dh %dmin', intdiv((int)$connected, 3600), intdiv((int)$connected % 3600, 60)) : '?',
        ];
    }

    private static function liveSearch(): array
    {
        $data = self::search();
        return [
            'total' => $data['total'],
            'searches' => $data['searches'],
            'entries' => $data['entries'],
            'running' => (bool)array_filter($data['searches'], static fn($search) => $search['running']),
        ];
    }


    /**
     * @return list<array<string,mixed>> Downloads; Reihenfolge nach $sort/$dir.
     */
    public static function downloads(string $sort = 'status', ?int $dir = null): array
    {
        $dl = new Downloads();
        $dl->refresh_cache();
        $rows = [];
        foreach (array_keys($dl->subdirs ?? []) as $subdir) {
            foreach (array_keys($dl->ids($sort, $subdir, $dir)) as $id) {
                $d = $dl->download($id);
                $size = (float)$d['SIZE'];
                $rest = (float)$d['phpaj_REST'];
                [$statusKey, $statusText] = Format::downloadStatus((string)$d['phpaj_STATUS']);
                $rows[] = [
                    'id' => (int)$id,
                    'name' => (string)$d['FILENAME'],
                    'size' => Format::bytes($size),
                    'loaded' => Format::bytes(max(0.0, $size - $rest)),
                    'part' => trim((string)subs::parts((string)$d['FILENAME']), ' |'),
                    'status' => $statusKey,
                    'status_text' => $statusText,
                    'percent' => $size > 0 ? round((float)$d['phpaj_DONE'], 2) : 0.0,
                    'rest' => Format::bytes($rest),
                    'eta' => Format::eta($rest, (float)$d['phpaj_dl_speed']),
                    'speed' => Format::speed($d['phpaj_dl_speed']),
                    'speed_raw' => (int)$d['phpaj_dl_speed'],
                    'pdl' => Format::pdl($d['POWERDOWNLOAD']),
                    'sources_active' => (int)$d['phpaj_quellen_dl'],
                    'sources_queue' => (int)$d['phpaj_quellen_queue'],
                    'sources_total' => (int)$d['phpaj_quellen_gesamt'],
                    'target' => (string)($d['TARGETDIRECTORY'] ?? ''),
                ];
            }
        }

        return $rows;
    }

    /** @return list<array<string,mixed>> */
    public static function uploads(string $sort = 'status', ?int $dir = null): array
    {
        $ul = new Uploads();
        $ul->refresh_cache();
        $shares = new Share();
        $ids = array_merge($ul->cache['phpaj_ids_ul'] ?? [], $ul->cache['phpaj_ids_queue'] ?? []);
        $keys = [];
        foreach ($ids as $id) {
            $u = $ul->get_upload($id);
            $keys[$id] = $sort === 'name'
                ? ($shares->get_file($u['SHAREID'])['SHORTFILENAME'] ?? '')
                : (int)$u['phpaj_STATUS_SORT'];
        }
        $sort === 'name'
            ? (($dir ?? 0) === 0 ? asort($keys, SORT_STRING) : arsort($keys, SORT_STRING))
            : (($dir ?? 0) === 0 ? asort($keys, SORT_NUMERIC) : arsort($keys, SORT_NUMERIC));

        $now = (float)($ul->cache['TIME']['VALUES']['CDATA'] ?? 0);
        $rows = [];
        foreach (array_keys($keys) as $id) {
            $u = $ul->get_upload($id);
            $share = $shares->get_file($u['SHAREID']);
            $active = (string)$u['STATUS'] === '1';
            $span = (float)$u['UPLOADTO'] - (float)$u['UPLOADFROM'];
            $pdl = '';
            if ((float)$u['PRIORITY'] > (float)($share['PRIORITY'] ?? 0)) {
                $pdl = '(' . ((((float)$u['PRIORITY'] - (float)$share['PRIORITY']) - 10) / 10) . ') ';
            }
            if ($active && $span > 0) {
                $done = ((float)$u['ACTUALUPLOADPOSITION'] - (float)$u['UPLOADFROM']) / $span * 100;
                $label = number_format($done, 2) . '%';
                $sub = Format::bytes((float)$u['ACTUALUPLOADPOSITION'] - (float)$u['UPLOADFROM']) . ' / ' . Format::bytes($span);
                $percent = round($done, 2);
            } else {
                $age = isset($u['LASTCONNECTION']) ? max(0, ($now - (float)$u['LASTCONNECTION']) / 1000) : 0;
                $label = sprintf('%dmin %02ds', (int)($age / 60), (int)$age % 60);
                $sub = Format::bytes($span);
                $percent = 0.0;
            }
            [$statusKey, $statusText] = Format::uploadStatus((string)$u['STATUS']);
            $rows[] = [
                'id' => (int)$id,
                'name' => (string)($share['SHORTFILENAME'] ?? ''),
                'nick' => (string)($u['NICK'] ?? ''),
                'priority' => $pdl . $u['PRIORITY'],
                'status' => $statusKey,
                'status_text' => $statusText,
                'active' => $active,
                'label' => $label,
                'sub' => $sub,
                'percent' => $percent,
                'speed' => Format::speed($u['SPEED']),
                'speed_raw' => (int)$u['SPEED'],
                'direct' => Icons::directState($active ? $u['DIRECTSTATE'] : 'WAIT'),
                'os' => Icons::os($u['OPERATINGSYSTEM'] ?? 0),
            ];
        }

        return $rows;
    }

    /**
     * Suchen und deren Ergebnisse.
     *
     * @return array{searches:array<int|string,array<string,mixed>>,entries:list<array<string,mixed>>,total:int}
     */
    public static function search(string $sort = 'name', ?int $dir = null): array
    {
        $search = new Search();
        $search->refresh_cache();
        $search->process_results();
        $searches = [];
        foreach ($search->cache['SEARCH'] ?? [] as $sid => $s) {
            $total = (int)($s['SUMSEARCHES'] ?? 0) + (int)($s['OPENSEARCHES'] ?? 0);
            $running = ($s['RUNNING'] ?? '') === 'true';
            $searches[$sid] = [
                'id' => (string)$sid,
                'text' => (string)$s['SEARCHTEXT'],
                'running' => $running,
                'found' => (int)($s['phpaj_FOUNDFILES'] ?? 0),
                'progress' => $total > 0 ? round((int)$s['SUMSEARCHES'] * 100 / $total, 2) : 100.0,
            ];
        }
        $entries = [];
        if (!empty($search->cache['SEARCHENTRY'])) {
            foreach (array_keys($search->sortieren($sort, $dir)) as $eid) {
                $e = $search->cache['SEARCHENTRY'][$eid];
                $name = (string)$e['phpaj_FILENAME'];
                $link = 'ajfsp://file|' . $name . '|' . $e['CHECKSUM'] . '|' . $e['SIZE'] . '/';
                $entries[] = [
                    'id' => (int)$eid,
                    'search' => (string)$e['SEARCHID'],
                    'name' => $name,
                    'size' => Format::bytes($e['SIZE']),
                    'size_raw' => (float)$e['SIZE'],
                    'format' => (string)$e['phpaj_FORMAT'],
                    'sources' => (int)$e['phpaj_COUNT'],
                    'link' => $link,
                    'info' => !empty($_ENV['REL_INFO']) ? sprintf($_ENV['REL_INFO'], $link) : '',
                ];
            }
        }

        return ['searches' => $searches, 'entries' => $entries, 'total' => (int)($search->cache['SEARCHENTRY_count'] ?? 0)];
    }

    /** Anzahl aktiver und gesamter Downloads für das Dashboard (wie zuvor: "aktiv/alle ohne fertige"). */
    public static function downloadCounts(array $rows): string
    {
        $all = 0;
        $active = 0;
        foreach ($rows as $r) {
            if ($r['status'] === 'done') {
                continue;
            }
            $all++;
            if ($r['status'] === 'loading') {
                $active++;
            }
        }

        return $rows === [] ? '0' : $active . '/' . $all;
    }
}
