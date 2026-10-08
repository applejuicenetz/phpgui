<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Search;
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Flash;
use appleJuiceNETZ\GUI\Format;
use appleJuiceNETZ\GUI\LinkProcessor;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Request;
use appleJuiceNETZ\GUI\ViewData;

final class SearchController extends Controller
{
    private const SORT = ['name' => 'asc', 'size' => 'desc', 'count' => 'desc', 'format' => 'asc'];

    public function handle(): Page
    {
        $this->runAction();

        $sort = Request::get('sort', 'name');
        if (!isset(self::SORT[$sort])) {
            $sort = 'name';
        }
        $dirStr = Request::get('sort_dir');
        $dir = $dirStr === 'asc' ? 0 : ($dirStr === 'desc' ? 1 : null);
        $data = ViewData::search($sort, $dir);

        return new Page('pages/search', [
            'searches' => $data['searches'],
            'entries' => $data['entries'],
            'total' => $data['total'],
            'sort' => $sort,
            'sort_dir' => $dirStr ?: self::SORT[$sort],
            'sort_defaults' => self::SORT,
            'active' => Request::get('searchid', 'all'),
        ], poll: ['search'], scripts: ['search.js']);
    }

    private function runAction(): void
    {
        $search = new Search();
        $isPost = Request::isPost();

        // Download einzelner Links per GET bleibt erhalten (Lesezeichen der Erweiterung), alles andere nur per POST
        $single = Request::get('link');
        if ($single !== '' && !$isPost) {
            $this->download([$single]);
            $this->redirect('index.php?site=search');
        }
        if (!$isPost) {
            return;
        }
        if (!Csrf::valid()) {
            $this->redirect('index.php?site=search');
        }

        $term = trim(Request::str('searchstring'));
        if ($term !== '') {
            $search->start($term);
            $this->redirect('index.php?site=search');
        }
        $cancel = Request::str('cancelid');
        if ($cancel !== '' && ctype_digit($cancel)) {
            $search->cancel($cancel);
        }
        $delete = Request::str('deleteid');
        if ($delete !== '' && ctype_digit($delete)) {
            $_GET['deleteid'] = $delete;
            $search->refresh_cache();
            $search->delete($delete);
        }
        if (Request::str('deleteall') !== '') {
            $search->delete_all();
        }
        $links = Request::list('selected_links');
        if ($links !== []) {
            $this->download($links);
        }
        if ($cancel !== '' || $delete !== '' || Request::str('deleteall') !== '' || $links !== []) {
            $this->redirect('index.php?site=search');
        }
    }

    /** @param list<string> $links */
    private function download(array $links): void
    {
        $done = 0;
        foreach ($links as $raw) {
            foreach (LinkProcessor::submit($raw, $this->core) as $r) {
                if ($r['ok']) {
                    $done++;
                    $_SESSION['link_marker'][] = $r['link']['raw'];
                }
            }
        }
        if ($done > 0) {
            Flash::add('success', $done . ' ' . $this->lang->Share->files . ' → ' . $this->lang->Navigation->downloads, $this->lang->Downloads->get_start);
        }
    }
}
