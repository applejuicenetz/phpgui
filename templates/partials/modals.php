<?php
use appleJuiceNETZ\GUI\View;
/** @var object $lang @var string $csrf */
$ui = $lang->UI;
?>
<div class="modal" id="modal-links" role="dialog" aria-modal="true" aria-labelledby="modal-links-title" hidden>
    <div class="modal-background" data-modal-close></div>
    <form class="modal-card" method="post" action="index.php?site=downloads">
        <header class="modal-card-head">
            <h2 class="modal-card-title" id="modal-links-title"><?= $e($ui->add_links) ?></h2>
            <button class="delete" type="button" aria-label="<?= $e($ui->close) ?>" data-modal-close></button>
        </header>
        <section class="modal-card-body">
            <div class="field">
                <label class="label" for="ajfsp-link-input"><?= $e($ui->add_links_label) ?></label>
                <div class="control"><textarea class="textarea" id="ajfsp-link-input" name="ajfsp_link" rows="4" placeholder="ajfsp://file|…" required></textarea></div>
            </div>
            <div class="field">
                <label class="label" for="ajfsp-target-input"><?= $e($ui->add_links_target) ?></label>
                <div class="control"><input class="input" type="text" id="ajfsp-target-input" name="ajfsp_target" maxlength="255" autocomplete="off" placeholder="<?= $e($ui->add_links_target_hint) ?>"></div>
            </div>
            <div class="field">
                <label class="label" for="ajfsp-file-input"><?= $e($ui->add_links_file) ?></label>
                <div class="control"><input class="input" type="file" id="ajfsp-file-input" accept=".ajl"></div>
                <p class="help" id="ajfsp-file-note" data-text-loaded="<?= $e($ui->add_links_loaded) ?>" data-text-invalid="<?= $e($ui->add_links_invalid) ?>" hidden></p>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-primary" type="submit"><?= View::icon('download') ?><span><?= $e($ui->add_links_submit) ?></span></button>
            <button class="button" type="button" data-modal-close><?= $e($ui->cancel) ?></button>
        </footer>
    </form>
</div>

<div class="modal" id="modal-kick" role="alertdialog" aria-modal="true" aria-labelledby="modal-kick-title" hidden>
    <div class="modal-background" data-modal-close></div>
    <form class="modal-card" method="post" action="index.php?site=kickcore">
        <?= \appleJuiceNETZ\GUI\Csrf::field() ?>
        <header class="modal-card-head has-background-danger">
            <h2 class="modal-card-title has-text-danger-invert" id="modal-kick-title"><?= $e($ui->kill_core_title) ?></h2>
            <button class="delete" type="button" aria-label="<?= $e($ui->close) ?>" data-modal-close></button>
        </header>
        <section class="modal-card-body"><?= $e($ui->kill_core_text) ?></section>
        <footer class="modal-card-foot">
            <button class="button is-danger" type="submit"><?= View::icon('power') ?><span><?= $e($ui->kill_core_confirm) ?></span></button>
            <button class="button" type="button" data-modal-close><?= $e($ui->cancel) ?></button>
        </footer>
    </form>
</div>
