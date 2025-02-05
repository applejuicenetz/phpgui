<?php

ob_start () ;
phpinfo () ;
$pinfo = ob_get_contents () ;
ob_end_clean () ;
$pinfo = preg_replace ( '%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo );
$pinfo = str_replace ( '<div class="center">', '<div class="col-12 mb-3"><div class="card-body">', $pinfo );
$pinfo = str_replace ( '<table>', '<table class="table table-striped">', $pinfo );



                        echo $pinfo;
                        ?>
