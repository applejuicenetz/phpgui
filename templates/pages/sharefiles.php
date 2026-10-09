<?php
use appleJuiceNETZ\GUI\View;
/** @var string $dir @var string $filter @var string $base @var list<array> $folders @var object $lang */
?>
<div id="sharefiles-root">
    <?= $partial('share-tabs', ['site' => 'sharefiles', 'lang' => $lang]) ?>

    <p class="mb-3"><a class="button is-small" href="index.php?site=shares"><?= View::icon('arrow-left-short') ?><span><?= $e($lang->Navigation->shares) ?></span></a>
        <code class="dir-path"><?= $e($dir) ?></code></p>

    <?= $partial('share-search', ['site' => 'sharefiles', 'dir' => $dir, 'filter' => $filter, 'clear' => $base, 'lang' => $lang]) ?>
    <?= $partial('share-files', get_defined_vars()) ?>
</div>
