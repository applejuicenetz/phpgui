<?php
/** @var array<string,array{label:string}> $modes @var string $mode @var string $column @var list<array{name:string,link:string,value:string}> $rows @var object $lang */
$sh = $lang->Share;
?>
<div id="sharestats-root">
    <?= $partial('share-tabs', ['site' => 'sharestats', 'lang' => $lang]) ?>

    <div class="buttons has-addons share-stats-modes" role="group" aria-label="<?= $e($sh->statistics) ?>">
        <?php foreach ($modes as $key => $m): ?>
            <a class="button is-small<?= $key === $mode ? ' is-link is-selected' : '' ?>" href="index.php?site=sharestats&amp;stats=<?= $e(rawurlencode($key)) ?>"<?= $key === $mode ? ' aria-current="true"' : '' ?>><?= $e($m['label']) ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (!$rows): ?>
        <p class="empty-state"><?= $e($lang->UI->no_results) ?></p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table is-fullwidth is-hoverable responsive-table">
                <thead><tr><th>#</th><th><?= $e($column) ?></th><th><?= $e($sh->stats->file) ?></th></tr></thead>
                <tbody>
                <?php foreach ($rows as $i => $row): ?>
                    <tr>
                        <td data-label="#"><strong><?= $i + 1 ?>.</strong></td>
                        <td data-label="<?= $e($column) ?>"><?= $e($row['value']) ?></td>
                        <td class="col-name" data-label="<?= $e($sh->stats->file) ?>"><a href="<?= $e($row['link']) ?>"><?= $e($row['name']) ?></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
