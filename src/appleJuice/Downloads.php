<?php

declare(strict_types=1);

namespace appleJuiceNETZ\appleJuice;

use appleJuiceNETZ\GUI\subs;

class Downloads
{
    public array $cache;
    public array $subdirs = [];
    private Core $core;

    public function __construct()
    {
        $_SESSION['cache']['DOWNLOADS'] ??= [];
        $this->cache =& $_SESSION['cache']['DOWNLOADS'];
        $this->core = new Core();
    }

    public function time(): string
    {
        return date('j.n.y - H:i:s', (int)((float)($this->cache['TIME']['VALUES']['CDATA'] ?? 0) / 1000));
    }

    public function refresh_cache(): void
    {
        unset($this->cache['IDS']);
        $timestamp = $this->cache['LASTTIMESTAMP'] ?? 0;
        $this->cache = $this->core->command('xml', 'modified.xml?timestamp=' . $timestamp . '&filter=down;ids;user', $this->cache);
        $this->cache['LASTTIMESTAMP'] = $this->cache['TIME']['VALUES']['CDATA'];
        $this->process_sources();
    }

    public function process_sources(): void
    {
        $this->subdirs = [];
        foreach (array_keys($this->cache['DOWNLOAD'] ?? []) as $id) {
            if (empty($this->cache['IDS']['VALUES']['DOWNLOADID'][$id])) {
                unset($this->cache['DOWNLOAD'][$id]);
                continue;
            }
            $download =& $this->cache['DOWNLOAD'][$id];
            foreach (['gesamt', 'dl', 'queue'] as $group) {
                $download['phpaj_quellen_' . $group] = 0;
            }
            foreach (['queue', 'dl', 'rest'] as $group) {
                $download['phpaj_ids_quellen_' . $group] = [];
            }
            $download['phpaj_dl_speed'] = 0;
            $download['phpaj_loading_parts'] = [];
            $download['phpaj_STATUS'] = (string)$download['STATUS'];
            if ((string)$download['STATUS'] === '14') $download['READY'] = $download['SIZE'];
            $download['phpaj_READY'] = $download['READY'];
            unset($download);
        }
        foreach ($this->cache['USER'] ?? [] as $id => $source) {
            $downloadId = $source['DOWNLOADID'];
            if (!isset($this->cache['DOWNLOAD'][$downloadId])
                || empty($this->cache['IDS']['DOWNLOADID'][$downloadId]['USERID'][$id])) {
                unset($this->cache['USER'][$id]);
                continue;
            }
            $download =& $this->cache['DOWNLOAD'][$downloadId];
            $download['phpaj_quellen_gesamt']++;
            $status = (string)$source['STATUS'];
            $group = match ($status) { '7' => 'dl', '5', '14' => 'queue', default => 'rest' };
            $download['phpaj_ids_quellen_' . $group][] = $id;
            if ($group !== 'rest') $download['phpaj_quellen_' . $group]++;
            if ($status === '7') {
                $download['phpaj_dl_speed'] += $source['SPEED'];
                $download['phpaj_READY'] += $source['ACTUALDOWNLOADPOSITION'] - $source['DOWNLOADFROM'];
                $download['phpaj_loading_parts'][$id] = array_intersect_key($source,
                    array_flip(['DOWNLOADFROM', 'DOWNLOADTO', 'ACTUALDOWNLOADPOSITION']));
            }
            unset($download);
        }
        foreach (array_keys($this->cache['DOWNLOAD'] ?? []) as $id) {
            $download =& $this->cache['DOWNLOAD'][$id];
            $download['LINK'] = sprintf('ajfsp://file|%s|%s|%s/', $download['FILENAME'], $download['HASH'], $download['SIZE']);
            $download['phpaj_REST'] = $download['SIZE'] - $download['phpaj_READY'];
            $download['phpaj_DONE'] = (float)$download['SIZE'] > 0
                ? $download['phpaj_READY'] / $download['SIZE'] * 100 : 0.0;
            if ((string)$download['STATUS'] === '0') {
                $download['phpaj_STATUS'] = $download['phpaj_quellen_dl'] > 0 ? '0_2' : '0_1';
            }
            $download['phpaj_STATUS_SORT'] = match ($download['phpaj_STATUS']) {
                '0_2' => 0, '0_1' => 1, '18' => 2, '17' => 3, '14' => 4, default => 5,
            };
            $this->subdirs[$download['TARGETDIRECTORY']][$id] = $download;
            unset($download);
        }
    }

    public function ids(string $sort = 'name', string $subdir = '', ?int $dir = null): array
    {
        [$field, $mode, $defaultDirection] = match ($sort) {
            'sources' => ['phpaj_quellen_gesamt', SORT_NUMERIC, 1],
            'status' => ['phpaj_STATUS_SORT', SORT_NUMERIC, 0],
            'speed' => ['phpaj_dl_speed', SORT_NUMERIC, 1],
            'pdl' => ['POWERDOWNLOAD', SORT_NUMERIC, 1],
            'size' => ['SIZE', SORT_NUMERIC, 1],
            'rest' => ['phpaj_REST', SORT_NUMERIC, 0],
            'done' => ['phpaj_DONE', SORT_NUMERIC, 1],
            default => ['FILENAME', SORT_STRING, 0],
        };
        return empty($this->subdirs[$subdir]) ? []
            : subs::ajsort($this->subdirs[$subdir], $field, $mode, $dir ?? $defaultDirection);
    }

    public function download(int|string $id): array
    {
        return $this->cache['DOWNLOAD'][$id];
    }

    public function user(int|string $id): array
    {
        return $this->cache['USER'][$id];
    }

    public function action(string $action, array $ids = [], string $value = ''): string
    {
        $ids = array_values($ids);
        if ($action === 'settargetdir') {
            $result = '';
            foreach ($ids as $id) {
                $result .= $action . ' &rArr; ' . $this->core->command('function',
                    $action . '?' . http_build_query(['id' => $id, 'dir' => $value], '', '&', PHP_QUERY_RFC3986)) . '<br/>';
            }
            return $result;
        }
        $parameters = [];
        foreach ($ids as $index => $id) $parameters[$index === 0 ? 'id' : 'id' . $index] = $id;
        if ($action === 'setpowerdownload') {
            $power = (float)str_replace(',', '.', $value);
            if ($power > 1 && $power < 2.2) $power = 2.2;
            // The Core parses an integer; a fractional value would silently become 0.
            $parameters['Powerdownload'] = (int)round($power * 10 - 10);
        }
        if ($action === 'renamedownload') $parameters['name'] = $value;
        return $action . ' &rArr; ' . $this->core->command('function',
            $action . '?' . http_build_query($parameters, '', '&', PHP_QUERY_RFC3986));
    }
}
