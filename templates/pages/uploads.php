<?php
use appleJuiceNETZ\GUI\View;
/** @var list<array> $rows @var string $sort @var string $sort_dir @var array $sort_defaults @var int $max @var string $speed_label @var float $speed_percent @var string $limit_text @var object $lang */
$u = $lang->Uploads;
$sortArgs = ['sort' => $sort, 'dir' => $sort_dir, 'defaults' => $sort_defaults, 'site' => 'uploads', 'param' => 'ul_sort'];
?>
<div data-csrf="<?= $e(\appleJuiceNETZ\GUI\Csrf::token()) ?>" id="ul-root">
    <?= $partial('speed-panel', ['kind' => 'ul', 'label' => $speed_label, 'percent' => $speed_percent, 'max' => $max, 'lang' => $lang, 'note' => $limit_text]) ?>

    <section class="box">
        <?php if (!$rows): ?>
            <p class="empty-state"><?= View::icon('inbox', 'is-large') ?><br><?= $e($u->none) ?></p>
        <?php else: ?>
            <div class="sort-bar">
                <?= $partial('sort-link', $sortArgs + ['field' => 'name', 'label' => $u->files]) ?>
                <?= $partial('sort-link', $sortArgs + ['field' => 'status', 'label' => $u->statuss]) ?>
            </div>
            <div class="table-wrap">
                <table class="table is-fullwidth is-hoverable responsive-table" id="ul-table">
                    <thead><tr>
                        <th class="col-check"></th>
                        <th><?= $partial('sort-link', $sortArgs + ['field' => 'name', 'label' => $u->files]) ?></th>
                        <th><?= $partial('sort-link', $sortArgs + ['field' => 'status', 'label' => $u->statuss]) ?></th>
                        <th><?= $e($u->progress) ?></th>
                        <th class="th-shrink" title="<?= $e($u->speed) ?>"><?= $e($u->speed) ?></th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr id="ul-<?= (int)$r['id'] ?>" data-id="<?= (int)$r['id'] ?>">
                            <td class="col-check" data-label=""><img class="inline-icon" data-aj="direct" src="<?= $e($r['direct']['src']) ?>" alt="<?= $e($r['direct']['alt']) ?>" width="16" height="16"></td>
                            <td class="col-name" data-label="<?= $e($u->files) ?>">
                                <div class="dl-name" title="<?= $e($r['name']) ?>"><?= $e($r['name']) ?></div>
                                <div class="dl-meta"><?= $e($u->username) ?>: <?= $e($r['nick']) ?> · <?= $e($u->pdl) ?>: <?= $e($r['priority']) ?></div>
                            </td>
                            <td data-label="<?= $e($u->statuss) ?>"><span class="tag ul-<?= $e($r['status']) ?>" data-aj="status"><?= $e($r['status_text']) ?></span></td>
                            <td class="col-progress" data-label="<?= $e($u->progress) ?>">
                                <div class="progress-line"><strong data-aj="label"><?= $e($r['label']) ?></strong><span class="dl-meta" data-aj="sub"><?= $e($r['sub']) ?></span></div>
                                <progress class="progress is-success is-small" value="<?= $e($r['percent']) ?>" max="100" data-aj="bar"><?= $e($r['percent']) ?>%</progress>
                            </td>
                            <td data-label="<?= $e($u->speed) ?>" data-aj="speed"><?= $e($r['speed']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>
