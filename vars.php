<?php
//Language
$standard_language_xml = (!empty($_ENV['GUI_LANGUAGE']) ? $_ENV['GUI_LANGUAGE'] : 'deutsch') . '.xml';

//Standard Login Data
$core_standard_ip = !empty($_ENV['CORE_HOST']) ? $_ENV['CORE_HOST'] : ('http://'. $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);
$core_standard_xml_port = $_ENV['CORE_PORT'] ?? 9851;
$core_standard_host = $core_standard_ip . (empty($core_standard_xml_port) ? '' : ':' . $core_standard_xml_port);

//Style
$standard_stylefile = (!empty($_ENV['GUI_STYLE']) ? $_ENV['GUI_STYLE'] : 'new') . '.php';

//reload intervall (in seconds)
$_SESSION['reloadtime']['status'] = !empty($_ENV['GUI_REFRESH_STATUS']) ? $_ENV['GUI_REFRESH_STATUS'] : 10;
$_SESSION['reloadtime']['downloads'] = !empty($_ENV['GUI_REFRESH_DOWNLOADS']) ? $_ENV['GUI_REFRESH_DOWNLOADS'] : 30;
$_SESSION['reloadtime']['uploads'] = !empty($_ENV['GUI_REFRESH_UPLOADS']) ? $_ENV['GUI_REFRESH_UPLOADS'] : 30;
$_SESSION['reloadtime']['search'] = !empty($_ENV['GUI_REFRESH_SEARCH']) ? $_ENV['GUI_REFRESH_SEARCH'] : 30;

//allowed tags in servermessage
$_SESSION['phpaj']['allowed_servermsg_tags'] = '<a><b><i><u><br>';

//show Progressbars
// 1 = use GD to create images
// 2 = without GD, but text-problems in IE (works fine in Firefox, Opera and Konqueror)
// 3 = without GD, works in IE, Firefox, Opera and Konqueror but the text might be harder to read
$_SESSION['phpaj']['progressbars_type'] = !empty($_ENV['GUI_PROGRESSBARS_TYPE']) ? $_ENV['GUI_PROGRESSBARS_TYPE'] : 1;

//Start
$start_shownews = !empty($_ENV['GUI_SHOW_NEWS']) ? $_ENV['GUI_SHOW_NEWS'] : 1;
$start_showshareinfo = !empty($_ENV['GUI_SHOW_SHARE']) ? $_ENV['GUI_SHOW_SHARE'] : 1;
