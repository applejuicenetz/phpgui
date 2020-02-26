<?php
header('Cache-Control: no-cache');
header('Content-Type: text/html; charset=UTF-8');

session_start();

include_once 'login.php';

if (empty($_SESSION['core_host'])) {
    header('Location: ../index.php');
    die;
}

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
