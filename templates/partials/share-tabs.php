<?php
/** @var string $site @var object $lang */
$tabs = ['shares' => $lang->Navigation->shares, 'sharestats' => $lang->Share->statistics];
?>
<div class="tabs is-boxed share-tabs" role="tablist">
    <ul>
        <?php foreach ($tabs as $key => $label): ?>
            <li class="<?= $site === $key || ($key === 'shares' && $site === 'sharefiles') ? 'is-active' : '' ?>"<?= $site === $key ? ' aria-current="page"' : '' ?>><a href="index.php?site=<?= $e($key) ?>" role="tab"><?= $e($label) ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
