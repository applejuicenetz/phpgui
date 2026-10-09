<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Request;

final class StatisticsEndpoint extends Endpoint
{
    private const MODES = [
        'last' => ['LASTASKED', true], '-last' => ['LASTASKED', false],
        'most' => ['ASKCOUNT', true], '-most' => ['ASKCOUNT', false],
        'search' => ['SEARCHCOUNT', true], '-search' => ['SEARCHCOUNT', false],
    ];

    public function get(): array
    {
        $mode = Request::get('stats', 'most');
        if (!isset(self::MODES[$mode])) $mode = 'most';
        [$field, $descending] = self::MODES[$mode];
        $rows = [];
        $share = new Share();
        foreach ($share->statistics($field, $descending) as $file) {
            $rows[] = ['name' => (string)$file['SHORTFILENAME'], 'link' => (string)$file['LINK'], 'value' => (int)($file[$field] ?? 0)];
        }
        return ['mode' => $mode, 'rows' => $rows, 'spent' => $share->spentprio];
    }
}
