<?php

use appleJuiceNETZ\UI\View;
use appleJuiceNETZ\appleJuice\Core;

$core = new Core();


// Überprüfe, ob der 'site' Parameter leer ist oder nicht existiert
$site = isset($_GET['site']) && !empty($_GET['site']) ? $_GET['site'] : 'Dashboard';

//Logout
if(isset($_GET['serv']) && $_GET['serv'] == "Logout")
{
    // Alle Session-Variablen löschen
    $_SESSION = [];
    // Falls das Session-Cookie existiert, lösche es
    if (ini_get("session.use_cookies"))
    {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, 
            $params["path"], 
            $params["domain"], 
            $params["secure"], 
            $params["httponly"]
        );
    }
    // Beende die Session
    session_destroy();

    echo'<meta http-equiv="refresh" content="0;url=index.php" />';
}elseif(isset($_GET['serv']) && $_GET['serv'] == "Kick-Core")
{
    //Language

$core->command("function", "exitcore");
echo "<script>
	parent.location.href='index.php';
	</script>";
}
View::Template("Header");
View::Template("Navigation");
View::Template("ContentLoader");
View::Template("Footer");