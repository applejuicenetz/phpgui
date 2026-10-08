<?php
use appleJuiceNETZ\GUI\View;
/** @var bool $error_host @var bool $error_pass @var string $default_host @var string $ajfsp_link @var string $title */
$lang = \appleJuiceNETZ\GUI\Format::lang();
?>
<!DOCTYPE html>
<html lang="<?= $e($_ENV['GUI_LANGUAGE'] ?? 'de') ?>" data-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <title><?= $e($title) ?> – appleJuice phpGUI</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/apple-icon.svg">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#f5a623">
    <link rel="stylesheet" href="<?= $e(View::asset('vendor/bulma/bulma.min.css')) ?>">
    <link rel="stylesheet" href="<?= $e(View::asset('css/app.css')) ?>">
    <script src="<?= $e(View::asset('js/theme.js')) ?>"></script>
</head>
<body class="login-page">
<?= View::iconSprite() ?>
<main class="login-card box">
    <div class="login-apple" role="img" aria-label="appleJuice"></div>

    <?php if ($error_host): ?>
        <div class="notification is-danger is-light" role="alert"><?= $e($lang->UI->login_no_core) ?></div>
    <?php endif; ?>
    <?php if ($error_pass): ?>
        <div class="notification is-danger is-light" role="alert"><?= $e($lang->UI->login_wrong_pass) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?login=1" id="login_form" data-login-error="<?= $error_host || $error_pass ? '1' : '0' ?>"> <input type="hidden" name="remember_login" id="remember-login-value" value="0">
        <input type="hidden" name="ajfsp_link" value="<?= $e($ajfsp_link) ?>" id="ajfsp_link">
        <div class="field has-addons">
            <p class="control"><label class="button is-static" for="chost"><?= $e($lang->UI->login_core_url) ?></label></p>
            <p class="control is-expanded"><input class="input" type="url" id="chost" name="host" value="<?= $e($default_host) ?>" required inputmode="url" autocomplete="url"></p>
            <p class="control"><button type="button" class="button" title="<?= $e($lang->Login->url_info) ?>" aria-label="<?= $e($lang->Login->url_info) ?>"><?= View::icon('info-circle') ?></button></p>
        </div>
        <div class="field has-addons">
            <p class="control"><label class="button is-static" for="cpass"><?= $e($lang->Login->password) ?></label></p>
            <p class="control is-expanded"><input class="input" type="password" id="cpass" name="cpass" autocomplete="current-password"></p>
            <p class="control"><button type="button" class="button" title="<?= $e($lang->Login->password_info) ?>" aria-label="<?= $e($lang->Login->password_info) ?>"><?= View::icon('info-circle') ?></button></p>
        </div>
        <div class="field">
            <label class="login-remember"><input type="checkbox" id="remember-login"><span class="login-remember-switch" aria-hidden="true"></span><span><?= $e($lang->Login->remember) ?></span></label>
        </div>
        <div class="field">
            <button class="button is-primary is-fullwidth" type="submit"><?= $e($lang->Login->login) ?></button>
        </div>
    </form>
</main>
<footer class="login-footer">
    <a href="https://github.com/applejuicenetz/phpgui" target="_blank" rel="noopener noreferrer">v<?= $e(PHP_GUI_VERSION) ?></a>
</footer>
<script type="module" src="<?= $e(View::asset('js/login.js')) ?>"></script>
</body>
</html>
