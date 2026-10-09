<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Request;

final class SharesEndpoint extends Endpoint
{
    public function get(): array
    {
        $share = new Share();
        $dirs = [];
        foreach ($share->get_shared_dirs(true) as $id) {
            $d = $share->get_shared_dir($id);
            $dirs[] = ['name' => (string)$d['NAME'], 'subs' => $d['SHAREMODE'] === 'subdirectory'];
        }
        return ['temp' => $share->get_temp(), 'dirs' => $dirs, 'spent' => $share->summary()['spent']];
    }

    public function post(): array
    {
        $share = new Share();
        $name = trim(Request::str('name'));
        switch (Request::str('action')) {
            case 'check':
                $share->changesub('*sharecheck', false);
                break;
            case 'toggle':
                if ($name === '') throw new ApiException(400, 'invalid_value');
                $share->changesub($name, Request::str('subs') === '1');
                break;
            case 'remove':
                if ($name === '') throw new ApiException(400, 'invalid_value');
                $share->del_share($name);
                break;
            case 'add':
                if ($name === '' || strlen($name) > 4096) throw new ApiException(400, 'invalid_value');
                $share->add_share($name, Request::str('subs') === '1');
                break;
            default:
                throw new ApiException(400, 'unknown_action');
        }
        return ['ok' => true];
    }
}
