<?php
//Neue Subs
require_once '_classes/env.php';

class subs{
	function appleJuiceNews($zeit, $version){
		$subs = new subs();
			
		if(!empty($_ENV['GUI_SHOW_NEWS'])){
			echo'<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            		<div class="panel panel-default" data-panel-collapsable="false" data-panel-fullscreen="false" data-panel-close="false">
                		<div class="panel-heading bg-success"><i class="fa fa-news"></i> appleJuice News</div>
                		<div class="panel-body">
                		'.$subs->getnews($zeit, $version).'
                		</div>
    				</div>
    			</div>';
                		
		}
		
	}
	function getnews($zeit, $version){
		$news_file =file_get_contents(sprintf($_ENV['NEWS_URL'], $version ?: '404'),false,
    		stream_context_create(
        		[
            		'http' => [
                			'ignore_errors' => true,
            		],
        		]
    		));
		return $news_file;
	}
	
	function get_title($site){
		$language = new language($_ENV['GUI_LANGUAGE']);
		$lang = $language->translate();
		
		return $lang->System->pagetitle->$site;
	}

	function prozess($balken){
		$balken = round($balken, 2);
		return'<span class="pie">'.$balken.'/100</span><br>'.$balken.'%';
	}
	function dl_source($wert){
		$language = new language($_ENV['GUI_LANGUAGE']);
		$lang = $language->translate();
		
		if($wert == 1) $wert = $lang->Downloads->dl_source->src_1;
		if($wert == 2) $wert = $lang->Downloads->dl_source->src_2;
		if($wert == 3) $wert = $lang->Downloads->dl_source->src_3;
		if($wert == 4) $wert = $lang->Downloads->dl_source->src_4;
		if($wert == 5) $wert = $lang->Downloads->dl_source->src_5;
		if($wert == 6) $wert = $lang->Downloads->dl_source->src_6;
		
		return $wert;
	}
	function dl_status($wert){
		$language = new language($_ENV['GUI_LANGUAGE']);
		$lang = $language->translate();
		
		if($wert == 1) $wert = $lang->Downloads->dl_status->status_1;
		if($wert == 2) $wert = $lang->Downloads->dl_status->status_2;
		if($wert == 3) $wert = $lang->Downloads->dl_status->status_3;
		if($wert == 4) $wert = $lang->Downloads->dl_status->status_4;
		if($wert == 5) $wert = $lang->Downloads->dl_status->status_5;
		if($wert == 6) $wert = $lang->Downloads->dl_status->status_6;
		if($wert == 7) $wert = $lang->Downloads->dl_status->status_7;
		if($wert == 8) $wert = $lang->Downloads->dl_status->status_8;
		if($wert == 9) $wert = $lang->Downloads->dl_status->status_9;
		if($wert == 10) $wert = $lang->Downloads->dl_status->status_10;
		if($wert == 11) $wert = $lang->Downloads->dl_status->status_11;
		if($wert == 12) $wert = $lang->Downloads->dl_status->status_12;
		if($wert == 13) $wert = $lang->Downloads->dl_status->status_13;
		if($wert == 14) $wert = $lang->Downloads->dl_status->status_14;
		if($wert == 15) $wert = $lang->Downloads->dl_status->status_15;
		
		return $wert;	
	}
}

//Alte Subs
function versions_checker(){}
//Dateigroessen die richtige einheit verpassen (groesse in bytes uebergeben)
function sizeformat($bytesize)
{
    $i = 0;
    while (abs($bytesize) >= 1024 && $i < 6) {
        $bytesize /= 1024;
        $i++;
    }
    $bezeichnung = ['Bytes', "KB", "MB", "GB", "TB", "PB", "EB"];
    $newsize = ($i > 0) ? number_format($bytesize, 2) : (int)$bytesize;
    return ("$newsize $bezeichnung[$i]");
}

function dirlisting($verzeichnis, $endung)
{
    $verzeichnis_liste = [];
    foreach (new DirectoryIterator($verzeichnis) as $fileInfo) {
        if ($endung === $fileInfo->getExtension()) {
            $verzeichnis_liste[$fileInfo->getBasename()] = $fileInfo->getBasename('.' . $endung);
        }
    }

    return $verzeichnis_liste;

}


//sortiert alle keys von $srcarray nach den werten von $sortkey eine ebene tiefer
function ajsort($srcarray, $sortkey, $type, $reverse)
{
    foreach (array_keys($srcarray) as $a) {
        $sortarray["$a"] =& $srcarray[$a][$sortkey];
    }
    if (empty($reverse)) {
        asort($sortarray, $type);
    } else {
        arsort($sortarray, $type);
    }
    return ($sortarray);
}

function cutstring($string, $length)
{
    $changed = 0;
    if (function_exists('mb_strlen')) {
        // utf-8 string sauber kuerzen
        if (mb_strlen($string, 'UTF-8') > ($length + 3)) {
            $string_neu = mb_substr($string, 0, $length, 'UTF-8') . "...";
            $changed = 1;
        }
    } elseif (strlen($string) > ($length + 3)) {
        /* wenn mb extension fehlt: nach bytes kuerzen und hoffen kein
        utf-8 zeichen kaputt zu machen */
        $string_neu = substr($string, 0, $length) . "...";
        $changed = 1;
    }
    return (($changed) ? $string_neu : $string);
}


function debug($var)
{
    echo '<pre>';
    print_r($var);
    die;
}
