<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Format;
use appleJuiceNETZ\GUI\Request;

/** File listing, search, export and priority actions shared by the shares and sharefiles pages. */
abstract class ShareFilesBase extends Controller
{
    private const PAGE_SIZE = 200;

    /**
     * Runs POST actions, then returns the template data of the file list.
     *
     * @param ?string $dir null searches every shared directory
     * @return array<string,mixed>
     */
    protected function fileList(Share $share, ?string $dir, string $filter, string $self): array
    {
        if (Request::isPost() && Csrf::valid()) {
            $this->fileAction($share, $dir, $filter, $self);
        }
        $result = $share->page($dir, max(1, Request::int('page', 1)), self::PAGE_SIZE, $filter);
        $files = [];
        foreach ($result['files'] as $f) {
            $files[] = [
                'id' => (int)$f['ID'],
                'name' => (string)$f['SHORTFILENAME'],
                'path' => (string)$f['FILENAME'],
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

        return [
            'filter' => $filter,
            'self' => $self,
            'files' => $files,
            'export' => implode("\n", $export),
            'spent' => $share->spentprio,
            'page' => $result['page'],
            'pages' => $result['pages'],
            'total' => $result['total'],
        ];
    }

    private function fileAction(Share $share, ?string $dir, string $filter, string $self): void
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
                    $result = $share->page($dir, $pageNumber++, self::PAGE_SIZE, $filter);
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
