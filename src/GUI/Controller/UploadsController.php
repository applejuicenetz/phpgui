<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\GUI\Format;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Request;
use appleJuiceNETZ\GUI\ViewData;

final class UploadsController extends Controller
{
    private const SORT = ['name' => 'asc', 'status' => 'asc'];

    public function handle(): Page
    {
        $sort = Request::get('ul_sort', 'status');
        if (!isset(self::SORT[$sort])) {
            $sort = 'status';
        }
        $dirStr = Request::get('ul_sort_dir');
        $dir = $dirStr === 'asc' ? 0 : ($dirStr === 'desc' ? 1 : null);

        $rows = ViewData::uploads($sort, $dir);
        $info = $this->information();
        $max = (int)($this->settings()['maxupload'] ?? 0);

        $ul = new Uploads();
        $slotsUsed = count($ul->cache['IDS']['VALUES']['UPLOADID'] ?? []);
        $slotsMax = (int)($info['MAXUPLOADPOSITIONS'] ?? 0);
        if ($slotsMax > 0) {
            $_SESSION['cache']['UPLOADS']['phpaj_MAXUPLOADPOSITIONS'] = $slotsMax;
        }
        $percent = $slotsMax > 0 ? (string)(int)(($slotsUsed / $slotsMax) * 100 + 0.5) : '?';

        return new Page('pages/uploads', [
            'rows' => $rows,
            'sort' => $sort,
            'sort_dir' => $dirStr ?: self::SORT[$sort],
            'sort_defaults' => self::SORT,
            'speed' => (int)$info['UPLOADSPEED'],
            'max' => $max,
            'speed_label' => Format::speed($info['UPLOADSPEED']) . ' / ' . ($max > 0 ? Format::bytes($max, 2, true) . '/s' : '∞'),
            'speed_percent' => $max > 0 ? min(100, round($info['UPLOADSPEED'] / $max * 100, 1)) : 0,
            'limit_text' => strtr($this->lang->Uploads->limit, ['{percent}' => $percent]),
        ], poll: ['uploads'], scripts: ['uploads.js']);
    }
}
