<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\GUI\CoreSettings;
use appleJuiceNETZ\GUI\Request;

final class DownloadsEndpoint extends Endpoint
{
    private const ACTIONS = ['pausedownload', 'resumedownload', 'canceldownload', 'cleandownloadlist', 'setpowerdownload', 'settargetdir', 'renamedownload'];

    public function get(): array
    {
        return [
            'items' => CoreData::downloads(),
            'speed' => (int)CoreData::information()['DOWNLOADSPEED'],
            'max' => (int)(CoreSettings::read(new Core())['maxdownload'] ?? 0),
        ];
    }

    public function post(): array
    {
        $action = Request::str('action');
        if (!in_array($action, self::ACTIONS, true)) throw new ApiException(400, 'unknown_action');
        $ids = Request::list('dl_id');
        if ($action === 'cleandownloadlist') $ids = ['0'];
        if ($ids === [] || count($ids) > 1000 || count(array_filter($ids, 'ctype_digit')) !== count($ids)) throw new ApiException(400, 'invalid_id');
        $value = Request::str('action_value');
        if ($action === 'setpowerdownload') {
            $power = str_replace(',', '.', $value);
            if (!is_numeric($power) || (float)$power < 1 || (float)$power > 50) throw new ApiException(400, 'invalid_value');
        }
        if ($action === 'renamedownload' && (trim($value) === '' || strlen($value) > 1024 || preg_match('/[\x00-\x1f]/', $value))) throw new ApiException(400, 'invalid_value');
        (new Downloads())->action($action, $ids, $value);
        return ['ok' => true];
    }
}
