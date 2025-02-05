<?php

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\Kernel;


$core = new Core();

//Language
$language = Kernel::getLanguage();
$lang = $language->translate();


$core->command("function", "exitcore");
echo "<script>
	parent.location.href='index.php';
	</script>";
