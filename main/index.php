<?php
header('Cache-Control: no-cache');
header('Content-Type: text/html; charset=UTF-8');
session_start();

if (isset($_POST['host']) && !empty($_POST['host'])) {
    if(!parse_url($_POST['host'], PHP_URL_SCHEME)) {
        $_SESSION['core_host'] = 'http://' . $_POST['host'];
    }else{
        $_SESSION['core_host'] = $_POST['host'];
    }
}

#echo "<pre>";var_dump($_SESSION);die;

if (empty($_SESSION['core_host'])) {
    header('Location: ../index.php');
    die;
}

if (empty($_SESSION['core_pass'])) {
    if (32 === strlen($_POST['cpass'])) $_SESSION['core_pass'] = ($_POST['cpass']);
    else $_SESSION['core_pass'] = md5($_POST['cpass']);
}

if(isset($_COOKIE['savebw']))
	$_SESSION['phpaj']['savebw']=$_COOKIE['savebw'];
if(isset($_COOKIE['autocleandownloadlist']))
	$_SESSION['phpaj']['autocleandownloadlist']=$_COOKIE['autocleandownloadlist'];
if(isset($_COOKIE['progressbars_type']))
	$_SESSION['phpaj']['progressbars_type']=$_COOKIE['progressbars_type'];
setcookie('savebw', $_SESSION['phpaj']['savebw'], time()+1209600);
setcookie('autocleandownloadlist', $_SESSION['phpaj']['autocleandownloadlist'],
	time()+1209600);
setcookie('progressbars_type', $_SESSION['phpaj']['progressbars_type'],
	time()+1209600);

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" "
	."\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n"
	."<head>\n<title>php-applejuice</title>\n"
	."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n"
	."<meta http-equiv=\"Cache-Control\" content=\"no-cache\" />"
	."</head>\n"
	."<frameset rows=\"70,*,30\" border=\"0\">\n";
	//border=0 is zwar nicht (x)html-standard, sieht aber schoener aus
	echo '<frame src="top.php?'.SID.'" name="oben" frameborder="0" '
		.'scrolling="no" noresize="noresize" />';
	echo '<frame src="start.php?reloadnews='.$_POST['reloadnews']
		.'&amp;reloadshare='.$_POST['reloadshare']
		.'&amp;'.SID.'" name="main" frameborder="0" noresize="noresize" />';
	echo '<frame src="status.php?'.SID.'" name="status" frameborder="0" '
		.'scrolling="no" noresize="noresize" />';

echo "<noframes>
<body>get a browser with support for frames</body>
</noframes>
</frameset>
</html>";
