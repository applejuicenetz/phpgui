<?php
use appleJuiceNETZ\GUI\View;
/** @var string $sid @var array $x @var object $lang */
$s = $lang->Search;
$showProgress = $x['running'] && $x['progress'] < 100;
?>
<span class="search-actions" data-search-actions="<?= $e($sid) ?>" hidden>
    <button class="button is-warning" type="submit" form="search-action-form" name="cancelid" value="<?= $e($sid) ?>" data-aj="cancel"<?= $x['running'] ? '' : ' hidden' ?>><?= $e($s->cancle_search) ?></button>
    <button class="button is-danger" type="submit" form="search-action-form" name="deleteid" value="<?= $e($sid) ?>" data-aj="delete"<?= $x['running'] ? ' hidden' : '' ?>><?= View::icon('trash') ?><span><?= $e($s->delet_search) ?></span></button>
    <progress class="progress is-success search-progress" data-search-progress="<?= $e($sid) ?>" value="<?= $e($x['progress']) ?>" max="100"<?= $showProgress ? '' : ' hidden' ?>><?= $e($x['progress']) ?>%</progress>
</span>
