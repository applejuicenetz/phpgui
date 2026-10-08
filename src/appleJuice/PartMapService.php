<?php

declare(strict_types=1);

namespace appleJuiceNETZ\appleJuice;

/** Load part availability and active transfers without HTTP or rendering concerns. */
final class PartMapService
{
    public function load(int $downloadId, int $userId): ?array
    {
        $map = (new Core())->command('xml', $downloadId > 0 ? 'downloadpartlist.xml?id=' . $downloadId : 'userpartlist.xml?id=' . $userId);
        $size = (float)array_key_first($map['FILEINFORMATION'] ?? []);
        if ($size <= 0) {
            return null;
        }
        $downloads = new Downloads();
        $downloads->refresh_cache();
        $transfers = [];
        if ($downloadId > 0 && isset($downloads->cache['DOWNLOAD'][$downloadId])) {
            $transfers = array_values($downloads->download($downloadId)['phpaj_loading_parts'] ?? []);
        } elseif ($userId > 0 && isset($downloads->cache['USER'][$userId])) {
            $transfers[] = $downloads->user($userId);
        }
        return ['size' => $size, 'parts' => $map['PART'] ?? [], 'download' => $downloadId > 0, 'transfers' => $transfers];
    }
}
