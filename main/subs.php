<?php
$phpguiversion="v0.25++";
$requiredcoreversion=146;

//Dateigroessen die richtige einheit verpassen (groesse in bytes uebergeben)
function sizeformat($bytesize){
	$i=0;
	while(abs($bytesize)>=1024 && $i<6){
		$bytesize/=1024;
		$i++;
	}
	$bezeichnung=array("Bytes","KB","MB","GB","TB","PB","EB");
	$newsize=($i>0) ? number_format($bytesize,2) : (int) $bytesize;
	return("$newsize $bezeichnung[$i]");
}

function writehead($title,$strict=0){
	Header("Cache-Control: no-cache");
	Header('Content-Type: text/html; charset=UTF-8');
	$text="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	$text.=($strict)?
		"<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" "
			."\"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"
		: "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" "
			."\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	$text.="<html xmlns=\"http://www.w3.org/1999/xhtml\">\n"
		."<head>\n"
		."<meta http-equiv=\"Content-Type\" content=\"text/html; "
			."charset=UTF-8\" />\n"
		."<meta http-equiv=\"Cache-Control\" content=\"no-cache\" />\n"
		."<title>$title</title>\n";
	return $text;
}

function dirlisting($verzeichnis,$endung){
	$verzeichnis_liste=array();
	$verzeichnis_liste[0]=array();
	$verzeichnis_liste[1]=array();
	$verzeichnis_=opendir($verzeichnis);
	while ($verzeichnis_eintrag = readdir ($verzeichnis_)) {
		if(preg_match("/\.$endung\$/i",$verzeichnis_eintrag)){
			array_push($verzeichnis_liste[0],$verzeichnis_eintrag);
			array_push($verzeichnis_liste[1],preg_replace("/\.$endung\$/i",
				"",$verzeichnis_eintrag));
		}
	}
	return($verzeichnis_liste);
}

function getnews($zeit,$version){
	if(empty($_SESSION['cache']['NEWS']['LASTTIMESTAMP']))
		$_SESSION['cache']['NEWS']['LASTTIMESTAMP']=time();
	if(empty($_SESSION['cache']['NEWS']['ITEMS'])
			|| ((time() - $_SESSION['cache']['NEWS']['LASTTIMESTAMP'])>($zeit*60))){
		$_SESSION['cache']['NEWS']['LASTTIMESTAMP']=time();
		$_SESSION['cache']['NEWS']['ITEMS']='';
		$news_file=get_http_file("www.applejuicenet.de",80,
			"/inprog/news.php?version=".$version);
		$_SESSION['cache']['NEWS']['ITEMS']=strtr($news_file,
			array("a href="=>"a target=\"_blank\" href=","<br>"=>"<br />"));
		$_SESSION['cache']['NEWS']['ITEMS']=preg_replace(
			'/&([^;]*?=)/','&amp;$1',$_SESSION['cache']['NEWS']['ITEMS']);
		$_SESSION['cache']['NEWS']['ITEMS']=
			utf8_encode($_SESSION['cache']['NEWS']['ITEMS']);
	}
}

//sortiert alle keys von $srcarray nach den werten von $sortkey eine ebene tiefer
function ajsort($srcarray,$sortkey,$type,$reverse){
	foreach(array_keys($srcarray) as $a){
		$sortarray["$a"]=&$srcarray[$a][$sortkey];
	}
	if(empty($reverse)){
		asort($sortarray,$type);
	}else{
		arsort($sortarray,$type);
	}
	return($sortarray);
}

function cutstring($string,$length){
	$changed=0;
	if(function_exists('mb_strlen')){
		// utf-8 string sauber kuerzen
		if(mb_strlen($string,'UTF-8')>($length+3)){
			$string_neu=mb_substr($string,0,$length,'UTF-8')."...";
			$changed=1;
		}
	}elseif(strlen($string)>($length+3)){
			/* wenn mb extension fehlt: nach bytes kuerzen und hoffen kein
			utf-8 zeichen kaputt zu machen */
			$string_neu=substr($string,0,$length)."...";
			$changed=1;
	}
	return(($changed) ? $string_neu : $string);
}

function progressbar($fortschritt, $fertig, $link = ""){
	$ausgabe="";
	$balkentext=$fertig." (".number_format($fortschritt,0)."%)";
	$ausgabe.="<div style=\"height:12px; width:100px; overflow:hidden;\">";
	switch($_SESSION['phpaj']['progressbars_type']){
		case 1:
			if(!empty($link)) $ausgabe.=$link;
			$ausgabe.= "<img src=\"progressbar.php?progress="
				.number_format($fortschritt,2)."&amp;ready="
				.$fertig
				."&amp;fg=".urlencode($_SESSION['progressbar_fg_color'])
				."&amp;bg=".urlencode($_SESSION['progressbar_bg_color'])
				."\" width=\"100\" height=\"12\" "
				."border=\"0\" alt=\"".$fertig
				." (".number_format($fortschritt,2)."%)\" />";
			if(!empty($link)) $ausgabe.= "</a>";
			break;
		case 2:
			$ausgabe.="<span style=\"width:".round($fortschritt,0)
				."px; background-color:".$_SESSION['progressbar_fg_color']
				."; overflow:hidden; float:left\">";
			$ausgabe.=$link;
			$ausgabe.="<span style=\"position:relative;left:1px;color:"
				.$_SESSION['progressbar_bg_color'].";white-space:nowrap\">"
				.$balkentext."</span>";
			if(!empty($link)) $ausgabe.= "</a>";
			$ausgabe.="</span>";
			$ausgabe.="<span style=\"width:".round(100-$fortschritt,0)
				."px; background-color:".$_SESSION['progressbar_bg_color']
				."; overflow:hidden; float:left\">";
			$ausgabe.=$link;
			$ausgabe.="<span style=\"position:relative;right:"
				.(round($fortschritt,0)-1)."px;color:"
				.$_SESSION['progressbar_fg_color']."; white-space:nowrap\">"
				.$balkentext."</span>";
			if(!empty($link)) $ausgabe.= "</a>";
			$ausgabe.="</span>";
			break;
		case 3:
			if(!empty($link)) $ausgabe.=$link;
			$ausgabe.="<div style=\"position:relative; top:0px; height:12px; "
				."width:100px; text-align:center; z-index:1;\">"
				."<span style=\"font-size:9px; color:#111111;\">"
				."$balkentext</span></div>";
			if(!empty($link)) $ausgabe.= "</a>";
			$ausgabe.="<div style=\"width:100px; height:12px; "
				."background-color:".$_SESSION['progressbar_bg_color']
				."; position:relative; top:-12px; z-index:0;\">"
				."<div style=\"width:".round($fortschritt,0)
				."px; height:12px; background-color:"
				.$_SESSION['progressbar_fg_color'].";\"></div></div>";
			break;
	}
	$ausgabe.="</div>";
	return $ausgabe;
}
	
function get_http_file($ip,$port,$file){
	$result="";
	$fp = @fsockopen($ip, $port, $errno, $errstr, 10);
	if(!$fp){
		$result="Error: $errstr ($errno)<br />\n";
	}else{
		$fp_out="GET $file HTTP/1.0\r\n";
		$fp_out.="Host: $ip:$port\r\n";
		$fp_out.="Connection: Close\r\n\r\n";
		fwrite($fp, $fp_out);
		$header="";
		do $header.=fread($fp,1);
			while(!preg_match('/\\r?\\n\\r?\\n$/',$header) && !feof($fp));
		while(!feof($fp)) $result.=fread($fp,4096);
		fclose($fp);
	}
	return $result;
}

