<?php /** @var object $lang */ ?>
<section class="box has-text-centered">
    <p class="title is-1">404</p>
    <h2 class="title is-5"><?= $e($lang->System->error404->title) ?></h2>
    <p class="mb-4"><?= $e($lang->System->error404->subtitle) ?></p>
    <a class="button is-primary" href="index.php?site=start"><?= $e($lang->UI->home) ?></a>
</section>
