<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Request;

final class SharesController extends ShareFilesBase
{
    public function handle(): Page
    {
        $share = new Share();
        $this->runAction($share);
        $filter = trim(Request::get('q'));
        if ($filter !== '') {
            return $this->search($share, $filter);
        }

        $dirs = [];
        foreach ($share->get_shared_dirs(1) as $id) {
            $d = $share->get_shared_dir($id);
            $dirs[] = [
                'name' => (string)$d['NAME'],
                'subs' => $d['SHAREMODE'] === 'subdirectory',
            ];
        }

        return new Page('pages/shares', [
            'temp' => $share->get_temp(),
            'dirs' => $dirs,
            'filter' => '',
        ], scripts: ['shares.js', 'sharefiles.js']);
    }

    /** Searches the file names of every shared directory. */
    private function search(Share $share, string $filter): Page
    {
        $self = 'index.php?site=shares&q=' . rawurlencode($filter);
        $data = $this->fileList($share, null, $filter, $self);

        return new Page('pages/shares', $data + [
            'temp' => '',
            'dirs' => [],
            'folders' => [],
        ], scripts: ['shares.js', 'sharefiles.js']);
    }

    private function runAction(Share $share): void
    {
        if (!Request::isPost() || !Csrf::valid()) {
            return;
        }
        $lang = $this->lang;
        switch (Request::str('action')) {
            case 'check':
                $share->changesub('*sharecheck', 0);
                $this->flash('info', $lang->Share->set_share, $lang->Share->inprogress);
                break;
            case 'toggle':
                $share->changesub(Request::str('name'), Request::str('subs') === '1' ? 1 : 0);
                $this->flash('info', $lang->Share->set_share, $lang->Share->inprogress);
                break;
            case 'remove':
                $share->del_share(Request::str('name'));
                break;
            case 'add':
                $name = trim(Request::str('name'));
                if ($name !== '') {
                    $share->add_share($name, Request::str('subs') === '1' ? 1 : 0);
                    $this->flash('info', $lang->Share->new_share, $lang->Share->inprogress);
                }
                break;
            default:
                return;
        }
        $this->redirect('index.php?site=shares');
    }
}
