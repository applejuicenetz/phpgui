<?php
use appleJuiceNETZ\GUI\View;
/** @var string $content @var string $site @var string $nav_site @var string $title @var array $scripts @var array $poll */
/** @var array $flash @var array $link_marker @var object $lang @var array $nav */
$ui = $lang->UI;
$pageTitle = trim(($title !== '' ? $title . ' – ' : '') . 'appleJuice phpGUI');
?>
<!DOCTYPE html>
<html lang="<?= $e($_ENV['GUI_LANGUAGE'] ?? 'de') ?>" data-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="<?= $e($csrf) ?>">
    <title><?= $e($pageTitle) ?></title>
    <link rel="icon" type="image/svg+xml" href="assets/img/apple-icon.svg">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#f5a623">
    <link rel="stylesheet" href="<?= $e(View::asset('vendor/bulma/bulma.min.css')) ?>">
    <link rel="stylesheet" href="<?= $e(View::asset('css/app.css')) ?>">
    <script src="<?= $e(View::asset('js/theme.js')) ?>"></script>
</head>
<?php
$rememberLogin = $_SESSION['remember_login'] ?? null;
unset($_SESSION['remember_login']);
?>
<?php if ($rememberLogin !== null): ?>
<script type="application/json" id="remember-login-data"><?= json_encode($rememberLogin, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<?php endif; ?>
<body data-site="<?= $e($site) ?>" data-poll="<?= $e(implode(',', $poll)) ?>" data-refresh="<?= (int)$_ENV['GUI_REFRESH_INTERVAL'] ?>">
<?= View::iconSprite() ?>
<a class="skip-link" href="#main"><?= $e($ui->skip) ?></a>

<div class="app">
    <?= $partial('sidebar', compact('nav', 'nav_site', 'uploads_active', 'downloads_active', 'lang', 'faq_url', 'site')) ?>

    <div class="app-main">
        <?= $partial('topbar', compact('credits', 'credits_negative', 'download_speed', 'upload_speed', 'nick', 'permalink', 'lang', 'title', 'csrf')) ?>

        <main id="main" class="app-content" tabindex="-1">
            <?php foreach ($flash as $f): ?>
                <?= $partial('alert', ['level' => $f['level'], 'title' => $f['title'], 'text' => $f['text'], 'auto' => true]) ?>
            <?php endforeach; ?>

            <?php if ($new_version): ?>
                <?= $partial('alert', [
                    'level' => 'info',
                    'title' => str_replace('%version%', $new_version, $lang->System->version),
                    'text' => $lang->System->version_akt,
                    'href' => 'https://github.com/applejuicenetz/phpgui/releases',
                ]) ?>
            <?php endif; ?>

            <?php if ($firewalled): ?>
                <?= $partial('alert', ['level' => 'danger', 'title' => $lang->System->warning, 'text' => $lang->System->firewall]) ?>
            <?php endif; ?>

            <?php if ($connecting && $site !== 'server'): ?>
                <?= $partial('alert', ['level' => 'warning', 'title' => $ui->connecting_title, 'text' => $ui->connecting_text]) ?>
            <?php endif; ?>

            <?= $content ?>
        </main>

        <footer class="app-footer">
            <span>create with <?= View::icon('heart-fill') ?> by <b>kddk22</b> &amp; <b>red171</b>, inspired by <b>UP</b></span>
            <span class="has-text-weight-bold">v<?= $e($version) ?></span>
        </footer>
    </div>

    <?= $partial('tabbar', compact('nav', 'nav_site', 'uploads_active', 'downloads_active', 'lang')) ?>
</div>

<?= $partial('modals', compact('lang', 'csrf')) ?>

<?php foreach ($link_marker as $raw): ?>
    <span class="is-sr-only">newlinkinfo <?= $e($raw) ?> ok</span>
<?php endforeach; ?>

<script type="module" src="<?= $e(View::asset('js/app.js')) ?>"></script>
<script type="module" src="<?= $e(View::asset('js/links.js')) ?>"></script>
<?php foreach ($scripts as $js): ?>
    <script type="module" src="<?= $e(View::asset('js/' . $js)) ?>"></script>
<?php endforeach; ?>
</body>
</html>
