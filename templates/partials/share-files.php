<?php
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\View;
/** @var int $page @var int $pages @var int $total @var string $filter @var string $self @var list<array> $files @var string $export @var int $spent @var object $lang @var list<array> $folders */
$sh = $lang->Share;
?>
<?php if ($export !== ''): ?>
    <section class="box">
        <h2 class="box-title"><?= $e($sh->link_export_title) ?></h2>
        <textarea class="textarea" rows="8" readonly id="export-text"><?= $e($export) ?></textarea>
        <form method="post" action="<?= $e($self) ?>" class="mt-3">
            <?= Csrf::field() ?>
            <button class="button" type="submit" name="clear_list" value="1"><?= $e($sh->delet_export) ?></button>
        </form>
    </section>
<?php endif; ?>

<form method="post" action="<?= $e($self) ?>" class="box" id="sharefiles-form">
    <?= Csrf::field() ?>
    <div class="toolbar sharefiles-toolbar">
        <div class="buttons mb-0">
            <button class="button" type="submit" name="exportlinks" value="1"><?= $e($sh->export) ?></button>
            <a class="button" href="<?= $e($self) ?>&amp;forcereload=1" aria-label="<?= $e($sh->refresh) ?>"><?= View::icon('arrow-clockwise') ?></a>
        </div>
        <div class="field has-addons mb-0">
            <div class="control"><div class="select"><select name="prio" aria-label="<?= $e($sh->prio) ?>">
                <?php for ($i = 1; $i <= 250; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
            </select></div></div>
            <div class="control"><button class="button" type="submit" name="setprio" value="1"><?= $e($sh->set_prio) ?></button></div>
        </div>

    </div>

    <ul class="dir-list">
        <?php foreach ($folders ?? [] as $f): ?>
            <li class="dir-row"><?= View::icon('folder-fill', 'dir-icon') ?><a class="dir-name" href="index.php?site=sharefiles&amp;dir=<?= $e(rawurlencode($f['path'])) ?>"><?= $e($f['name']) ?></a></li>
        <?php endforeach; ?>
    </ul>

    <?php if (!$files): ?>
        <p class="empty-state"><?= $e($sh->no_files) ?></p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table is-fullwidth is-hoverable responsive-table">
                <thead><tr><th class="col-check"><input type="checkbox" id="sharefiles-select-all" aria-label="<?= $e($lang->UI->select_all) ?>"></th><th><?= $e($sh->name) ?></th><th><?= $e($sh->size) ?></th><th><?= $e($sh->prio) ?></th><th class="col-actions"></th></tr></thead>
                <tbody>
                <?php foreach ($files as $f): ?>
                    <tr>
                        <td class="col-check" data-label=""><input type="checkbox" name="sharefile[]" value="<?= (int)$f['id'] ?>" aria-label="<?= $e($f['name']) ?>"></td>
                        <td class="col-name" data-label="<?= $e($sh->name) ?>">
                            <div class="dl-name" title="<?= $e($f['path']) ?>"><?= $e($f['name']) ?></div>
                            <?php if ($filter !== ''): ?><div class="dl-meta"><?= $e($f['path']) ?></div><?php endif; ?>
                            <div class="dl-meta">ID <?= (int)$f['id'] ?> · <?= $e($sh->last_asked) ?>: <?= $e($f['last']) ?> · <?= $e($sh->ask_count) ?>: <?= $e($f['asked']) ?> · <?= $e($sh->search_count) ?>: <?= $e($f['searched']) ?></div>
                        </td>
                        <td class="is-nowrap" data-label="<?= $e($sh->size) ?>"><?= $e($f['size']) ?></td>
                        <td data-label="<?= $e($sh->prio) ?>"><?= $e($f['priority']) ?></td>
                        <td class="col-actions" data-label="">
                            <?php if ($f['info'] !== ''): ?><a class="app-icon-button is-small" href="<?= $e($f['info']) ?>" target="_blank" rel="noopener noreferrer"><?= View::icon('info-circle') ?></a><?php endif; ?>
                            <a class="app-icon-button is-small" href="<?= $e($f['link']) ?>" title="<?= $e($sh->source_link) ?>"><?= View::icon('link-45deg') ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php if ($pages > 1): ?>
        <nav class="pagination is-centered mt-4" role="navigation" aria-label="pagination">
            <?php if ($page > 1): ?><a class="pagination-previous" href="<?= $e($self) ?>&amp;page=<?= $page - 1 ?>">‹</a><?php endif; ?>
            <?php if ($page < $pages): ?><a class="pagination-next" href="<?= $e($self) ?>&amp;page=<?= $page + 1 ?>">›</a><?php endif; ?>
            <span class="pagination-list dl-meta"><?= (int)$page ?> / <?= (int)$pages ?> · <?= (int)$total ?></span>
        </nav>
    <?php endif; ?>
    <p class="dl-meta mt-3"><?= $e(strtr($sh->prio_spend ?? '', ['%spent' => $spent])) ?></p>
</form>
