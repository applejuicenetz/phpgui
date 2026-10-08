<?php
/** @var string $id @var string $label @var float $percent */
?>
<div class="speed-bar" title="<?= $e($label) ?>">
    <progress class="progress" id="<?= $e($id) ?>" value="<?= $e($percent) ?>" max="100"><?= $e($percent) ?>%</progress>
    <span class="speed-bar-label" data-speed-label><?= $e($label) ?></span>
</div>
