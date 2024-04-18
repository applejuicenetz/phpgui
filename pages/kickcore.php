<?php
require_once "_classes/subs.php";
require_once "_classes/core.php";

$core = new Core();

//Language
$language = new language($_ENV['GUI_LANGUAGE']);
$lang = $language->translate();


    $core->command("function", "exitcore");
    echo "<script>
	parent.location.href='index.php';
	</script>";

?>