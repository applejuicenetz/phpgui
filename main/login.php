<?php

//sprache
$languages = dirlisting(__DIR__ . '/../language', 'xml');

if (!empty($_GET['c_lang']) && array_key_exists($_GET['c_lang'] . '.xml', $languages)) {
    $_SESSION['language']['name'] = $_GET['c_lang'];
} else {
    $_SESSION['language']['name'] = ($_ENV['GUI_LANGUAGE'] ?: 'deutsch');
}

$language_file = file_get_contents(__DIR__ . '/../language/' . $_SESSION['language']['name'] . '.xml');
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

$styles = dirlisting(__DIR__ . '/../style', 'php');

if (isset($_GET['c_style']) && array_key_exists($_GET['c_style'], $styles)) {
    $_SESSION['stylefile'] = $_GET['c_style'];
} else {
    $_SESSION['stylefile'] = $_ENV['GUI_STYLE'];
}

// auto login handler
if (isset($_GET['l']) && !empty($_GET['l'])) {
    $login_data = explode('|', trim(base64_decode(trim($_GET['l']))), 2);

    if (2 === count($login_data)) {
        $_SESSION['core_host'] = $login_data[0];
        $_SESSION['core_pass'] = 32 === strlen($login_data[1]) ? $login_data[1] : md5($login_data[1]);
    }
}

if (isset($_POST['host']) && !empty($_POST['host'])) {
    $_SESSION['core_host'] = $_POST['host'];
}

if (isset($_POST['cpass']) && !empty($_POST['cpass'])) {
    $_SESSION['core_pass'] = 32 === strlen($_POST['cpass']) ? $_POST['cpass'] : md5($_POST['cpass']);
}

require_once __DIR__ . '/../style/' . $_ENV['GUI_STYLE'];

$_SESSION['stylesheet'] = '<link rel="stylesheet" type="text/css" href="../style/' . $stylesheet . '" />';
