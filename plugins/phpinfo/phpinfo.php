<?php

ob_start();
phpinfo();
$phpinfo = (string)ob_get_clean();
$phpinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo);
echo '<div class="phpinfo">' . $phpinfo . '</div>';
