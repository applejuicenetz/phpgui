<?php

session_start();

include_once "subs.php";
include_once "login.php";
include_once "classes/class_core.php";

$core = new Core();

echo writehead('phpaj');
echo $_SESSION['stylesheet'];
echo '</head>
<body class="top">
<form method="post" action="' . $_SERVER['PHP_SELF'] . '" name="linkform">
<input name="showlinkpage" type="hidden" value="1" />
<input name="' . session_name() . '" type="hidden" value="' . session_id() . '" />
<table><tr>';

echo "<td class=\"link\"><label for=\"link\">"
    . $_SESSION['language']['LINK']['LINK'] . "</label>:</td><td class=\"link\">"
    . "<input id=\"link\" name=\"ajfsp_link\" size=\"60\" /></td>";
echo "<td class=\"link\"><input type=\"submit\" value=\""
    . $_SESSION['language']['LINK']['OK'] . "\" /></td>";

if (!empty($_SESSION['ajfsp_link']) && empty($_REQUEST['ajfsp_link'])) {
    $_REQUEST['ajfsp_link'] = $_SESSION['ajfsp_link'];
    $_REQUEST['showlinkpage'] = 1;
    unset($_SESSION['ajfsp_link']);
}

if (!empty($_REQUEST['ajfsp_link'])) {

    preg_match_all('#ajfsp://(file|server)\|([^|]*)\|([a-z0-9]{32})\|([0-9]*)/#', urldecode($_REQUEST['ajfsp_link']), $links, PREG_SET_ORDER);
    echo '<td id="newlinkinfo">';

    foreach ($links as $link) {

        //Infos fr Dateilink anzeigen + im hauptfenster die downloads zeigen
        if ('file' === $link[1]) {
            echo htmlspecialchars($link[2]) . ' (' . sizeformat($link[4]) . ') &rArr; ';
            echo $core->command('function', 'processlink?link=' . urlencode($link[0])) . ' &middot; ';

            if (!empty($_REQUEST['showlinkpage'])) {
                echo "<script>parent.main.location.href='downloads.php';</script>";
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

        echo '<script>window.setTimeout(function() {document.getElementById("newlinkinfo").style.display="none"}, 5000);</script>';
    }
    echo '</td>';
}
echo '</tr></table>';

if (!empty($_REQUEST['killcore'])) {
    $core->command("function", "exitcore");
    echo "<script>
	parent.location.href='../index.php';
	</script>";
}

echo "<div class=\"tabs\">";
echo "<a href=\"start.php?" . SID . "\" target=\"main\">"
    . "<img src=\"../style/" . $_SESSION['tabs_start_icon']
    . "\" alt=\"\" />" . $_SESSION['language']['TABS']['START'] . "</a> ";
echo "<a href=\"downloads.php?" . SID . "\" target=\"main\">"
    . "<img src=\"../style/" . $_SESSION['tabs_downloads_icon']
    . "\" alt=\"\" />" . $_SESSION['language']['TABS']['DOWNLOADS'] . "</a> ";
echo "<a href=\"uploads.php?" . SID . "\" target=\"main\">"
    . "<img src=\"../style/" . $_SESSION['tabs_uploads_icon']
    . "\" alt=\"\" />" . $_SESSION['language']['TABS']['UPLOADS'] . "</a> ";
echo "<a href=\"shares.php?" . SID . "\" target=\"main\">"
    . "<img src=\"../style/" . $_SESSION['tabs_share_icon']
    . "\" alt=\"\" />" . $_SESSION['language']['TABS']['SHARE'] . "</a> ";
echo "<a href=\"search.php?" . SID . "\" target=\"main\">"
    . "<img src=\"../style/" . $_SESSION['tabs_search_icon']
    . "\" alt=\"\" />" . $_SESSION['language']['TABS']['SEARCH'] . "</a> ";
echo "<a href=\"server.php?" . SID . "\" target=\"main\">"
    . "<img src=\"../style/" . $_SESSION['tabs_server_icon']
    . "\" alt=\"\" />" . $_SESSION['language']['TABS']['SERVER'] . "</a> ";
echo "<a href=\"settings.php?" . SID . "\" target=\"main\">"
    . "<img src=\"../style/" . $_SESSION['tabs_settings_icon']
    . "\" alt=\"\" />" . $_SESSION['language']['TABS']['SETTINGS'] . "</a> ";
echo "<a href=\"extras.php?" . SID . "\" target=\"main\">"
    . "<img src=\"../style/" . $_SESSION['tabs_extras_icon']
    . "\"alt=\"\" />" . $_SESSION['language']['TABS']['EXTRAS'] . "</a> ";
echo "<a href=\"javascript:if(confirm('"
    . addslashes($_SESSION['language']['TABS']['COREKILL_QUESTION'])
    . "')) window.location.href='" . $_SERVER['PHP_SELF'] . "?" . SID . "&amp;killcore=1'\" "
    . "style=\"margin-left:50px;\">"
    . "<img src=\"../style/" . $_SESSION['tabs_corekill_icon']
    . "\" alt=\"\" />" . $_SESSION['language']['TABS']['COREKILL'] . "</a> ";
echo "<a href=\"../index.php?logout\" target=\"_parent\">"
    . "<img src=\"../style/" . $_SESSION['tabs_logout_icon']
    . "\" alt=\"\" />" . $_SESSION['language']['TABS']['LOGOUT'] . "</a> ";
echo "</div>";

echo '</form>
</body>
</html>';
