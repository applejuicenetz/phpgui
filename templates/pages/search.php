<?php
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\View;
/** @var array $searches @var list<array> $entries @var int $total @var string $sort @var string $sort_dir @var array $sort_defaults @var string $active @var object $lang */
$s = $lang->Search;
$sortArgs = ['sort' => $sort, 'dir' => $sort_dir, 'defaults' => $sort_defaults, 'site' => 'search'];
?>
<div id="search-root" data-csrf="<?= $e(Csrf::token()) ?>" data-sort="<?= $e($sort) ?>" data-sort-dir="<?= $e($sort_dir) ?>" data-text-none="<?= $e($s->none) ?>" data-text-empty="<?= $e($s->empty) ?>">
    <section class="box">
        <form method="post" action="index.php?site=search" class="field has-addons search-form">
            <?= Csrf::field() ?>
            <div class="control is-expanded has-icons-left">
                <input class="input" type="search" name="searchstring" placeholder="<?= $e($s->placeholder) ?>" aria-label="<?= $e($s->placeholder) ?>" required>
                <span class="icon is-left"><?= View::icon('search') ?></span>
            </div>
            <div class="control"><button class="button is-primary" type="submit"><?= $e($s->button) ?></button></div>
        </form>
    </section>

    <div class="search-tabs" id="search-list" data-active="<?= $e($active) ?>">
        <div class="tabs is-boxed search-tabs-scroll">
            <ul role="tablist">
                <li class="is-active"><a href="#" role="tab" data-search-tab="all" aria-selected="true"><?= $e($s->all) ?> <span class="tag is-rounded is-link" id="aj-search-badge-all"><?= (int)$total ?></span></a></li>
                <?php foreach ($searches as $sid => $x): ?>
                    <li><a href="#" role="tab" data-search-tab="<?= $e($sid) ?>" aria-selected="false"><span class="search-term"><?= $e($x['text']) ?></span> <span class="tag is-rounded is-link" data-search-badge="<?= $e($sid) ?>"><?= (int)$x['found'] ?></span></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <template id="search-tab-template"><li><a href="#" role="tab" data-search-tab="" aria-selected="false"><span class="search-term"></span> <span class="tag is-rounded is-link" data-search-badge="">0</span></a></li></template>
        <form method="post" action="index.php?site=search" class="search-tabs-delete" id="search-delete-all"<?= $searches ? '' : ' hidden' ?>>
            <?= Csrf::field() ?>
            <button class="button is-danger is-light is-small" type="submit" name="deleteall" value="1" title="<?= $e($s->delet) ?>"><?= View::icon('trash') ?><span><?= $e($s->delet) ?></span></button>
        </form>
    </div>

    <div class="columns">
        <div class="column">
            <section class="box" id="search-results">
                <form method="post" action="index.php?site=search" id="search-action-form" hidden><?= Csrf::field() ?></form>
                <template id="search-actions-template"><?= $partial('search-actions', ['sid' => '__SID__', 'x' => ['running' => false, 'progress' => 100], 'lang' => $lang]) ?></template>

                <form method="post" action="index.php?site=search" id="search-download-form">
                    <?= Csrf::field() ?>
                    <div class="search-toolbar">
                        <button class="button is-primary" type="submit"><?= View::icon('download') ?><span><?= $e($s->download_selected) ?></span></button>
                        <div class="field filter-field">
                            <p class="control has-icons-left">
                                <input class="input" type="search" id="search-filter" placeholder="<?= $e($lang->UI->filter_placeholder) ?>" aria-label="<?= $e($lang->UI->filter) ?>" autocomplete="off">
                                <span class="icon is-left"><?= View::icon('funnel') ?></span>
                            </p>
                        </div>
                        <?php foreach ($searches as $sid => $x): ?>
                            <?= $partial('search-actions', ['sid' => (string)$sid, 'x' => $x, 'lang' => $lang]) ?>
                        <?php endforeach; ?>
                        <span id="search-actions-host" hidden></span>
                    </div>

                    <div class="sort-bar">
                        <?= $partial('sort-link', $sortArgs + ['field' => 'name', 'label' => $s->name]) ?>
                        <?= $partial('sort-link', $sortArgs + ['field' => 'count', 'label' => $s->sources]) ?>
                        <?= $partial('sort-link', $sortArgs + ['field' => 'size', 'label' => $s->size]) ?>
                    </div>

                    <div class="table-wrap">
                        <table class="table is-fullwidth is-hoverable responsive-table" id="search-table">
                            <thead><tr>
                                <th class="col-check"><input type="checkbox" id="search-select-all" aria-label="<?= $e($lang->UI->select_all) ?>"></th>
                                <th><?= $partial('sort-link', $sortArgs + ['field' => 'name', 'label' => $s->name]) ?></th>
                                <th class="col-narrow has-text-centered"><?= $partial('sort-link', $sortArgs + ['field' => 'count', 'label' => $s->sources]) ?></th>
                                <th class="col-narrow"><?= $partial('sort-link', $sortArgs + ['field' => 'size', 'label' => $s->size]) ?></th>
                                <th class="col-actions"></th>
                            </tr></thead>
                            <tbody id="search-tbody">
                            <?php foreach ($entries as $r): ?>
                                <tr data-entry="<?= (int)$r['id'] ?>" data-search="<?= $e($r['search']) ?>" data-name="<?= $e($r['name']) ?>">
                                    <td class="col-check" data-label=""><input type="checkbox" name="selected_links[]" value="<?= $e($r['link']) ?>" aria-label="<?= $e($r['name']) ?>"></td>
                                    <td class="col-name" data-label="<?= $e($s->name) ?>"><strong class="dl-name"><?= $e($r['name']) ?></strong></td>
                                    <td data-label="<?= $e($s->sources) ?>" class="col-narrow has-text-centered" data-aj="sources"><?= (int)$r['sources'] ?></td>
                                    <td data-label="<?= $e($s->size) ?>" class="col-narrow"><?= $e($r['size']) ?></td>
                                    <td class="col-actions" data-label="">
                                        <?php if ($r['info'] !== ''): ?><a class="app-icon-button is-small" href="<?= $e($r['info']) ?>" target="_blank" rel="noopener noreferrer" title="<?= $e($s->info) ?>"><?= View::icon('info-circle') ?></a><?php endif; ?>
                                        <a class="button is-success is-small" href="index.php?site=search&amp;link=<?= $e(rawurlencode($r['link'])) ?>" title="<?= $e($lang->UI->download) ?>"><?= View::icon('download') ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <template id="search-row-template">
                        <tr data-entry="" data-search="" data-name="">
                            <td class="col-check" data-label=""><input type="checkbox" name="selected_links[]" value=""></td>
                            <td class="col-name" data-label="<?= $e($s->name) ?>"><strong class="dl-name"></strong></td>
                            <td data-label="<?= $e($s->sources) ?>" class="col-narrow has-text-centered" data-aj="sources"></td>
                            <td data-label="<?= $e($s->size) ?>" class="col-narrow" data-aj="size"></td>
                            <td class="col-actions" data-label="">
                                <a class="app-icon-button is-small" data-aj="info" href="#" target="_blank" rel="noopener noreferrer" title="<?= $e($s->info) ?>" hidden><?= View::icon('info-circle') ?></a>
                                <a class="button is-success is-small" data-aj="download" href="#" title="<?= $e($lang->UI->download) ?>"><?= View::icon('download') ?></a>
                            </td>
                        </tr>
                    </template>
                    <p class="empty-state" id="search-empty" <?= $entries ? 'hidden' : '' ?>><?= $e($searches ? $s->empty : $s->none) ?></p>
                </form>
            </section>
        </div>
    </div>
</div>
