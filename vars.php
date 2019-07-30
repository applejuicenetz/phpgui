<?php
//Language
$standard_language_xml = 'deutsch.xml';
//$standard_language_xml='english.xml';

//Standard Login Data
$core_standard_ip = !empty($_ENV['CORE_HOST']) ? $_ENV['CORE_HOST'] : $_SERVER['REMOTE_ADDR'];
$core_standard_xml_port = !empty($_ENV['CORE_PORT']) ? $_ENV['CORE_PORT'] : 9851;

//Style
$standard_stylefile = 'new.php';
//$standard_stylefile='tango.php';
//$standard_stylefile='default_green.php';
//$standard_stylefile='default_blue.php';
//$standard_stylefile='default_grey.php';

//reload intervall (in seconds)
$_SESSION['reloadtime']['status'] = 40;
$_SESSION['reloadtime']['downloads'] = 80;
$_SESSION['reloadtime']['uploads'] = 80;
$_SESSION['reloadtime']['search'] = 80;

//allowed tags in servermessage
$_SESSION['phpaj']['allowed_servermsg_tags'] = '<a><b><i><u><br>';

//do not fetch dowload-sources from core?
$_SESSION['phpaj']['savebw'] = 0;

//clean finished and canceled downloads automatically?
$_SESSION['phpaj']['autocleandownloadlist'] = 0;

//show Progressbars
// 1 = use GD to create images
// 2 = without GD, but text-problems in IE (works fine in Firefox, Opera and Konqueror)
// 3 = without GD, works in IE, Firefox, Opera and Konqueror but the text might be harder to read
$_SESSION['phpaj']['progressbars_type'] = 1;

//use gzip compression
$_SESSION['phpaj']['zipped'] = 1;

//Start
$start_shownews = 1;
$start_showshareinfo = 1;
