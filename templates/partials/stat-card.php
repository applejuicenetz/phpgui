<?php
use appleJuiceNETZ\GUI\View;
/** @var string $icon @var string $tone @var string $label @var string $href @var string $id */
?>
<a class="box stat-card" href="<?= $e($href) ?>">
    <span class="stat-icon has-background-<?= $e($tone) ?> has-text-<?= $e($tone) ?>-invert"><?= View::icon($icon, 'is-large') ?></span>
    <span class="stat-body">
        <span class="stat-value" <?= !empty($id) ? 'id="' . $e($id) . '"' : '' ?>><?= $value ?></span>
        <span class="stat-label"><?= $e($label) ?></span>
    </span>
</a>
