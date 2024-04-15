<?php

session_start();

require_once "_classes/subs.php";
require_once "_classes/core.php";

$core = new Core();

    $core->command("function", "exitcore");
    echo "<script>
	parent.location.href='index.php';
	</script>";

?>