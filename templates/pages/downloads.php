<?php
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\View;
/** @var list<array> $rows @var string $sort @var string $sort_dir @var array $sort_defaults @var int $max @var string $speed_label @var float $speed_percent @var object $lang */
$d = $lang->Downloads;
$sortArgs = ['sort' => $sort, 'dir' => $sort_dir, 'defaults' => $sort_defaults, 'site' => 'downloads'];
?>
<form id="dl-form" method="post" action="index.php?site=downloads" data-max="<?= (int)$max ?>" data-csrf="<?= $e(Csrf::token()) ?>">
    <?= Csrf::field() ?>

    <?= $partial('speed-panel', ['kind' => 'dl', 'label' => $speed_label, 'percent' => $speed_percent, 'max' => $max, 'lang' => $lang, 'note' => '']) ?>

    <section class="box">
        <div class="selection-bar" id="selection-bar">

            <div class="field has-addons pdl-form" id="pdl-form">
                <p class="control"><button type="button" class="button is-small" data-pdl="dec" aria-label="−"><?= View::icon('dash-lg') ?></button></p>
                <p class="control"><input class="input is-small" type="text" inputmode="decimal" id="pdl-input" name="pdl" value="1.0" size="4" aria-label="<?= $e($d->pdl_value) ?>"></p>
                <p class="control"><button type="button" class="button is-small" data-pdl="inc" aria-label="+"><?= View::icon('plus-lg') ?></button></p>
                <p class="control"><button type="button" class="button is-small" data-dl-action="setpowerdownload"><?= $e($d->set_pdl) ?></button></p>
            </div>

            <div class="buttons are-small action-buttons">
                <button type="button" class="button is-warning is-light" data-dl-action="pausedownload" title="<?= $e($d->pause) ?>"><?= View::icon('pause-fill') ?><span class="action-text"><?= $e($d->pause) ?></span></button>
                <button type="button" class="button is-success is-light" data-dl-action="resumedownload" title="<?= $e($d->resume) ?>"><?= View::icon('play-fill') ?><span class="action-text"><?= $e($d->resume) ?></span></button>
                <button type="button" class="button is-danger is-light" data-dl-action="canceldownload" title="<?= $e($d->cancel) ?>"><?= View::icon('x-lg') ?><span class="action-text"><?= $e($d->cancel) ?></span></button>
                <button type="button" class="button" data-dl-action="settargetdir" title="<?= $e($d->target) ?>"><?= View::icon('folder') ?><span class="action-text"><?= $e($d->target) ?></span></button>
                <button type="button" class="button is-link action-clean" data-dl-action="cleandownloadlist" title="<?= $e($d->clean) ?>"><?= View::icon('magic') ?><span class="action-text"><?= $e($d->clean) ?></span></button>
            </div>
        </div>

        <div class="field filter-field">
            <p class="control has-icons-left">
                <input class="input" type="search" id="dl-filter" placeholder="<?= $e($lang->UI->filter_placeholder) ?>" aria-label="<?= $e($lang->UI->filter) ?>" autocomplete="off">
                <span class="icon is-left"><?= View::icon('funnel') ?></span>
            </p>
        </div>

        <div class="sort-bar" aria-label="Sortierung">
            <?= $partial('sort-link', $sortArgs + ['field' => 'name', 'label' => $d->filename]) ?>
            <?= $partial('sort-link', $sortArgs + ['field' => 'status', 'label' => $d->statuss]) ?>
            <?= $partial('sort-link', $sortArgs + ['field' => 'done', 'label' => $d->progress]) ?>
            <?= $partial('sort-link', $sortArgs + ['field' => 'pdl', 'label' => $d->pdl]) ?>
        </div>

        <?php if (!$rows): ?>
            <p class="empty-state"><?= $e($d->none) ?></p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table is-fullwidth is-hoverable responsive-table" id="dl-table">
                    <thead>
                    <tr>
                        <th class="col-check"><input type="checkbox" id="dl-select-all" aria-label="<?= $e($lang->UI->select_all) ?>"></th>
                        <th><?= $partial('sort-link', $sortArgs + ['field' => 'name', 'label' => $d->filename]) ?></th>
                        <th class="has-text-centered"><?= $e($d->sources) ?></th>
                        <th class="col-status has-text-centered"><?= $partial('sort-link', $sortArgs + ['field' => 'status', 'label' => $d->statuss]) ?></th>
                        <th><?= $partial('sort-link', $sortArgs + ['field' => 'done', 'label' => $d->progress]) ?></th>
                        <th class="has-text-centered"><?= $partial('sort-link', $sortArgs + ['field' => 'pdl', 'label' => $d->pdl]) ?></th>
                        <th class="th-shrink col-speed" title="<?= $e($d->speed) ?>"><?= $e($d->speed) ?></th>
                        <th class="col-actions"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr id="dl-<?= (int)$r['id'] ?>" data-id="<?= (int)$r['id'] ?>" data-name="<?= $e($r['name']) ?>" data-pdl="<?= $e($r['pdl']) ?>" data-target="<?= $e($r['target']) ?>" data-status="<?= $e($r['status']) ?>">
                            <td class="col-check" data-label="">
                                <input class="dl-check" type="checkbox" name="dl_id[]" value="<?= (int)$r['id'] ?>" aria-label="<?= $e($r['name']) ?>">
                            </td>
                            <td class="col-name" data-label="<?= $e($d->filename) ?>">
                                <div class="dl-name" title="<?= $e($r['name']) ?>"><?= $e($r['name']) ?></div>
                                <div class="dl-meta dl-target" data-aj="target-line" title="<?= $e($d->target) ?>"<?= $r['target'] === '' ? ' hidden' : '' ?>><?= View::icon('folder') ?><span data-aj="target"><?= $e($r['target']) ?></span></div>
                                <?php if ($r['part'] !== ''): ?><div class="dl-meta"><?= $e($r['part']) ?></div><?php endif; ?>
                            </td>
                            <td class="has-text-centered" data-label="<?= $e($d->sources) ?>"><a href="index.php?site=dl_users&amp;dl_id=<?= (int)$r['id'] ?>" data-aj="sources" title="<?= $e($d->sources_show) ?>"><?= (int)($r['sources_queue'] + $r['sources_active']) ?>/<?= (int)$r['sources_total'] ?></a></td>
                            <td class="col-status" data-label="<?= $e($d->statuss) ?>"><span class="tag status-<?= $e($r['status']) ?>" data-aj="status"><?= $e($r['status_text']) ?></span></td>
                            <td class="col-progress" data-label="<?= $e($d->progress) ?>">
                                <div class="progress-line">
                                    <strong data-aj="percent"><?= $e($r['percent']) ?>%</strong>
                                    <span class="dl-meta" data-aj="loaded" title="<?= $e($d->size) ?>"><?= $e($r['loaded']) ?> / <?= $e($r['size']) ?></span>
                                </div>
                                <progress class="progress is-small" value="<?= $e($r['percent']) ?>" max="100" data-aj="bar"><?= $e($r['percent']) ?>%</progress>
                            </td>
                            <td class="has-text-centered" data-label="<?= $e($d->pdl) ?>" data-aj="pdl"><?= $e($r['pdl']) ?></td>
                            <td class="col-speed" data-label="<?= $e($d->speed) ?>"><span data-aj="speed"><?= $e($r['speed']) ?></span><div class="dl-meta dl-eta" data-aj="eta" title="<?= $e($d->rest) ?>"><?= $e($r['eta']) ?></div></td>
                            <td class="col-actions" data-label="">
                                <div class="dropdown is-right" data-dropdown>
                                    <button type="button" class="app-icon-button is-small" data-dropdown-toggle aria-haspopup="true" aria-expanded="false" aria-label="<?= $e($lang->UI->actions) ?>"><?= View::icon('three-dots-vertical') ?></button>
                                    <div class="dropdown-menu" role="menu"><div class="dropdown-content">
                                        <button type="button" class="dropdown-item" data-row-action="rename"><?= View::icon('pencil') ?> <?= $e($d->rename) ?></button>
                                        <button type="button" class="dropdown-item" data-row-action="target"><?= View::icon('folder') ?> <?= $e($d->target) ?></button>
                                        <a class="dropdown-item" href="index.php?site=dl_users&amp;dl_id=<?= (int)$r['id'] ?>"><?= View::icon('people') ?> <?= $e($d->sources_show) ?></a>
                                        <a class="dropdown-item" href="index.php?site=dl_parts&amp;dl_id=<?= (int)$r['id'] ?>"><?= View::icon('bar-chart') ?> <?= $e($d->parts_show) ?></a>
                                    </div></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</form>

