<?php
use appleJuiceNETZ\GUI\View;
/** @var list<array> $nav @var string $nav_site @var int $uploads_active @var int $downloads_active @var object $lang */
?>
<nav class="app-tabbar" aria-label="<?= $e($lang->UI->page_of_nav) ?>">
    <?php foreach ($nav as $item): ?>
        <?php if (!$item['primary']) { continue; } ?>
        <a href="index.php?site=<?= $e($item['site']) ?>" class="app-tab<?= $nav_site === $item['site'] ? ' is-active' : '' ?>"<?= $nav_site === $item['site'] ? ' aria-current="page"' : '' ?>>
            <span class="app-tab-icon">
                <?= View::icon($item['icon']) ?>
                <?php $badges = ['downloads' => $downloads_active, 'uploads' => $uploads_active]; $badge = $badges[$item['site']] ?? null; ?>
                <?php if ($badge !== null): ?>
                    <span class="tag is-danger is-rounded app-tab-badge" data-badge="<?= $e($item['site']) ?>"<?= $badge > 0 ? '' : ' hidden' ?>><?= (int)$badge ?></span>
                <?php endif; ?>
            </span>
            <span class="app-tab-label"><?= $e($item['label']) ?></span>
        </a>
    <?php endforeach; ?>
    <button type="button" class="app-tab" data-sidebar-toggle aria-controls="sidebar" aria-expanded="false">
        <span class="app-tab-icon"><?= View::icon('list') ?></span>
        <span class="app-tab-label"><?= $e($lang->UI->more) ?></span>
    </button>
</nav>
