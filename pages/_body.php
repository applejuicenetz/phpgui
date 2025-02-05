<?php

use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\GUI\subs;
use appleJuiceNETZ\GUI\template;
use appleJuiceNETZ\Kernel;

$template = new template();
$Servers = new Server();
$subs = new subs();

$language = Kernel::getLanguage();
$lang = $language->translate();

subs::ccts();

require(GUI_ROOT . "/pages/_header.php");

if (!isset($_GET["show"])) $_GET["show"] = "";
if (empty($_GET["site"])) $_GET["site"] = "start";
// Wichtige Fehlermeldungen auf allen Seiten anzeigen
//Firewall aktiv
if ($Servers->netstats['firewalled'] === 'true') {
    $template->alert("danger", "Firewall", $lang->System->firewall);
}
//veraltete Version
$gui->check_version(PHP_GUI_VERSION);
// Downloads anzeigen

if (!empty($_SESSION['ajfsp_link']) && empty($_REQUEST['ajfsp_link'])) {
    $_REQUEST['ajfsp_link'] = $_SESSION['ajfsp_link'];
    $_REQUEST['showlinkpage'] = 1;
    unset($_SESSION['ajfsp_link']);
}

if (!empty($_REQUEST['ajfsp_link'])) {

    $regexe = [
        // ajfsp://file|ajcore-0.31.149.110.jar|653f4d793595e65bbbe58c0c55620589|313164/
        '#ajfsp://(file)\|([^|]*)\|([a-z0-9]{32})\|([\d]*)/#',

        // ajfsp://server|knastbruder.applejuicenet.de|9855/
        '#ajfsp://(server)\|([^|]*)\|([\d]{1,5})/#',

        // ajfsp://file|ajcore-0.31.149.110.so|653f4d793595e65bbbe58c0c55620589|313164|123.123.123.123:9850/
        '#ajfsp://(file)\|([^|]*)\|([a-z0-9]{32})\|([\d]*)\|[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}:[\d]{1,5}/#',

        // ajfsp://file|ajcore-0.31.149.110.jar|653f4d793595e65bbbe58c0c55620589|313164|123.123.123.123:9850:knastbruder.applejuicenet.de:9855/
        '#ajfsp://(file)\|([^|]*)\|([a-z0-9]{32})\|([\d]*)\|[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}:[\d]{1,5}:[^:]*:[\d]{1,5}/#',
    ];

    $links = [];

    foreach ($regexe as $regex) {
        preg_match_all($regex, urldecode($_REQUEST['ajfsp_link']), $matches, PREG_SET_ORDER);
        $links = array_merge($links, $matches);
    }

    foreach ($links as $link) {

        //Infos fr Dateilink anzeigen + im hauptfenster die downloads zeigen
        if ('file' === $link[1]) {

            $text = htmlspecialchars($link[2]) . ' (' . subs::sizeformat($link[4]) . ')';
            $text .= '<span class="hidden">newlinkinfo ' . $link[0] . ' ok</span>'; // for browser extension matching

            if ($core->command('function', 'processlink?link=' . urlencode($link[0])) == "ok") {
                $template->alert("success", $lang->Downloads->get_start, $text);
            }

            if (!empty($_REQUEST['showlinkpage'])) {
                echo "<script>parent.main.location.href='?site=downloads';</script>";
            }
        }
        //Infos für Serverlink anzeigen + im hauptfenster die server zeigen
        if ('server' === $link[1]) {
            echo htmlspecialchars($link[2]) . ':' . htmlspecialchars($link[3]) . ' &rArr; ';
            echo $core->command('function', 'processlink?link=' . urlencode($link[0]));
            if (!empty($_REQUEST['showlinkpage'])) {
                echo "<script>parent.main.location.href='server.php';</script>";
            }
        }


    }
}

$currentUrl = sprintf(
    '%s://%s%s',
    isset($_SERVER['HTTPS']) ? 'https' : 'http',
    $_SERVER['HTTP_HOST'],
    str_replace('top.php', 'index.php', $_SERVER['REQUEST_URI'])
);

$permaLink = sprintf('%s|%s', $_SESSION['core_host'], $_SESSION['core_pass']);

$page = basename($_GET['site'] ?? 'start');

if (file_exists(GUI_ROOT . "/pages/" . $page . ".php")) {
    require_once(GUI_ROOT . "/pages/" . $page . ".php");
} else {
    require_once(GUI_ROOT . "/pages/404.php");
    $_GET['site'] = "404";
}

require(GUI_ROOT . "/pages/_footer.php");