<div class="modal" id="modal-rename" role="dialog" aria-modal="true" aria-labelledby="modal-rename-title" hidden>
    <div class="modal-background" data-modal-close></div>
    <div class="modal-card">
        <header class="modal-card-head"><h2 class="modal-card-title" id="modal-rename-title"><?= $e($d->rename_title) ?></h2><button class="delete" type="button" data-modal-close aria-label="<?= $e($lang->UI->close) ?>"></button></header>
        <section class="modal-card-body">
            <label class="label" for="rename-input"><?= $e($d->rename_label) ?></label>
            <input class="input" id="rename-input" type="text" autocomplete="off">
        </section>
        <footer class="modal-card-foot">
            <button type="button" class="button is-primary" id="rename-ok"><?= $e($lang->UI->ok) ?></button>
            <button type="button" class="button" data-modal-close><?= $e($lang->UI->cancel) ?></button>
        </footer>
    </div>
</div>

<div class="modal" id="modal-target" role="dialog" aria-modal="true" aria-labelledby="modal-target-title" hidden>
    <div class="modal-background" data-modal-close></div>
    <div class="modal-card">
        <header class="modal-card-head"><h2 class="modal-card-title" id="modal-target-title"><?= $e($d->target_title) ?></h2><button class="delete" type="button" data-modal-close aria-label="<?= $e($lang->UI->close) ?>"></button></header>
        <section class="modal-card-body">
            <label class="label" for="target-input"><?= $e($d->target_label) ?></label>
            <input class="input" id="target-input" type="text" autocomplete="off">
        </section>
        <footer class="modal-card-foot">
            <button type="button" class="button is-primary" id="target-ok"><?= $e($lang->UI->ok) ?></button>
            <button type="button" class="button" data-modal-close><?= $e($lang->UI->cancel) ?></button>
        </footer>
    </div>
</div>

<div class="modal" id="modal-cancel" role="alertdialog" aria-modal="true" aria-labelledby="modal-cancel-title" hidden>
    <div class="modal-background" data-modal-close></div>
    <div class="modal-card">
        <header class="modal-card-head"><h2 class="modal-card-title" id="modal-cancel-title"><?= $e($d->cancel_title) ?></h2><button class="delete" type="button" data-modal-close aria-label="<?= $e($lang->UI->close) ?>"></button></header>
        <section class="modal-card-body"><ul class="cancel-list" id="cancel-list"></ul></section>
        <footer class="modal-card-foot">
            <button type="button" class="button is-danger" id="cancel-ok"><?= $e($d->cancel_confirm) ?></button>
            <button type="button" class="button" data-modal-close><?= $e($lang->UI->cancel) ?></button>
        </footer>
    </div>
</div>
