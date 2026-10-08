<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Format;
use appleJuiceNETZ\GUI\View;

/** @var object $lang @var string $phpaj_ownurl @var string $phpaj_show */
$p = $lang->Plugins;
$results = [];

$text = is_string($_POST['linktext'] ?? null) ? $_POST['linktext'] : '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && Csrf::valid()) {
    $lines = explode("\n", $text);
    $subdir = (string)($_POST['subdir'] ?? '');
    $core = new Core();
    $links = [];
    // Beide Eingabewege verarbeiten ausschließlich das AJL-Format.
    while ($lines && trim($lines[0]) !== '100') {
        array_shift($lines);
    }
    array_shift($lines);
    for ($i = 0; $i < count($lines) - 2; $i += 3) {
        [$name, $hash, $size] = [trim($lines[$i]), trim($lines[$i + 1]), trim($lines[$i + 2])];
        if ($size === '') {
            break;
        }
        $links[] = [$name, (int)$size, 'ajfsp://file|' . $name . '|' . $hash . '|' . $size . '/'];
    }
    foreach ($links as [$name, $size, $link]) {
        $reply = (string)$core->command('function', 'processlink?link=' . rawurlencode($link) . '&subdir=' . rawurlencode($subdir));
        $results[] = [$name, $size, $reply];
    }
}
?>
<form method="post" action="<?= View::e($phpaj_ownurl) ?>" id="ajl-form">
    <?= Csrf::field() ?>
    <p class="mb-3">.ajl</p>
    <div class="field"><label class="label" for="ajl-file"><?= View::e($p->ajl_file) ?></label><div class="control"><input class="input" type="file" id="ajl-file" accept=".ajl"></div></div>
    <div class="field"><label class="label" for="ajl-links"><?= View::e($p->ajl_text) ?> (.ajl)</label><div class="control"><textarea class="textarea" id="ajl-links" name="linktext" rows="8" required><?= View::e($text) ?></textarea></div></div>
    <div class="field"><label class="label" for="ajl-subdir"><?= View::e($p->ajl_subdir) ?></label><div class="control"><input class="input" type="text" id="ajl-subdir" name="subdir"></div></div>
    <input type="hidden" name="show" value="<?= View::e($phpaj_show) ?>">
    <button class="button is-primary" type="submit"><?= View::e($p->ajl_submit) ?></button>
</form>
<?php if ($results): ?>
    <ul class="mt-4 result-list">
        <?php foreach ($results as [$name, $size, $reply]): ?>
            <li><?= View::e($name) ?> (<?= View::e(Format::bytes($size)) ?>) ⇒ <strong><?= View::e($reply) ?></strong></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
