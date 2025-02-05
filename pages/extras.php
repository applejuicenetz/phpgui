<?php
require_once "plugins/register.php";
$Plugin = new Plugin();
$Plugin->Find_Plugins();


$phpaj_pluginurllist = array();

foreach ($Plugin->liste as $a) {
    array_push($phpaj_pluginurllist, $a[2]);
}
// $phpaj_ownurl enthält die url zur aufgerufenen seite
// $phpaj_show enthält die url zum plugin und muss vom plugin immer
//als "show" mit get oder post übergeben werden
$phpaj_ownurl = $_SERVER['PHP_SELF'] . "?" . SID;
$phpaj_show = "";
if (!empty($_GET['show'])) $phpaj_show = $_GET['show'];
if (!empty($_POST['show'])) $phpaj_show = $_POST['show'];
if (!empty($phpaj_show)) {
    $phpaj_ownurl .= "&show=" . $phpaj_show;
    //check, ob die an show übergebene url auch zu einem plugin gehört ;)
    if (in_array($phpaj_show, $phpaj_pluginurllist))
        include "plugins/" . $phpaj_show;
}
