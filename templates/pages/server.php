<?php
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\View;
/** @var list<array> $rows @var object $lang */
$sv = $lang->Server;
$icons = ['connected' => ['wifi', 'has-text-success'], 'trying' => ['wifi', 'has-text-danger'], 'been' => ['wifi', 'has-text-warning'], 'none' => ['wifi-off', 'has-text-grey']];
?>
<div class="toolbar mb-4">
    <form method="post" action="index.php?site=server">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="getservers">
        <button class="button is-primary" type="submit"><?= View::icon('download') ?><span><?= $e($sv->add_more) ?></span></button>
    </form>
</div>
<?php if (!$rows): ?>
    <p class="empty-state box"><?= $e($sv->none) ?></p>
<?php else: ?>
    <div class="card-grid">
        <?php foreach ($rows as $r): [$icon, $tone] = $icons[$r['state']]; ?>
            <article class="box server-card" data-state="<?= $e($r['state']) ?>">
                <header class="server-head">
                    <h2 class="title is-6"><?= $e($r['name']) ?></h2>
                    <span class="server-state <?= $e($tone) ?>"><?= View::icon($icon) ?> <?= $e($r['state_text']) ?></span>
                </header>
                <dl class="kv">
                    <div><dt><?= $e($sv->host) ?></dt><dd><?= $e($r['host']) ?></dd></div>
                    <div><dt><?= $e($sv->port) ?></dt><dd><?= $e($r['port']) ?></dd></div>
                    <div><dt><?= $e($sv->last_connection) ?></dt><dd><?= $e($r['lastseen']) ?></dd></div>
                </dl>
                <footer class="buttons">
                    <form method="post" action="index.php?site=server" data-confirm="<?= $e($sv->delet . ': ' . $r['name']) ?>">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="action" value="removeserver">
                        <input type="hidden" name="serv_id" value="<?= (int)$r['id'] ?>">
                        <button class="button is-danger is-light is-small" type="submit"><?= View::icon('trash') ?><span><?= $e($sv->delet) ?></span></button>
                    </form>
                    <?php if ($r['can_login']): ?>
                        <form method="post" action="index.php?site=server">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="action" value="serverlogin">
                            <input type="hidden" name="serv_id" value="<?= (int)$r['id'] ?>">
                            <button class="button is-link is-light is-small" type="submit"><?= View::icon('box-arrow-in-right') ?><span><?= $e($sv->login) ?></span> (<?= $e($r['tries']) ?>)</button>
                        </form>
                    <?php else: ?>
                        <span class="tag"><?= $e($sv->login) ?> (<?= $e($r['tries']) ?>)</span>
                    <?php endif; ?>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
