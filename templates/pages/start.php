<?php
use appleJuiceNETZ\GUI\View;
/** @var object $lang */
?>
<div class="columns is-multiline dashboard-columns">
    <div class="column is-12-tablet is-8-desktop">
        <section class="box">
            <h2 class="box-title"><?= View::icon('hdd-network') ?> <?= $e($lang->Start->current_server) ?></h2>
            <p class="title is-5 mb-2"><?= $e($server_name) ?></p>
            <?php if ($welcome !== ''): ?><div class="content"><?= $welcome ?></div><?php endif; ?>
        </section>

        <div class="stat-grid">
            <?= $partial('stat-card', ['icon' => 'cloud-download', 'tone' => 'warning', 'label' => $lang->Start->active_downloads, 'href' => 'index.php?site=downloads', 'id' => 'aj-dash-downloads', 'value' => $e($downloads)]) ?>
            <?= $partial('stat-card', ['icon' => 'cloud-upload', 'tone' => 'link', 'label' => $lang->Start->active_uploads, 'href' => 'index.php?site=uploads', 'id' => 'aj-dash-uploads', 'value' => $e($uploads_active)]) ?>
            <?= $partial('stat-card', ['icon' => 'diamond', 'tone' => 'warning', 'label' => $lang->Start->credits, 'href' => 'index.php?site=extras&show=' . rawurlencode('sharestats/sharestats.php'), 'id' => 'aj-dash-credits', 'value' => '<span class="' . ($credits_negative ? 'has-text-danger' : '') . '">' . $e($credits) . '</span>']) ?>
            <?php if ($share): ?>
                <?= $partial('stat-card', ['icon' => 'folder2-open', 'tone' => 'link', 'label' => $share['count'] . ' ' . $lang->Start->share_dat, 'href' => 'index.php?site=shares', 'id' => 'aj-dash-shares', 'value' => $e($share['size'])]) ?>
            <?php endif; ?>
        </div>

        <?php if ($show_news): ?>
            <section class="box news" id="aj-news" hidden>
                <h2 class="box-title"><?= View::icon('newspaper') ?> appleJuice News</h2>
                <div class="content" id="aj-news-content"></div>
            </section>
        <?php endif; ?>
    </div>

    <div class="column is-12-tablet is-4-desktop">
        <section class="box">
            <h2 class="box-title"><?= View::icon('globe2') ?> <?= $e($lang->Start->core_info) ?></h2>
            <dl class="kv">
                <div><dt><?= $e($lang->Start->server_time) ?></dt><dd><?= $e($server_time) ?></dd></div>
                <div><dt>Core Version</dt><dd><?= $e($core_version) ?></dd></div>
                <div><dt><?= $e($lang->Start->op_system) ?></dt><dd><img class="inline-icon" src="<?= $e($core_os_icon['src']) ?>" alt="" width="16" height="16"> <?= $e($core_os) ?></dd></div>
                <div><dt><?= $e($lang->Start->connected_since) ?></dt><dd id="aj-dash-connected"><?= $e($connected_since) ?></dd></div>
                <div><dt><?= $e($lang->Start->open_connections) ?></dt><dd id="aj-dash-connections"><?= $e($connections) ?></dd></div>
                <div><dt><?= $e($lang->Start->bytes_in) ?></dt><dd id="aj-dash-session-dl"><?= $e($session_dl) ?></dd></div>
                <div><dt><?= $e($lang->Start->bytes_out) ?></dt><dd id="aj-dash-session-ul"><?= $e($session_ul) ?></dd></div>
            </dl>
        </section>
        <section class="box">
            <h2 class="box-title"><?= View::icon('diagram-3') ?> <?= $e($lang->Start->network_info) ?></h2>
            <dl class="kv">
                <div><dt><?= $e($lang->Start->download_speed) ?></dt><dd id="aj-dash-dl-speed"><?= $e($dl_speed) ?></dd></div>
                <div><dt><?= $e($lang->Start->upload_speed) ?></dt><dd id="aj-dash-ul-speed"><?= $e($ul_speed) ?></dd></div>
                <div><dt><?= $e($lang->Start->public_ip) ?></dt><dd><?= $e($public_ip) ?></dd></div>
                <div><dt><?= $e($lang->Start->shared_users) ?></dt><dd><?= $e($users) ?></dd></div>
                <div><dt><?= $e($lang->Start->all_data) ?></dt><dd class="kv-nowrap"><?= $e($filecount) ?> (<?= $e($filesize) ?>)</dd></div>
            </dl>
        </section>
    </div>
</div>
