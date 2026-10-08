<?php
use appleJuiceNETZ\GUI\View;
/** @var string $kind dl|ul @var int $max @var object $lang */
$kb = $max / 1024;
$ui = $lang->UI;
?>
<div class="field has-addons limit-form" data-limit="<?= $e($kind) ?>" data-max="<?= (int)$max ?>">
    <p class="control"><span class="button is-static"><?= View::icon('speedometer2') ?></span></p>
    <p class="control is-expanded"><input class="input" type="text" inputmode="decimal" name="limit" value="<?= $e(rtrim(rtrim(number_format($kb, 2, '.', ''), '0'), '.') ?: '0') ?>" aria-label="<?= $e($lang->Downloads->limit) ?>"></p>
    <p class="control"><button type="button" class="button is-selected is-link is-light" data-unit="kb"><?= $e($lang->Downloads->unit_kb) ?></button></p>
    <p class="control"><button type="button" class="button" data-unit="mb"><?= $e($lang->Downloads->unit_mb) ?></button></p>
    <p class="control"><button type="button" class="button is-primary" data-limit-apply aria-label="<?= $e($lang->Settings->save) ?>"><?= View::icon('check-lg') ?></button></p>
</div>
