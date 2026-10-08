<?php
/** @var string $field @var string $label @var string $sort @var string $dir @var array $defaults @var string $site @var string $param */
$param = $param ?? 'sort';
$isCurrent = $sort === $field;
$next = $isCurrent ? ($dir === 'asc' ? 'desc' : 'asc') : ($defaults[$field] ?? 'asc');
$url = 'index.php?' . http_build_query(['site' => $site, $param => $field, $param . '_dir' => $next]);
?>
<a class="sort-link<?= $isCurrent ? ' is-sorted' : '' ?>" href="<?= $e($url) ?>"<?= $isCurrent ? ' aria-sort="' . ($dir === 'asc' ? 'ascending' : 'descending') . '"' : '' ?>><?= $e($label) ?><?php if ($isCurrent): ?> <span aria-hidden="true"><?= $dir === 'asc' ? '↑' : '↓' ?></span><?php endif; ?></a>
