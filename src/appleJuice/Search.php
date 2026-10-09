<?php

declare(strict_types=1);

namespace appleJuiceNETZ\appleJuice;

use appleJuiceNETZ\GUI\subs;

class Search
{
    public array $cache;
    private Core $core;

    public function __construct()
    {
        $_SESSION['cache']['SEARCH'] ??= [];
        $this->cache =& $_SESSION['cache']['SEARCH'];
        $this->core = new Core();
    }

    public function time(): string
    {
        return date('j.n.y - H:i:s', (int)((float)($this->cache['TIME']['VALUES']['CDATA'] ?? 0) / 1000));
    }

    public function refresh_cache(): void
    {
        $timestamp = $this->cache['LASTTIMESTAMP'] ?? 0;
        $this->cache = $this->core->command('xml', 'modified.xml?filter=search&timestamp=' . $timestamp, $this->cache);
        $this->cache['LASTTIMESTAMP'] = $this->cache['TIME']['VALUES']['CDATA'];
    }

    public function process_results(): void
    {
        foreach ($this->cache['SEARCH'] ?? [] as $id => $search) {
            $this->cache['SEARCH'][$id]['phpaj_FOUNDFILES'] = 0;
        }
        foreach ($this->cache['SEARCHENTRY'] ?? [] as $id => $entry) {
            $names = [];
            foreach ($entry['FILENAME'] as $name => $file) {
                $names[$name] = (int)$file['USER'];
            }
            arsort($names, SORT_NUMERIC);
            $name = (string)(array_key_first($names) ?? '');
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $this->cache['SEARCHENTRY'][$id]['phpaj_FILENAME'] = $name;
            $this->cache['SEARCHENTRY'][$id]['phpaj_FORMAT'] = strtoupper($extension !== '' ? $extension : '?');
            $this->cache['SEARCHENTRY'][$id]['phpaj_COUNT'] = array_sum($names);
            $this->cache['SEARCH'][$entry['SEARCHID']]['phpaj_FOUNDFILES']++;
        }
        $this->cache['SEARCHENTRY_count'] = count($this->cache['SEARCHENTRY'] ?? []);
    }

    public function start(string $query): void
    {
        $this->core->command('function', 'search?search=' . rawurlencode($query));
    }

    public function delete(int|string $id): string
    {
        $result = '';
        if (($this->cache['SEARCH'][$id]['RUNNING'] ?? '') === 'false') {
            $result = 'cancelsearch &rArr; ' . $this->core->command('function', 'cancelsearch?id=' . $id);
        }
        unset($this->cache['SEARCH'][$id]);
        foreach ($this->cache['SEARCHENTRY'] ?? [] as $entryId => $entry) {
            if ((string)$id === (string)$entry['SEARCHID']) {
                unset($this->cache['SEARCHENTRY'][$entryId]);
            }
        }
        return $result;
    }

    public function delete_all(): string
    {
        $result = '';
        foreach ($this->cache['SEARCH'] ?? [] as $id => $search) {
            if ($search['RUNNING'] !== 'canceled') {
                $result .= 'cancelsearch &rArr; ' . $this->core->command('function', 'cancelsearch?id=' . $id) . '<br>';
            }
        }
        unset($this->cache['SEARCH'], $this->cache['SEARCHENTRY']);
        return $result;
    }

    public function cancel(int|string $id): string
    {
        $this->cache['SEARCH'][$id]['RUNNING'] = 'canceled';
        return 'cancelsearch &rArr; ' . $this->core->command('function', 'cancelsearch?id=' . $id);
    }

    public function sortedResults(string $type = 'count', ?int $direction = null): array
    {
        [$field, $mode, $defaultDirection] = match ($type) {
            'name' => ['phpaj_FILENAME', SORT_STRING, 0],
            'format' => ['phpaj_FORMAT', SORT_STRING, 0],
            'size' => ['SIZE', SORT_NUMERIC, 1],
            default => ['phpaj_COUNT', SORT_NUMERIC, 1],
        };
        return empty($this->cache['SEARCHENTRY']) ? []
            : subs::ajsort($this->cache['SEARCHENTRY'], $field, $mode, $direction ?? $defaultDirection);
    }
}
