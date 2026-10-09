<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\appleJuice\Search;
use appleJuiceNETZ\GUI\Request;

final class SearchEndpoint extends Endpoint
{
    public function get(): array
    {
        return CoreData::search();
    }

    public function post(): array
    {
        $search = new Search();
        $action = Request::str('action');
        switch ($action) {
            case 'start':
                $term = trim(Request::str('searchstring'));
                if ($term === '' || strlen($term) > 1024) throw new ApiException(400, 'invalid_value');
                $search->start($term);
                break;
            case 'cancel':
            case 'delete':
                $id = Request::str('id');
                if (!ctype_digit($id)) throw new ApiException(400, 'invalid_id');
                $search->refresh_cache();
                if ($action === 'cancel') $search->cancel($id);
                else $search->delete($id);
                break;
            case 'deleteall':
                $search->refresh_cache();
                $search->delete_all();
                break;
            default:
                throw new ApiException(400, 'unknown_action');
        }
        return ['ok' => true];
    }
}
