<?php

namespace appleJuiceNETZ\GUI;

use appleJuiceNETZ\Kernel;
use appleJuiceNETZ\appleJuice\Uploads;
use appleJuiceNETZ\appleJuice\Downloads;
use appleJuiceNETZ\appleJuice\Core;

class subs
{
    function appleJuiceNews($zeit, $version)
    {
        if (!empty($_ENV['GUI_SHOW_NEWS'])) {
            echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            		<div class="card">
                		<div class="card-header"><svg class="icon icon-l">
                        	<use xlink:href="themes/CoreUI/vendors/@coreui/icons/svg/free.svg#cil-newspaper"></use>
                        </svg> appleJuice News</div>
                		<div class="card-body">
                		' . $this->getnews($zeit, $version) . '
                		</div>
    				</div>
    			</div>';
        }
    }

    function getnews($zeit, $version)
    {
        $news_file = file_get_contents(sprintf($_ENV['NEWS_URL'], $version ?: '404'), false,
            stream_context_create(
                [
                    'http' => [
                        'ignore_errors' => true,
                    ],
                ]
            ));
        return $news_file;
    }

    static function get_title($site)
    {
        $language = Kernel::getLanguage();
        $lang = $language->translate();

        return $lang->System->pagetitle->$site;
    }

    
	function prozess_bar($balken)
	{
		return '<div class="progress mt-3">
                <div class="progress-bar progress-bar-success progress-bar-striped active col-black" style="width: ' . $balken . '%">
                '  .$balken . ' %</div>
              </div>';	
	}
    function dl_source($wert)
    {
        $language = Kernel::getLanguage();
        $lang = $language->translate();

        if ($wert == 1) $wert = $lang->Downloads->dl_source->src_1;
        if ($wert == 2) $wert = $lang->Downloads->dl_source->src_2;
        if ($wert == 3) $wert = $lang->Downloads->dl_source->src_3;
        if ($wert == 4) $wert = $lang->Downloads->dl_source->src_4;
        if ($wert == 5) $wert = $lang->Downloads->dl_source->src_5;
        if ($wert == 6) $wert = $lang->Downloads->dl_source->src_6;

        return $wert;
    }

    function dl_status($wert)
    {
        $language = new Language($_ENV['GUI_LANGUAGE']);
        $lang = $language->translate();

        if ($wert == 1) $wert = $lang->Downloads->dl_status->status_1;
        if ($wert == 2) $wert = $lang->Downloads->dl_status->status_2;
        if ($wert == 3) $wert = $lang->Downloads->dl_status->status_3;
        if ($wert == 4) $wert = $lang->Downloads->dl_status->status_4;
        if ($wert == 5) $wert = $lang->Downloads->dl_status->status_5;
        if ($wert == 6) $wert = $lang->Downloads->dl_status->status_6;
        if ($wert == 7) $wert = $lang->Downloads->dl_status->status_7;
        if ($wert == 8) $wert = $lang->Downloads->dl_status->status_8;
        if ($wert == 9) $wert = $lang->Downloads->dl_status->status_9;
        if ($wert == 10) $wert = $lang->Downloads->dl_status->status_10;
        if ($wert == 11) $wert = $lang->Downloads->dl_status->status_11;
        if ($wert == 12) $wert = $lang->Downloads->dl_status->status_12;
        if ($wert == 13) $wert = $lang->Downloads->dl_status->status_13;
        if ($wert == 14) $wert = $lang->Downloads->dl_status->status_14;
        if ($wert == 15) $wert = $lang->Downloads->dl_status->status_15;

        return $wert;
    }
    public static function ccts()
    {
        $core = new Core();
        //Info holen
        $statusbar_xml=$core->command("xml","modified.xml?filter=informations");
        $temp2=array_keys($statusbar_xml['NETWORKINFO']);
        $netinfo=&$statusbar_xml['NETWORKINFO'][$temp2[0]];
        
        if($netinfo['CONNECTEDWITHSERVERID'] < 0)
        {
	        include_once("pages/_ccts.php");
	        exit;
        }
    }

//sortiert alle keys von $srcarray nach den werten von $sortkey eine ebene tiefer
    public static function ajsort($srcarray, $sortkey, $type, $reverse)
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


//Dateigroessen die richtige einheit verpassen (groesse in bytes uebergeben)
	public static function sizeformat($bytes, $precision = 2)
    {
    	$i = 0;
        while (abs($bytes) >= 1024 && $i < 6) {
            $bytes /= 1024;
            $i++;
        }

        $bezeichnung = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];
        $newsize = ($i > 0) ? number_format($bytes, $precision) : (int)$bytes;
        return ("$newsize $bezeichnung[$i]");
    }


    public static function cutstring($string, $length)
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

    public static function debug(mixed $data)
    {
        echo '<pre>';
        print_r($data);
        die;
    }
    static function refresh_cache()
    {
    	$Uploadlist = new Uploads();
    	$Downloadlist = new Downloads();
    	
    	$Uploadlist->refresh_cache();
    	$Downloadlist->refresh_cache();
    }
    static function parts($part)
    {
    	$str1 = "part";
    	$str2 = "part2";
    	$str3 = "part3";
    	$Part2 = substr($part, -5, -4);

    if (strpos($part, $str1) !== false) {
    		return " | Part: $Part2";
		}
		if (strpos($part, $str2) !== false) {
    		return " | Part: 2";
		}
		if (strpos($part, $str3) !== false) {
    		return " | Part: 3";
		} else {
    	}
    }
    static function UploadStatus($wert)
    {
        $language = new Language($_ENV['GUI_LANGUAGE']);
        $lang = $language->translate();
  
        if ($wert == 1) $wert = $lang->Uploads->ul_status->status_1;
        if ($wert == 2) $wert = $lang->Uploads->ul_status->status_2;
        if ($wert == 5) $wert = $lang->Uploads->ul_status->status_5;
        if ($wert == 6) $wert = $lang->Uploads->ul_status->status_6;
        if ($wert == 7) $wert = $lang->Uploads->ul_status->status_7;
        

        return $wert;
    }
    
    static function find_children($array, $var, $parent) {
        if($var == "month")
        {
        return count(array_filter($array, function ($e) use ($parent) {
          return $e['month'] === $parent;
        }));
        }
        if($var == "date")
        {
        return count(array_filter($array, function ($e) use ($parent) {
          return $e['date'] === $parent;
        }));
        }
        
      }
}

