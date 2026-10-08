<?php
use appleJuiceNETZ\GUI\View;
/** @var string $level @var ?string $title @var string $text */
$map = ['success' => 'is-success', 'info' => 'is-info', 'warning' => 'is-warning', 'danger' => 'is-danger'];
$icon = ['success' => 'check-circle-fill', 'info' => 'info-circle-fill', 'warning' => 'exclamation-triangle-fill', 'danger' => 'exclamation-triangle-fill'][$level] ?? 'info-circle-fill';
$auto = $auto ?? false;
?>
<div class="notification is-light <?= $e($map[$level] ?? 'is-info') ?> app-alert" role="<?= $level === 'danger' || $level === 'warning' ? 'alert' : 'status' ?>"<?= $auto ? ' data-autodismiss="4000"' : '' ?>>
    <button class="delete" type="button" aria-label="<?= $e($lang->UI->close ?? 'Close') ?>" data-dismiss></button>
    <div class="app-alert-body">
        <?= View::icon($icon, 'is-medium') ?>
        <div>
            <?php if (!empty($title)): ?><strong><?= $e($title) ?></strong><br><?php endif; ?>
            <?php if (!empty($href)): ?><a href="<?= $e($href) ?>" target="_blank" rel="noopener noreferrer"><?= $e($text) ?></a><?php else: ?><?= $e($text) ?><?php endif; ?>
        </div>
    </div>
</div>
