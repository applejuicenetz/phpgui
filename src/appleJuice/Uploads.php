<?php

declare(strict_types=1);

namespace appleJuiceNETZ\appleJuice;

use appleJuiceNETZ\GUI\subs;

class Uploads
{
    public array $cache;
    private Core $core;

    public function __construct()
    {
        $_SESSION['cache']['UPLOADS'] ??= [];
        $this->cache =& $_SESSION['cache']['UPLOADS'];
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
        $this->cache = $this->core->command('xml', 'modified.xml?timestamp=' . $timestamp . '&filter=uploads;ids', $this->cache);
        $this->cache['LASTTIMESTAMP'] = $this->cache['TIME']['VALUES']['CDATA'];
        $this->process_uploads();
    }

    public function process_uploads(): void
    {
        $this->cache['phpaj_ul'] = $this->cache['phpaj_queue'] = 0;
        $this->cache['phpaj_ids_ul'] = $this->cache['phpaj_ids_queue'] = [];
        foreach (array_keys($this->ids()) as $id) {
            if (empty($this->cache['IDS']['VALUES']['UPLOADID'][$id])) {
                unset($this->cache['UPLOAD'][$id]);
                continue;
            }
            $upload =& $this->cache['UPLOAD'][$id];
            $status = (int)$upload['STATUS'];
            $upload['phpaj_STATUS_SORT'] = match ($status) {
                1 => 0,
                2 => 1,
                5, 6 => 2,
                7 => 3,
                default => 4,
            };
            $group = $status === 1 ? 'ul' : 'queue';
            $this->cache['phpaj_' . $group]++;
            $this->cache['phpaj_ids_' . $group][] = $upload['ID'];
            unset($upload);
        }
    }

    public function ids(): array
    {
        return empty($this->cache['UPLOAD']) ? []
            : subs::ajsort($this->cache['UPLOAD'], 'PRIORITY', SORT_NUMERIC, 1);
    }

    public function get_upload(int|string $id): array
    {
        return $this->cache['UPLOAD'][$id];
    }
}
