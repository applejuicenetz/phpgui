<?php
/** @var string $kind dl|ul @var string $label @var float $percent @var int $max @var object $lang @var string $note */
?>
<section class="box toolbar-box speed-panel">
    <div class="speed-panel-grid">
        <?= $partial('speed-bar', ['id' => 'aj-' . $kind . '-speed-bar', 'label' => $label, 'percent' => $percent]) ?>
        <?= $partial('limit-form', ['kind' => $kind, 'max' => $max, 'lang' => $lang]) ?>
    </div>
    <p class="speed-panel-note"><?= $e($note ?? '') ?></p>
</section>
