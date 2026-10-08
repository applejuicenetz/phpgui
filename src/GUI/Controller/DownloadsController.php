<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Format;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Request;
use appleJuiceNETZ\GUI\ViewData;

final class DownloadsController extends Controller
{
    /** Erlaubte Aktionen => Meldungsschlüssel. */
    private const ACTIONS = [
        'pausedownload' => 'pause_done',
        'resumedownload' => 'resume_done',
        'canceldownload' => 'canceldownload',
        'cleandownloadlist' => 'cleardownloadlist',
        'setpowerdownload' => 'pdl_done',
        'settargetdir' => 'target_done',
        'renamedownload' => 'rename_done',
    ];

    /** Spalten, nach denen sortiert werden kann => Standardrichtung. */
    private const SORT = ['name' => 'asc', 'status' => 'asc', 'done' => 'desc', 'pdl' => 'desc'];

    public function handle(): Page
    {
        $this->runAction();

        $sort = Request::get('sort', 'status');
        if (!isset(self::SORT[$sort])) {
            $sort = 'status';
        }
        $dirStr = Request::get('sort_dir');
        $dir = $dirStr === 'asc' ? 0 : ($dirStr === 'desc' ? 1 : null);
        $info = $this->information();
        $max = (int)($this->settings()['maxdownload'] ?? 0);

        return new Page('pages/downloads', [
            'rows' => ViewData::downloads($sort, $dir),
            'sort' => $sort,
            'sort_dir' => $dirStr ?: self::SORT[$sort],
            'sort_defaults' => self::SORT,
            'speed' => (int)$info['DOWNLOADSPEED'],
            'max' => $max,
            'speed_label' => $this->speedLabel((int)$info['DOWNLOADSPEED'], $max),
            'speed_percent' => $max > 0 ? min(100, round($info['DOWNLOADSPEED'] / $max * 100, 1)) : 0,
        ], poll: ['downloads'], scripts: ['downloads.js']);
    }

    private function runAction(): void
    {
        $action = Request::str('action');
        if ($action === '') {
            return;
        }
        if (!isset(self::ACTIONS[$action]) || !Request::isPost() || !Csrf::valid()) {
            $this->redirect('index.php?site=downloads');
        }

        $ids = array_values(array_filter(Request::list('dl_id'), 'ctype_digit'));
        $value = Request::str('action_value');
        if ($action === 'cleandownloadlist') {
            $ids = ['0'];
        }
        if ($ids === []) {
            $this->redirect('index.php?site=downloads');
        }
        (new Downloads())->action($action, $ids, $value);

        $key = self::ACTIONS[$action];
        $this->flash('success', (string)($this->lang->Downloads->$key ?? $this->lang->UI->saved));
        $this->redirect('index.php?site=downloads');
    }

    private function speedLabel(int $speed, int $max): string
    {
        return Format::speed($speed) . ' / ' . ($max > 0 ? Format::bytes($max, 2, true) . '/s' : '∞');
    }
}
