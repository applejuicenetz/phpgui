<?php
header('Cache-Control: no-cache');
header('Content-Type: text/html; charset=UTF-8');

session_start();

require_once 'login.php';

if (empty($_SESSION['core_host'])) {
    header('Location: ../index.php');
    die;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>php-applejuice</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="Cache-Control" content="no-cache"/>
    <link rel="shortcut icon" href="../favicon.ico"/>

</head>
<frameset rows="70,*,30" border="0">
    <frame src="top.php" name="oben" frameborder="0" scrolling="no" noresize="noresize"/>
    <frame src="start.php" name="main" frameborder="0" noresize="noresize"/>
    <frame src="status.php" name="status" frameborder="0" scrolling="no" noresize="noresize"/>
</frameset>
</html>
