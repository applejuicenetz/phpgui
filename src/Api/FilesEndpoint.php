<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Request;

final class FilesEndpoint extends Endpoint
{
    private const PAGE_SIZE = 200;

    private function directory(): ?string
    {
        return array_key_exists('dir', $_GET) ? Request::get('dir') : null;
    }

    public function get(): array
    {
        $share = new Share();
        $dir = $this->directory();
        $filter = trim(Request::get('q'));
        $result = $share->page($dir, max(1, Request::int('page', 1)), self::PAGE_SIZE, $filter);
        $files = [];
        foreach ($result['files'] as $f) {
            $files[] = [
                'id' => (int)$f['ID'], 'name' => (string)$f['SHORTFILENAME'], 'path' => (string)$f['FILENAME'],
                'size' => (int)$f['SIZE'], 'priority' => (int)$f['PRIORITY'], 'link' => (string)$f['LINK'],
                'last' => isset($f['LASTASKED']) ? (int)(float)$f['LASTASKED'] : null,
                'asked' => isset($f['ASKCOUNT']) ? (int)$f['ASKCOUNT'] : null,
                'searched' => isset($f['SEARCHCOUNT']) ? (int)$f['SEARCHCOUNT'] : null,
            ];
        }
        $folders = [];
        foreach ($dir !== null && $filter === '' ? $share->directory($dir) : [] as [$path, $name]) {
            $folders[] = ['path' => (string)$path, 'name' => (string)$name];
        }
        return [
            'files' => $files, 'folders' => $folders, 'filter' => $filter, 'spent' => $share->spentprio,
            'page' => $result['page'], 'pages' => $result['pages'], 'total' => $result['total'],
        ];
    }

    public function post(): array
    {
        $share = new Share();
        $ids = Request::list('sharefile');
        if (count($ids) > 1000 || count(array_filter($ids, 'ctype_digit')) !== count($ids)) throw new ApiException(400, 'invalid_id');
        switch (Request::str('action')) {
            case 'export':
                $links = [];
                if ($ids === []) {
                    $number = 1;
                    do {
                        $result = $share->page($this->directory(), $number++, self::PAGE_SIZE, trim(Request::get('q')));
                        foreach ($result['files'] as $file) $links[] = $file['LINK'];
                    } while ($result['page'] < $result['pages']);
                } else {
                    foreach ($ids as $id) $links[] = $share->get_file((int)$id)['LINK'];
                }
                $links = array_values(array_unique($links));
                sort($links);
                return ['links' => $links];
            case 'priority':
                $priority = Request::int('prio');
                if ($ids === [] || $priority < 1 || $priority > 250) throw new ApiException(400, 'invalid_value');
                $share->setpriority($ids, $priority);
                return ['ok' => true];
            default:
                throw new ApiException(400, 'unknown_action');
        }
    }
}
