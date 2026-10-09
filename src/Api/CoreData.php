<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Search;
use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\GUI\subs;

/** Turns the Core's legacy XML structures into flat arrays with raw values (no formatting, no translation). */
final class CoreData
{
    public static function downloadStatus(string $status): string
    {
        return match ($status) {
            '0_1' => 'searching',
            '0_2' => 'loading',
            '14' => 'done',
            '18' => 'paused',
            '17' => 'canceled',
            default => 'unknown',
        };
    }

    public static function uploadStatus(int $status): string
    {
        return match ($status) {
            1 => 'active',
            2 => 'queue',
            5, 6 => 'connecting',
            7 => 'failed',
            default => 'unknown',
        };
    }

    /** Displayed power download factor for a Core value (Core: 0 = 1.0). */
    public static function pdl(int|string $coreValue): float
    {
        return ((int)$coreValue + 10) / 10;
    }

    /** Operating system name or code of the Core / a source as the numeric code 0..3. */
    public static function osCode(string|int $value): int
    {
        return match ((string)$value) {
            '1', 'Windows' => 1,
            '2', 'Linux' => 2,
            '3', 'Mac' => 3,
            default => 0,
        };
    }

    public static function information(): array
    {
        $xml = (new Core())->command('xml', 'modified.xml?filter=informations');

        return $xml['INFORMATION'][array_key_first($xml['INFORMATION'])];
    }

    /** @return array{downloads_active:int,uploads_active:int} */
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

    /** @return list<array<string,mixed>> grouped by target directory, each group sorted by status */
    public static function downloads(): array
    {
        $dl = new Downloads();
        $dl->refresh_cache();
        $rows = [];
        foreach (array_keys($dl->subdirs) as $subdir) {
            foreach (array_keys($dl->ids('status', (string)$subdir, null)) as $id) {
                $d = $dl->download($id);
                $size = (float)$d['SIZE'];
                $rest = (float)$d['phpaj_REST'];
                $part = subs::parts((string)$d['FILENAME']);
                $rows[] = [
                    'id' => (int)$id,
                    'name' => (string)$d['FILENAME'],
                    'size' => (int)$size,
                    'loaded' => (int)max(0.0, $size - $rest),
                    'rest' => (int)$rest,
                    'percent' => $size > 0 ? round((float)$d['phpaj_DONE'], 2) : 0.0,
                    'status' => self::downloadStatus((string)$d['phpaj_STATUS']),
                    'speed' => (int)$d['phpaj_dl_speed'],
                    'pdl' => self::pdl($d['POWERDOWNLOAD']),
                    'sources_active' => (int)$d['phpaj_quellen_dl'],
                    'sources_queue' => (int)$d['phpaj_quellen_queue'],
                    'sources_total' => (int)$d['phpaj_quellen_gesamt'],
                    'target' => (string)($d['TARGETDIRECTORY'] ?? ''),
                    'part' => $part === null ? null : (int)substr($part, strrpos($part, ' ') + 1),
                ];
            }
        }

        return $rows;
    }

    /** @return list<array<string,mixed>> */
    public static function uploads(): array
    {
        $ul = new Uploads();
        $ul->refresh_cache();
        $shares = new Share();
        $ids = array_merge($ul->cache['phpaj_ids_ul'] ?? [], $ul->cache['phpaj_ids_queue'] ?? []);
        $now = (float)($ul->cache['TIME']['VALUES']['CDATA'] ?? 0);
        $rows = [];
        foreach ($ids as $id) {
            $u = $ul->get_upload($id);
            $share = $shares->get_file($u['SHAREID']);
            $status = (int)$u['STATUS'];
            $active = $status === 1;
            $span = (float)$u['UPLOADTO'] - (float)$u['UPLOADFROM'];
            $bonus = null;
            if ((float)$u['PRIORITY'] > (float)($share['PRIORITY'] ?? 0)) {
                $bonus = (((float)$u['PRIORITY'] - (float)$share['PRIORITY']) - 10) / 10;
            }
            $done = $active && $span > 0 ? ((float)$u['ACTUALUPLOADPOSITION'] - (float)$u['UPLOADFROM']) : null;
            $rows[] = [
                'id' => (int)$id,
                'name' => (string)($share['SHORTFILENAME'] ?? ''),
                'nick' => (string)($u['NICK'] ?? ''),
                'priority' => (int)$u['PRIORITY'],
                'pdl' => $bonus,
                'status' => self::uploadStatus($status),
                'status_code' => $status,
                'active' => $active,
                'span' => (int)$span,
                'done' => $done === null ? null : (int)$done,
                'percent' => $done === null ? 0.0 : round($done / $span * 100, 2),
                'age' => isset($u['LASTCONNECTION']) ? (int)max(0, ($now - (float)$u['LASTCONNECTION']) / 1000) : 0,
                'speed' => (int)$u['SPEED'],
                'direct' => $active ? (int)$u['DIRECTSTATE'] : 0,
                'os' => self::osCode($u['OPERATINGSYSTEM'] ?? 0),
            ];
        }

        return $rows;
    }

    /**
     * @return array{searches:list<array<string,mixed>>,entries:list<array<string,mixed>>,total:int}
     */
    public static function search(): array
    {
        $search = new Search();
        $search->refresh_cache();
        $search->process_results();
        $searches = [];
        foreach ($search->cache['SEARCH'] ?? [] as $sid => $s) {
            $total = (int)($s['SUMSEARCHES'] ?? 0) + (int)($s['OPENSEARCHES'] ?? 0);
            $searches[] = [
                'id' => (string)$sid,
                'text' => (string)$s['SEARCHTEXT'],
                'running' => ($s['RUNNING'] ?? '') === 'true',
                'found' => (int)($s['phpaj_FOUNDFILES'] ?? 0),
                'progress' => $total > 0 ? round((int)$s['SUMSEARCHES'] * 100 / $total, 2) : 100.0,
            ];
        }
        $entries = [];
        foreach ($search->cache['SEARCHENTRY'] ?? [] as $eid => $e) {
            $name = (string)$e['phpaj_FILENAME'];
            $entries[] = [
                'id' => (int)$eid,
                'search' => (string)$e['SEARCHID'],
                'name' => $name,
                'size' => (int)$e['SIZE'],
                'format' => (string)$e['phpaj_FORMAT'],
                'sources' => (int)$e['phpaj_COUNT'],
                'link' => 'ajfsp://file|' . $name . '|' . $e['CHECKSUM'] . '|' . $e['SIZE'] . '/',
            ];
        }

        return ['searches' => $searches, 'entries' => $entries, 'total' => (int)($search->cache['SEARCHENTRY_count'] ?? 0)];
    }

    public static function relInfo(): string
    {
        return (string)($_ENV['REL_INFO'] ?? '');
    }
}
