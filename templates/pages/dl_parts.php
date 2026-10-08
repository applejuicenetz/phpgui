<?php
use appleJuiceNETZ\GUI\View;
/** @var string $heading @var string $image @var string $back @var object $lang */
$p = $lang->Downloads->parts;
?>
<p class="mb-3"><a class="button is-small" href="<?= $e($back) ?>"><?= View::icon('arrow-left-short') ?><span><?= $e($lang->Downloads->back) ?></span></a></p>
<section class="box">
    <h2 class="box-title"><?= $e($heading) ?></h2>
    <ul class="legend">
        <li><span class="swatch" style="--sw:#0000ff"></span><?= $e($p->available) ?></li>
        <li><span class="swatch" style="--sw:#ff0000"></span><?= $e($p->NA) ?></li>
        <li><span class="swatch" style="--sw:#000000"></span><?= $e($p->received) ?></li>
        <li><span class="swatch" style="--sw:#00ff00"></span><?= $e($p->checked) ?></li>
        <li><span class="swatch" style="--sw:#ffff00"></span><?= $e($p->active_transfer) ?></li>
    </ul>
    <div class="parts-image-wrap"><img class="parts-image" src="<?= $e($image) ?>" alt="<?= $e($heading) ?>" loading="lazy"></div>
</section>
