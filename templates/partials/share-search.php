<?php
use appleJuiceNETZ\GUI\View;
/** @var string $site @var ?string $dir @var string $filter @var string $clear @var object $lang */
$hint = $lang->Share->filter_hint;
?>
<form method="get" action="index.php" class="field has-addons" id="share-search" role="search">
    <input type="hidden" name="site" value="<?= $e($site) ?>">
    <?php if ($dir !== null): ?><input type="hidden" name="dir" value="<?= $e($dir) ?>"><?php endif; ?>
    <div class="control is-expanded"><input class="input" type="search" name="q" id="share-filter" value="<?= $e($filter) ?>" placeholder="<?= $e($hint) ?>" title="<?= $e($hint) ?>" aria-label="<?= $e($hint) ?>" autocomplete="off"></div>
    <div class="control"><button class="button" type="submit" aria-label="<?= $e($hint) ?>"><?= View::icon('search') ?></button></div>
    <?php if ($filter !== ''): ?><div class="control"><a class="button" href="<?= $e($clear) ?>" aria-label="<?= $e($lang->UI->cancel) ?>"><?= View::icon('x-lg') ?></a></div><?php endif; ?>
</form>
