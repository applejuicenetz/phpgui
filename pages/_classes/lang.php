<?php
class lang{
	function ermitteln(){
		// $languages = dirlisting(__DIR__ . '../_lang', 'xml');
		$core = new core();
			$_SESSION['language']['name'] = $_ENV['GUI_LANGUAGE'];
		

		$language_file = file_get_contents('_lang/' . $_SESSION['language']['name'] . '.xml');
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
	}
}
?>