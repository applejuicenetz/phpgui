<?php
use appleJuiceNETZ\GUI\View;
/** @var string $credits @var bool $credits_negative @var string $nick @var ?string $permalink @var object $lang @var string $title @var string $csrf */
$ui = $lang->UI;
?>
<header class="app-topbar">
    <button type="button" class="app-icon-button app-menu-toggle" data-sidebar-toggle aria-controls="sidebar" aria-expanded="false" aria-label="<?= $e($ui->menu) ?>">
        <?= View::icon('list') ?>
    </button>
    <h1 class="app-title"><?= $e($title) ?></h1>
    <div class="app-topbar-actions">
        <div class="app-credits<?= $credits_negative ? ' is-negative' : '' ?>" title="<?= $e($ui->credits) ?>">
            <?= View::icon('diamond') ?>
            <span><span id="aj-header-credits"><?= $e($credits) ?></span><small><?= $e($ui->credits) ?></small></span>
        </div>
        <button type="button" class="app-icon-button" data-modal-open="modal-links" aria-label="<?= $e($ui->add_links) ?>">
            <?= View::icon('plus-lg') ?>
        </button>
        <div class="dropdown is-right" data-dropdown>
            <button type="button" class="app-icon-button" data-dropdown-toggle aria-haspopup="true" aria-expanded="false" aria-label="<?= $e($ui->theme) ?>">
                <?= View::icon('circle-half') ?>
            </button>
            <div class="dropdown-menu" role="menu">
                <div class="dropdown-content">
                    <button type="button" class="dropdown-item" data-theme-value="light"><?= View::icon('sun') ?> <?= $e($ui->theme_light) ?></button>
                    <button type="button" class="dropdown-item" data-theme-value="dark"><?= View::icon('moon-stars') ?> <?= $e($ui->theme_dark) ?></button>
                    <button type="button" class="dropdown-item" data-theme-value="auto"><?= View::icon('circle-half') ?> <?= $e($ui->theme_auto) ?></button>
                </div>
            </div>
        </div>
        <div class="dropdown is-right" data-dropdown>
            <button type="button" class="app-icon-button" data-dropdown-toggle aria-haspopup="true" aria-expanded="false" aria-label="<?= $e($nick) ?>">
                <?= View::icon('person-circle') ?>
            </button>
            <div class="dropdown-menu" role="menu">
                <div class="dropdown-content">
                    <div class="dropdown-item has-text-weight-semibold"><?= $e($nick) ?></div>
                    <hr class="dropdown-divider">
                    <a class="dropdown-item" href="index.php?site=user_settings"><?= $e($lang->Navigation->user_settings) ?></a>
                    <?php if ($permalink): ?>
                        <a class="dropdown-item" href="<?= $e($permalink) ?>"><?= $e($lang->Navigation->permalink) ?></a>
                    <?php endif; ?>
                    <a class="dropdown-item" href="index.php?site=logout"><?= $e($lang->Navigation->logout) ?></a>
                    <button type="button" class="dropdown-item has-text-danger" data-modal-open="modal-kick"><?= $e($lang->Navigation->kick_core) ?></button>
                </div>
            </div>
        </div>
    </div>
</header>
