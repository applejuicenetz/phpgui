<?php
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\View;
/** @var array $v @var object $lang */
$st = $lang->Settings;
$unit = static fn(string $field) => '<div class="buttons has-addons unit-toggle" data-unit-for="' . $field . '">'
    . '<button type="button" class="button is-selected is-link is-light" data-unit="kb">' . View::e($st->unit_kb) . '</button>'
    . '<button type="button" class="button" data-unit="mb">' . View::e($st->unit_mb) . '</button></div>';
$num = static fn(string $id, string $label, string $val) => '<div class="field"><label class="label" for="' . $id . '">' . View::e($label) . '</label>'
    . '<div class="control"><input class="input" type="number" id="' . $id . '" name="' . $id . '" value="' . View::e($val) . '"></div></div>';
?>
<div class="columns">
    <div class="column is-6">
        <form class="box" method="post" action="index.php?site=settings">
            <?= Csrf::field() ?>
            <input type="hidden" name="change" value="standard">
            <h2 class="box-title"><?= $e($st->head_all) ?></h2>
            <div class="field"><label class="label" for="tempdir"><?= $e($st->tempdir) ?></label><div class="control"><input class="input" type="text" id="tempdir" name="tempdir" value="<?= $e($v['tempdir']) ?>"></div></div>
            <div class="field"><label class="label" for="incdir"><?= $e($st->incomingdir) ?></label><div class="control"><input class="input" type="text" id="incdir" name="incdir" value="<?= $e($v['incdir']) ?>"></div></div>
            <?= $num('c_port', $st->port, $v['port']) ?>
            <?= $num('c_xml_port', $st->xml_port, $v['xml_port']) ?>
            <div class="field"><label class="label" for="nick"><?= $e($st->nick) ?></label><div class="control"><input class="input" type="text" id="nick" name="nick" value="<?= $e($v['nick']) ?>"></div></div>
            <button class="button is-primary" type="submit"><?= $e($st->save) ?></button>
        </form>
    </div>
    <div class="column is-6">
        <form class="box" method="post" action="index.php?site=settings" id="connection-form">
            <?= Csrf::field() ?>
            <input type="hidden" name="change" value="connection">
            <h2 class="box-title"><?= $e($st->head_con) ?></h2>
            <?= $num('maxcon', $st->max_connections, $v['maxcon']) ?>
            <div class="field">
                <label class="label" for="maxul"><?= $e($st->max_ul) ?></label>
                <div class="control limit-row"><input class="input" type="text" inputmode="decimal" id="maxul" name="maxul" value="<?= $e($v['maxul']) ?>"><?= $unit('maxul') ?></div>
            </div>
            <?= $num('uls', $st->speed_per_slot, $v['uls']) ?>
            <div class="field">
                <label class="label" for="maxdl"><?= $e($st->max_dl) ?></label>
                <div class="control limit-row"><input class="input" type="text" inputmode="decimal" id="maxdl" name="maxdl" value="<?= $e($v['maxdl']) ?>"><?= $unit('maxdl') ?></div>
            </div>
            <?= $num('conturn', $st->max_connections_per_turn, $v['conturn']) ?>
            <?= $num('maxdlsrc', $st->max_dl_src, $v['maxdlsrc']) ?>
            <div class="field"><label class="checkbox"><input type="checkbox" name="autoconnect" value="true" <?= $v['autoconnect'] ? 'checked' : '' ?>> <?= $e($st->autoconnect) ?></label></div>
            <button class="button is-primary" type="submit"><?= $e($st->save) ?></button>
        </form>
    </div>
</div>
