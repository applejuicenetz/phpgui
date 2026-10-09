<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\PartMapService;
use appleJuiceNETZ\GUI\Request;

/** JSON byte ranges; SVG belongs to the frontend. */
final class PartsEndpoint extends Endpoint
{
    public function get(): array
    {
        $download = Request::get('dl_id');
        $user = Request::get('usr_id');
        $downloadId = ctype_digit($download) ? (int)$download : 0;
        $userId = ctype_digit($user) ? (int)$user : 0;
        if ($downloadId <= 0 && $userId <= 0) throw new ApiException(400, 'invalid_id');
        $map = (new PartMapService())->load($downloadId, $userId);
        if ($map === null) throw new ApiException(404, 'not_found');
        $downloads = new Downloads();
        $d = $downloads->cache['DOWNLOAD'][$downloadId] ?? null;
        $u = $downloads->cache['USER'][$userId] ?? null;
        if ($downloadId > 0 && $d === null || $downloadId === 0 && $u === null) throw new ApiException(404, 'not_found');
        $parts = [];
        ksort($map['parts'], SORT_NUMERIC);
        foreach ($map['parts'] as $start => $part) $parts[] = ['start' => (int)$start, 'type' => (int)$part['TYPE']];
        $transfers = [];
        foreach ($map['transfers'] as $transfer) $transfers[] = [
            'start' => (int)$transfer['DOWNLOADFROM'], 'end' => (int)$transfer['DOWNLOADTO'], 'position' => (int)$transfer['ACTUALDOWNLOADPOSITION'],
        ];
        return [
            'size' => (int)$map['size'], 'parts' => $parts, 'download' => $map['download'], 'transfers' => $transfers,
            'heading' => $d !== null ? $d['TEMPORARYFILENUMBER'] . '.data - ' . $d['FILENAME'] : $u['NICKNAME'] . ' - ' . $u['FILENAME'],
            'download_id' => $d !== null ? $downloadId : (int)$u['DOWNLOADID'],
        ];
    }
}
