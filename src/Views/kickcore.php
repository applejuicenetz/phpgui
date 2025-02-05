<?php

use appleJuiceNETZ\appleJuice\Core;

$core = new Core();

//Language

$core->command("function", "exitcore");
echo "<script>
	parent.location.href='index.php';
	</script>";
