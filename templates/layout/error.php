<?php
use appleJuiceNETZ\GUI\View;
/** @var int $code @var string $title @var string $text */
$lang = \appleJuiceNETZ\GUI\Format::lang();
?>
<!DOCTYPE html>
<html lang="<?= $e($_ENV['GUI_LANGUAGE'] ?? 'de') ?>" data-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title><?= (int)$code ?> – appleJuice phpGUI</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/apple-icon.svg">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#f5a623">
    <link rel="stylesheet" href="<?= $e(View::asset('vendor/bulma/bulma.min.css')) ?>">
    <link rel="stylesheet" href="<?= $e(View::asset('css/app.css')) ?>">
    <script src="<?= $e(View::asset('js/theme.js')) ?>"></script>
</head>
<body class="login-page">
<main class="login-card box has-text-centered">
    <p class="title is-1"><?= (int)$code ?></p>
    <h1 class="title is-5"><?= $e($title) ?></h1>
    <?php if ($text !== ''): ?><p class="mb-4"><?= $e($text) ?></p><?php endif; ?>
    <a class="button is-primary" href="index.php"><?= $e($lang->UI->retry) ?></a>
    <a class="button" href="index.php?site=logout"><?= $e($lang->UI->logout) ?></a>
</main>
</body>
</html>
