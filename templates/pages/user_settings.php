<?php
use appleJuiceNETZ\GUI\Csrf;
/** @var array $v @var object $lang */
$st = $lang->Settings;
?>
<form class="box" method="post" action="index.php?site=user_settings">
    <?= Csrf::field() ?>
    <input type="hidden" name="change" value="nick">
    <h2 class="box-title"><?= $e($st->user_title) ?></h2>
    <div class="field"><label class="label" for="nick"><?= $e($st->nick) ?></label><div class="control"><input class="input" type="text" id="nick" name="nick" value="<?= $e($v['nick']) ?>"></div></div>
    <button class="button is-primary" type="submit"><?= $e($st->save) ?></button>
</form>
