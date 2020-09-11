<?php
header('Cache-Control: no-cache');
header('Content-Type: text/html; charset=UTF-8');

session_start();
session_unset();

require_once 'main/subs.php';

//sprache
$languages = dirlisting('language', 'xml');

if (!empty($_GET['c_lang']) && array_key_exists($_GET['c_lang'] . '.xml', $languages)) {
    $_SESSION['language']['name'] = $_GET['c_lang'];
} else {
    $_SESSION['language']['name'] = ($_ENV['GUI_LANGUAGE'] ?: 'deutsch');
}

$language_file = file_get_contents('language/' . $_SESSION['language']['name'] . '.xml');
$language_parser = xml_parser_create();
xml_set_element_handler($language_parser, function ($parser, $name, $attrs) {
    $keys = array_keys($attrs);
    $_SESSION['language'][$name] = [];
    foreach ($keys as $l) {
        $_SESSION['language'][$name][$l] = $attrs[$l];
    }
}, null);
xml_parse($language_parser, $language_file);
xml_parser_free($language_parser);

$styles = dirlisting('style', 'php');

if (isset($_GET['c_style']) && array_key_exists($_GET['c_style'], $styles)) {
    $_SESSION['stylefile'] = $_GET['c_style'];
} else {
    $_SESSION['stylefile'] = $_ENV['GUI_STYLE'];
}


require_once 'style/' . $_SESSION['stylefile'];

$_SESSION['stylesheet'] = '<link rel="stylesheet" type="text/css" href="../style/' . $stylesheet . '" />';
?>
<!DOCTYPE html>
<html>
<head>
    <title>php-applejuice</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/<?php echo $stylesheet; ?>"/>
    <style>
        select {
            width: 100%;
        }
    </style>
</head>
<body>
<div align="center">
    <h2><?php echo $_SESSION['language']['LOGIN']['HEADLINE']; ?></h2>
    <form name="loginform" action="main/index.php" method="post" autocomplete="off">
        <table>
            <tr>
                <td>
                    <label for="host"><?php echo $_SESSION['language']['LOGIN']['CORE_HOST']; ?></label>:
                </td>
                <td>
                    <input type="url" id="host" name="host" value="<?php echo ($_ENV['CORE_HOST'] ?: $_ENV['REAL_IP']) . ':' . ($_ENV['CORE_PORT'] ?? 9851); ?>" size="24" required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="cpass"><?php echo $_SESSION['language']['LOGIN']['CORE_PASSWORD']; ?></label>:
                </td>
                <td>
                    <input id="cpass" type="password" name="cpass" value="" size='24' autofocus required/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="c_style"><?php echo $_SESSION['language']['LOGIN']['GUI_STYLE']; ?></label>:
                </td>
                <td>
                    <select id="c_style" name="c_style" size="1" onchange="window.location.href='index.php?c_style='+document.forms[0].c_style.value+'&amp;c_lang='+document.forms[0].c_lang.value;">
                        <?php foreach ($styles as $styleValue => $styleName): ?>
                            <option <?php if ($styleValue === $_SESSION['stylefile']) echo ' selected'; ?> value="<?php echo $styleValue; ?>"><?php echo $styleName; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="c_lang"><?php echo $_SESSION['language']['LOGIN']['GUI_LANGUAGE']; ?></label>:
                </td>
                <td>
                    <select id="c_lang" name="c_lang" size="1" onchange="window.location.href='index.php?c_lang='+document.forms[0].c_lang.value+'&amp;c_style='+document.forms[0].c_style.value;">
                        <?php foreach ($languages as $languageValue => $languageName): ?>
                            <option <?php if ($languageName === $_SESSION['language']['name']) echo ' selected'; ?> value="<?php echo $languageName; ?>"><?php echo $languageName; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div align="right">
                        <input type="submit" value="<?php echo $_SESSION['language']['LOGIN']['OK']; ?>"/>
                    </div>
                </td>
            </tr>
        </table>
    </form>
    <div class="authors">
        Code by UP &middot; maintained by <a href="https://github.com/red171/" target="_blank">red171</a>
    </div>
    <div class="authors">
        <a href="https://github.com/applejuicenet/phpgui" target="_blank"><?php echo PHP_GUI_VERSION; ?></a>
    </div>
</div>
</body>
</html>
