<?php
use appleJuiceNETZ\GUI\View;
/** @var list<array> $nav @var string $nav_site @var array $plugins @var int $uploads_active @var int $downloads_active @var object $lang @var string $faq_url @var string $site */
?>
<aside class="app-sidebar" id="sidebar" aria-label="<?= $e($lang->UI->page_of_nav) ?>">
    <a class="app-brand" href="index.php?site=start" aria-label="appleJuice">
        <img class="app-brand-full" src="assets/img/applejuice.svg" alt="appleJuice" width="180" height="65">
        <img class="app-brand-icon" src="assets/img/apple-icon.svg" alt="" width="28" height="32">
    </a>
    <nav>
        <ul class="app-nav">
            <?php foreach ($nav as $item): ?>
                <li>
                    <a href="index.php?site=<?= $e($item['site']) ?>" class="app-nav-link<?= $nav_site === $item['site'] ? ' is-active' : '' ?>"<?= $nav_site === $item['site'] ? ' aria-current="page"' : '' ?>>
                        <?= View::icon($item['icon']) ?>
                        <span class="app-nav-label"><?= $e($item['label']) ?></span>
                        <?php $badges = ['downloads' => $downloads_active, 'uploads' => $uploads_active]; $badge = $badges[$item['site']] ?? null; ?>
                        <?php if ($badge !== null): ?>
                            <span class="tag is-info is-rounded app-nav-badge" data-badge="<?= $e($item['site']) ?>"<?= $badge > 0 ? '' : ' hidden' ?>><?= (int)$badge ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <?php if ($plugins): ?>
                <li class="app-nav-group">
                    <details<?= $site === 'extras' ? ' open' : '' ?>>
                        <summary class="app-nav-link">
                            <?= View::icon('puzzle') ?>
                            <span class="app-nav-label"><?= $e($lang->Navigation->addons) ?></span>
                            <?= View::icon('chevron-down', 'app-nav-caret') ?>
                        </summary>
                        <ul class="app-nav app-nav-sub">
                            <?php foreach ($plugins as $p): ?>
                                <li><a class="app-nav-link" href="index.php?site=extras&amp;show=<?= $e(rawurlencode($p[2])) ?>"><span class="app-nav-label"><?= $e($p[0]) ?></span></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </details>
                </li>
            <?php endif; ?>
            <li>
                <a class="app-nav-link" href="<?= $e($faq_url) ?>" target="_blank" rel="noopener noreferrer">
                    <?= View::icon('info-circle') ?>
                    <span class="app-nav-label"><?= $e($lang->Navigation->help) ?></span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
<div class="app-scrim" data-sidebar-close hidden></div>
