<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Request;

final class ShareFilesController extends ShareFilesBase
{
    public function handle(): Page
    {
        $share = new Share();
        $dir = Request::get('dir');
        $filter = trim(Request::get('q'));
        $base = 'index.php?site=sharefiles&dir=' . rawurlencode($dir);
        $self = $base . ($filter !== '' ? '&q=' . rawurlencode($filter) : '');
        $data = $this->fileList($share, $dir, $filter, $self);

        // A filter searches the whole subtree, so the folder list is replaced by flat results.
        $folders = [];
        foreach ($filter === '' ? $share->directory($dir) : [] as $d) {
            $folders[] = ['path' => (string)$d[0], 'name' => (string)$d[1]];
        }

        return new Page('pages/sharefiles', $data + [
            'dir' => $dir,
            'base' => $base,
            'folders' => $folders,
        ], $this->lang->System->pagetitle->sharefiles, ['sharefiles.js']);
    }
}
