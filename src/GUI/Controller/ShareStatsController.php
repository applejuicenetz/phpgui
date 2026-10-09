<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Request;

/** Top 50 shared files by request, search or last-request statistics. */
final class ShareStatsController extends Controller
{
    private const DEFAULT_MODE = 'most';

    /** @return array<string,array{label:string,field:string,descending:bool,column:string}> */
    private function modes(): array
    {
        $s = $this->lang->Share->stats;

        return [
            'last' => ['label' => $s->last, 'field' => 'LASTASKED', 'descending' => true, 'column' => $s->date],
            '-last' => ['label' => $s->nolast, 'field' => 'LASTASKED', 'descending' => false, 'column' => $s->date],
            'most' => ['label' => $s->most, 'field' => 'ASKCOUNT', 'descending' => true, 'column' => $s->requests],
            '-most' => ['label' => $s->least, 'field' => 'ASKCOUNT', 'descending' => false, 'column' => $s->requests],
            'search' => ['label' => $s->search, 'field' => 'SEARCHCOUNT', 'descending' => true, 'column' => $s->searches],
            '-search' => ['label' => $s->nosearch, 'field' => 'SEARCHCOUNT', 'descending' => false, 'column' => $s->searches],
        ];
    }

    public function handle(): Page
    {
        $modes = $this->modes();
        $mode = Request::get('stats');
        if (!isset($modes[$mode])) {
            $mode = self::DEFAULT_MODE;
        }
        $current = $modes[$mode];

        $rows = [];
        foreach ((new Share())->statistics($current['field'], $current['descending']) as $file) {
            $value = $file[$current['field']] ?? '';
            $rows[] = [
                'name' => (string)$file['SHORTFILENAME'],
                'link' => (string)$file['LINK'],
                'value' => $current['field'] === 'LASTASKED' && (float)$value > 0
                    ? date('j.n.y - H:i:s', (int)((float)$value / 1000))
                    : (string)$value,
            ];
        }

        return new Page('pages/sharestats', [
            'modes' => $modes,
            'mode' => $mode,
            'column' => $current['column'],
            'rows' => $rows,
        ], $this->lang->System->pagetitle->sharestats);
    }
}
