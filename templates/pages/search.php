<?php
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\View;
/** @var array $searches @var list<array> $entries @var int $total @var string $sort @var string $sort_dir @var array $sort_defaults @var string $active @var object $lang */
$s = $lang->Search;
$sortArgs = ['sort' => $sort, 'dir' => $sort_dir, 'defaults' => $sort_defaults, 'site' => 'search'];
?>
<div id="search-root" data-csrf="<?= $e(Csrf::token()) ?>">
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

    <div class="columns">
        <aside class="column is-3-desktop is-4-tablet">
            <div class="box search-list" id="search-list">
                <ul class="menu-list" role="tablist">
                    <li><a href="#" role="tab" class="is-active" data-search-tab="all" aria-selected="true"><?= $e($s->all) ?> <span class="tag is-rounded is-link" id="aj-search-badge-all"><?= (int)$total ?></span></a></li>
                    <?php foreach ($searches as $sid => $x): ?>
                        <li><a href="#" role="tab" data-search-tab="<?= $e($sid) ?>" aria-selected="false"><span class="search-term"><?= $e($x['text']) ?></span> <span class="tag is-rounded is-link" data-search-badge="<?= $e($sid) ?>"><?= (int)$x['found'] ?></span></a></li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($searches): ?>
                    <form method="post" action="index.php?site=search" class="mt-3">
                        <?= Csrf::field() ?>
                        <button class="button is-danger is-light is-fullwidth" type="submit" name="deleteall" value="1"><?= View::icon('trash') ?><span><?= $e($s->delet) ?></span></button>
                    </form>
                <?php endif; ?>
            </div>
        </aside>

        <div class="column">
            <section class="box" id="search-results">
                <?php foreach ($searches as $sid => $x): ?>
                    <div class="search-actions" data-search-actions="<?= $e($sid) ?>" hidden>
                        <form method="post" action="index.php?site=search">
                            <?= Csrf::field() ?>
                            <?php if ($x['running']): ?>
                                <button class="button is-warning" type="submit" name="cancelid" value="<?= $e($sid) ?>"><?= $e($s->cancle_search) ?></button>
                            <?php else: ?>
                                <button class="button is-danger" type="submit" name="deleteid" value="<?= $e($sid) ?>"><?= View::icon('trash') ?><span><?= $e($s->delet_search) ?></span></button>
                            <?php endif; ?>
                        </form>
                        <?php if ($x['running'] && $x['progress'] < 100): ?>
                            <progress class="progress is-success mt-3" data-search-progress="<?= $e($sid) ?>" value="<?= $e($x['progress']) ?>" max="100"><?= $e($x['progress']) ?>%</progress>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

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
                        <div class="dropdown" data-dropdown id="search-format-dropdown">
                            <button type="button" class="button" data-dropdown-toggle aria-haspopup="true" aria-expanded="false"><?= View::icon('funnel') ?><span><?= $e($s->format) ?></span></button>
                            <div class="dropdown-menu" role="menu"><div class="dropdown-content" id="search-format-list"></div></div>
                        </div>
                    </div>

                    <div class="sort-bar">
                        <?= $partial('sort-link', $sortArgs + ['field' => 'name', 'label' => $s->name]) ?>
                        <?= $partial('sort-link', $sortArgs + ['field' => 'size', 'label' => $s->size]) ?>
                        <?= $partial('sort-link', $sortArgs + ['field' => 'format', 'label' => $s->format]) ?>
                        <?= $partial('sort-link', $sortArgs + ['field' => 'count', 'label' => $s->sources]) ?>
                    </div>

                    <div class="table-wrap">
                        <table class="table is-fullwidth is-hoverable responsive-table" id="search-table">
                            <thead><tr>
                                <th class="col-check"><input type="checkbox" id="search-select-all" aria-label="<?= $e($lang->UI->select_all) ?>"></th>
                                <th><?= $e($s->name) ?></th>
                                <th><?= $e($s->size) ?></th>
                                <th><?= $e($s->format) ?></th>
                                <th><?= $e($s->sources) ?></th>
                                <th class="col-actions"></th>
                            </tr></thead>
                            <tbody id="search-tbody">
                            <?php foreach ($entries as $r): ?>
                                <tr data-entry="<?= (int)$r['id'] ?>" data-search="<?= $e($r['search']) ?>" data-name="<?= $e($r['name']) ?>" data-format="<?= $e($r['format']) ?>">
                                    <td class="col-check" data-label=""><input type="checkbox" name="selected_links[]" value="<?= $e($r['link']) ?>" aria-label="<?= $e($r['name']) ?>"></td>
                                    <td class="col-name" data-label="<?= $e($s->name) ?>"><strong class="dl-name"><?= $e($r['name']) ?></strong></td>
                                    <td data-label="<?= $e($s->size) ?>" class="is-nowrap"><?= $e($r['size']) ?></td>
                                    <td data-label="<?= $e($s->format) ?>"><?= $e($r['format']) ?></td>
                                    <td data-label="<?= $e($s->sources) ?>" data-aj="sources"><?= (int)$r['sources'] ?></td>
                                    <td class="col-actions" data-label="">
                                        <?php if ($r['info'] !== ''): ?><a class="app-icon-button is-small" href="<?= $e($r['info']) ?>" target="_blank" rel="noopener noreferrer" title="<?= $e($s->info) ?>"><?= View::icon('info-circle') ?></a><?php endif; ?>
                                        <a class="button is-success is-small" href="index.php?site=search&amp;link=<?= $e(rawurlencode($r['link'])) ?>" title="<?= $e($lang->UI->download) ?>"><?= View::icon('download') ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="empty-state" id="search-empty" <?= $entries ? 'hidden' : '' ?>><?= $e($searches ? $s->empty : $s->none) ?></p>
                </form>
            </section>
        </div>
    </div>
</div>
