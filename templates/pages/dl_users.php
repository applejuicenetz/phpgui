<?php
use appleJuiceNETZ\GUI\View;
/** @var int $id @var string $name @var string $size @var string $part @var string $status @var string $status_text @var float $percent @var string $rest @var string $eta @var string $speed @var string $pdl @var array $groups @var object $lang */
$d = $lang->Downloads;
$titles = ['active' => $d->transferring, 'queue' => $d->queue, 'rest' => $d->rest];
?>
<p class="mb-3"><a class="button is-small" href="index.php?site=downloads"><?= View::icon('arrow-left-short') ?><span><?= $e($d->back) ?></span></a></p>

<section class="box">
    <h2 class="title is-5 dl-name"><?= $e($name) ?></h2>
    <div class="progress-line"><strong><?= $e($percent) ?>%</strong><span class="dl-meta"><?= $e($rest) ?><?= $eta !== '' ? ' – ' . $e($eta) : '' ?></span></div>
    <progress class="progress is-success" value="<?= $e($percent) ?>" max="100"><?= $e($percent) ?>%</progress>
    <p class="dl-meta">
        <span class="tag status-<?= $e($status) ?>"><?= $e($status_text) ?></span>
        · <?= $e($size) ?><?= $part !== '' ? ' · ' . $e($part) : '' ?> · <?= $e($d->pdl) ?> <?= $e($pdl) ?> · <?= $e($speed) ?>
    </p>
</section>

<?php foreach ($groups as $key => $list): ?>
    <section class="box">
        <h2 class="box-title"><?= $e($titles[$key]) ?> (<?= count($list) ?>)</h2>
        <?php if (!$list): ?>
            <p class="empty-state"><?= $e($lang->UI->no_results) ?></p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table is-fullwidth is-hoverable responsive-table">
                    <thead><tr><th><?= $e($d->user_source) ?></th><th><?= $e($d->statuss) ?></th><th><?= $e($d->progress) ?></th><th><?= $e($d->speed) ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($list as $u): ?>
                        <tr>
                            <td class="col-name" data-label="<?= $e($d->user_source) ?>">
                                <img class="inline-icon" src="<?= $e($u['direct']['src']) ?>" alt="<?= $e($u['direct']['alt']) ?>" width="16" height="16">
                                <img class="inline-icon" src="<?= $e($u['os']['src']) ?>" alt="<?= $e($u['os']['alt']) ?>" width="16" height="16">
                                <a href="index.php?site=dl_parts&amp;usr_id=<?= (int)$u['id'] ?>"><?= $e($u['nick']) ?></a>
                                <div class="dl-meta"><?= $e($u['origin']) ?><?= $u['version'] !== '' ? ' · ' . $e($u['version']) : '' ?></div>
                            </td>
                            <td data-label="<?= $e($d->statuss) ?>"><?= $e($u['status']) ?></td>
                            <td data-label="<?= $e($d->progress) ?>">
                                <?php if ($u['percent'] !== null): ?>
                                    <div class="progress-line"><strong><?= $e($u['percent']) ?>%</strong></div>
                                    <progress class="progress is-success is-small" value="<?= $e($u['percent']) ?>" max="100"></progress>
                                <?php else: ?>–<?php endif; ?>
                            </td>
                            <td data-label="<?= $e($d->speed) ?>"><?= $e($u['speed']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
