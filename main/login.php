<?php

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

if (!isset($_SESSION['core_host']) || !isset($_SESSION['core_pass'])) {
    die("<script>parent.location.href='../index.php';</script>");
}