<?php

use appleJuiceNETZ\appleJuice\Share;
use appleJuiceNETZ\GUI\Format;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\GUI\View;

/** @var object $lang @var string $phpaj_ownurl */
$p = $lang->Plugins;

$modes = [
    'last' => ['label' => $p->stats_last, 'field' => 'LASTASKED', 'dir' => 1, 'col' => $p->stats_date],
    '-last' => ['label' => $p->stats_nolast, 'field' => 'LASTASKED', 'dir' => 0, 'col' => $p->stats_date],
    'most' => ['label' => $p->stats_most, 'field' => 'ASKCOUNT', 'dir' => 1, 'col' => $p->stats_requests],
    '-most' => ['label' => $p->stats_least, 'field' => 'ASKCOUNT', 'dir' => 0, 'col' => $p->stats_requests],
    'search' => ['label' => $p->stats_search, 'field' => 'SEARCHCOUNT', 'dir' => 1, 'col' => $p->stats_searches],
    '-search' => ['label' => $p->stats_nosearch, 'field' => 'SEARCHCOUNT', 'dir' => 0, 'col' => $p->stats_searches],
];
$mode = isset($_GET['stats'], $modes[$_GET['stats']]) ? $_GET['stats'] : 'most';
$cur = $modes[$mode];

$share = new Share();
$files = $share->statistics($cur['field'], $cur['dir'] === 1);
?>
<div class="tabs is-boxed"><ul>
    <?php foreach ($modes as $key => $m): ?>
        <li class="<?= $key === $mode ? 'is-active' : '' ?>"><a href="<?= View::e($phpaj_ownurl . '&stats=' . rawurlencode($key)) ?>"><?= View::e($m['label']) ?></a></li>
    <?php endforeach; ?>
</ul></div>
<?php if ($files): ?>
    <div class="table-wrap">
        <table class="table is-fullwidth is-hoverable responsive-table">
            <thead><tr><th>#</th><th><?= View::e($cur['col']) ?></th><th><?= View::e($p->stats_file) ?></th></tr></thead>
            <tbody>
            <?php foreach ($files as $i => $f): $val = $f[$cur['field']] ?? ''; ?>
                <tr>
                    <td data-label="#"><strong><?= $i + 1 ?>.</strong></td>
                    <td data-label="<?= View::e($cur['col']) ?>"><?= $cur['field'] === 'LASTASKED' && $val !== '' ? View::e(date('j.n.y - H:i:s', (int)($val / 1000))) : View::e($val) ?></td>
                    <td class="col-name" data-label="<?= View::e($p->stats_file) ?>"><a href="<?= View::e($f['LINK']) ?>"><?= View::e($f['SHORTFILENAME']) ?></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
