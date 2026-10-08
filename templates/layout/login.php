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
    <link rel="stylesheet" href="<?= $e(View::asset('vendor/bulma/bulma.min.css')) ?>">
    <link rel="stylesheet" href="<?= $e(View::asset('css/app.css')) ?>">
    <script src="<?= $e(View::asset('js/theme.js')) ?>"></script>
</head>
<body class="login-page">
<main class="login-card box">
    <img class="login-logo" src="assets/img/applejuice.svg" alt="appleJuice" width="220" height="80">
    <h1 class="title is-4"><?= $e($lang->Login->login) ?></h1>
    <p class="subtitle is-6"><?= $e($lang->Login->headline) ?></p>

    <?php if ($error_host): ?>
        <div class="notification is-danger is-light" role="alert"><?= $e($lang->UI->login_no_core) ?></div>
    <?php endif; ?>
    <?php if ($error_pass): ?>
        <div class="notification is-danger is-light" role="alert"><?= $e($lang->UI->login_wrong_pass) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?login=1" id="login_form">
        <input type="hidden" name="ajfsp_link" value="<?= $e($ajfsp_link) ?>" id="ajfsp_link">
        <div class="field">
            <label class="label" for="chost"><?= $e($lang->UI->login_core_url) ?></label>
            <div class="control"><input class="input" type="url" id="chost" name="host" value="<?= $e($default_host) ?>" required inputmode="url" autocomplete="url"></div>
        </div>
        <div class="field">
            <label class="label" for="cpass"><?= $e($lang->Login->password) ?></label>
            <div class="control"><input class="input" type="password" id="cpass" name="cpass" autocomplete="current-password"></div>
        </div>
        <div class="field">
            <button class="button is-primary is-fullwidth" type="submit"><?= $e($lang->Login->login) ?></button>
        </div>
    </form>
</main>
<script type="module" src="<?= $e(View::asset('js/login.js')) ?>"></script>
</body>
</html>
