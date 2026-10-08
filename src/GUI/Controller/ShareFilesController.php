<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Format;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Request;

final class ShareFilesController extends Controller
{
    public function handle(): Page
    {
        $share = new Share();
        $dir = Request::get('dir');
        $self = 'index.php?site=sharefiles&dir=' . rawurlencode($dir);

        if (Request::isPost() && Csrf::valid()) {
            $this->post($share, $dir, $self);
        }
        $result = $share->page($dir, max(1, Request::int('page', 1)), 200);

        $folders = [];
        foreach ($share->directory($dir) as $d) {
            $folders[] = ['path' => (string)$d[0], 'name' => (string)$d[1]];
        }
        $files = [];
        foreach ($result['files'] as $f) {
            $files[] = [
                'id' => (int)$f['ID'],
                'name' => (string)$f['SHORTFILENAME'],
                'size' => Format::bytes($f['SIZE']),
                'priority' => (string)$f['PRIORITY'],
                'link' => (string)$f['LINK'],
                'info' => !empty($_ENV['REL_INFO']) ? sprintf($_ENV['REL_INFO'], $f['LINK']) : '',
                'last' => isset($f['LASTASKED']) ? date('j.n.y - H:i:s', (int)($f['LASTASKED'] / 1000)) : 'N/A',
                'asked' => $f['ASKCOUNT'] ?? 'N/A',
                'searched' => $f['SEARCHCOUNT'] ?? 'N/A',
            ];
        }

        $export = $_SESSION['shareexport'] ?? [];
        sort($export);

        return new Page('pages/sharefiles', [
            'dir' => $dir,
            'self' => $self,
            'folders' => $folders,
            'files' => $files,
            'export' => implode("\n", $export),
            'spent' => (int)$share->spentprio,
            'page' => $result['page'],
            'pages' => $result['pages'],
            'total' => $result['total'],
        ], $this->lang->System->pagetitle->sharefiles, ['sharefiles.js']);
    }

    private function post(Share $share, string $dir, string $self): void
    {
        $ids = array_values(array_filter(Request::list('sharefile'), 'ctype_digit'));
        if (Request::str('clear_list') !== '') {
            $_SESSION['shareexport'] = [];
            $this->flash('success', $this->lang->Share->link_export_alert, $this->lang->Share->success);
        } elseif (Request::str('exportlinks') !== '') {
            $_SESSION['shareexport'] = [];
            $selected = $ids;
            if ($selected === []) {
                $pageNumber = 1;
                do {
                    $result = $share->page($dir, $pageNumber++, 200);
                    foreach ($result['files'] as $file) $selected[] = (string)$file['ID'];
                } while ($result['page'] < $result['pages']);
            }
            foreach ($selected as $id) {
                $link = $share->get_file((int)$id)['LINK'];
                if (!in_array($link, $_SESSION['shareexport'], true)) {
                    $_SESSION['shareexport'][] = $link;
                }
            }
            return;
        } elseif (Request::str('setprio') !== '' && $ids !== []) {
            $share->setpriority($ids, max(1, min(250, Request::int('prio', 1))));
        }
        $this->redirect($self);
    }
}
