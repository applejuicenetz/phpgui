<?php
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\View;
/** @var string $temp @var list<array{name:string,subs:bool}> $dirs @var string $filter @var object $lang */
$sh = $lang->Share;
?>
<div id="shares-root">
    <?= $partial('share-tabs', ['site' => 'shares', 'lang' => $lang]) ?>

    <?= $partial('share-search', ['site' => 'shares', 'dir' => null, 'filter' => $filter, 'clear' => 'index.php?site=shares', 'lang' => $lang]) ?>

    <?php if ($filter !== ''): ?>
        <?= $partial('share-files', get_defined_vars()) ?>
    <?php else: ?>
    <section class="box">
        <form method="post" action="index.php?site=shares" class="mb-4">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="check">
            <button class="button is-primary" type="submit"><?= View::icon('arrow-repeat') ?><span><?= $e($sh->check) ?></span></button>
        </form>

        <ul class="dir-list">
            <li class="dir-row">
                <?= View::icon('folder-fill', 'dir-icon') ?>
                <a class="dir-name" href="index.php?site=sharefiles&amp;dir=<?= $e(rawurlencode($temp)) ?>"><?= $e($temp) ?></a>
                <span class="tag"><?= $e($sh->temp) ?></span>
            </li>
            <?php foreach ($dirs as $d): ?>
                <li class="dir-row">
                    <?= View::icon('folder-fill', 'dir-icon') ?>
                    <a class="dir-name" href="index.php?site=sharefiles&amp;dir=<?= $e(rawurlencode($d['name'])) ?>"><?= $e($d['name']) ?></a>
                    <div class="dir-controls">
                        <form method="post" action="index.php?site=shares" class="subs-form">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="name" value="<?= $e($d['name']) ?>">
                            <input type="hidden" name="subs" value="<?= $d['subs'] ? '0' : '1' ?>">
                            <label class="checkbox" title="<?= $e($sh->subs) ?>">
                                <input type="checkbox" <?= $d['subs'] ? 'checked' : '' ?> data-autosubmit>
                                <span><?= $e($sh->subs_toggle) ?></span>
                            </label>
                        </form>
                        <form method="post" action="index.php?site=shares" data-confirm="<?= $e($sh->remove . ': ' . $d['name']) ?>">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="name" value="<?= $e($d['name']) ?>">
                            <button class="button is-danger is-light is-small" type="submit" aria-label="<?= $e($sh->remove) ?>"><?= View::icon('trash') ?></button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="box">
        <h2 class="box-title"><?= $e($sh->shared_directories_new) ?></h2>
        <form method="post" action="index.php?site=shares" id="share-add-form">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="add">
            <div class="field has-addons">
                <div class="control is-expanded"><input class="input" type="text" name="name" id="share-new-path" placeholder="<?= $e($sh->way) ?>" required aria-label="<?= $e($sh->way) ?>"></div>
                <div class="control"><button type="button" class="button" id="share-browse" data-modal-open="modal-dirs"><?= View::icon('folder') ?><span class="action-text"><?= $e($sh->browse) ?></span></button></div>
            </div>
            <div class="field"><label class="checkbox"><input type="checkbox" name="subs" value="1" checked> <?= $e($sh->with_subs) ?></label></div>
            <button class="button is-primary" type="submit"><?= View::icon('plus-lg') ?><span><?= $e($sh->add) ?></span></button>
        </form>
    </section>
    <?php endif; ?>
</div>

<?php if ($filter === ''): ?>
<div class="modal" id="modal-dirs" role="dialog" aria-modal="true" aria-labelledby="modal-dirs-title" hidden>
    <div class="modal-background" data-modal-close></div>
    <div class="modal-card">
        <header class="modal-card-head"><h2 class="modal-card-title" id="modal-dirs-title"><?= $e($sh->choose_dir) ?></h2><button class="delete" type="button" data-modal-close aria-label="<?= $e($lang->UI->close) ?>"></button></header>
        <section class="modal-card-body">
            <p class="dir-current" id="dirs-current"></p>
            <ul class="dir-list" id="dirs-list"></ul>
        </section>
        <footer class="modal-card-foot">
            <button type="button" class="button is-primary" id="dirs-choose"><?= $e($sh->choose) ?></button>
            <button type="button" class="button" data-modal-close><?= $e($lang->UI->cancel) ?></button>
        </footer>
    </div>
</div>
<?php endif; ?>
