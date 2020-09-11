<?php
Header("Cache-Control: no-cache");
Header("Content-type: image/png");
session_start();
require_once "subs.php";
require_once "classes/class_core.php";
require_once "classes/class_share.php";
require_once "classes/class_downloads.php";
$core = new Core;

if(!empty($_GET['dl_id'])){
	$partliste=$core->command("xml","downloadpartlist.xml?id=".$_GET['dl_id']);
}elseif(!empty($_GET['usr_id'])){
	$partliste=$core->command("xml","userpartlist.xml?id=".$_GET['usr_id']);
}else die("wtf");

$filesize=array_keys($partliste['FILEINFORMATION']);
$filesize=$filesize[0];

//bildgroesse
	$zeilen=14;
	$breite=500;
	$zeilenhoehe=14;
	$hoehe=$zeilen*$zeilenhoehe;

$pixelsize=($zeilen*$breite)/$filesize;
//echo "$pixelsize Pixel pro Byte<br>";

$image = imagecreate($zeilen*$breite,$zeilenhoehe);

//farben
	$rot = imagecolorallocate($image,255,0,0);
	$fertig = imagecolorallocate($image,0,0,0);
	$ok = imagecolorallocate($image,0,255,0);
	//verschiedene blautoene
	$blau=array();
	for($h=1;$h<=10;$h++){
		$blau[$h]=imagecolorallocate($image,250-(25*$h),250-(25*$h),255);
	}
	//verschiedene gelbtoene
	$gelb=array();
	for($h=0;$h<=10;$h++){
		$gelb[$h]=imagecolorallocate($image,255-(12*$h),255-(12*$h),0);
	}


$obenlinks_y=0;
$untenrechts_y=$zeilenhoehe;
$fertig_seit=-1;
$nextpart=array_keys($partliste['PART']);
$x=0;
$c=0;
foreach(array_keys($partliste['PART']) as $a){
	$c++;
	$obenlinks_x=($pixelsize*$a);
	//breite das parts im bild bestimmen
		if($a!=$filesize && !empty($nextpart[$c])){
			//nach groesse berechnen
			$untenrechts_x=$obenlinks_x+($pixelsize*($nextpart[$c]-$a));
		}else{
			//bis zum bildende bei letztem part
			$untenrechts_x=$zeilen*$breite;
		}
	//passende farbe waehlen
	switch ($partliste['PART'][$a]['TYPE']) {
		case "-1":
			//um spaeter gruene teile zu bestimmen
			if($fertig_seit==-1) $fertig_seit=$a;
			$farbe=$fertig;
			break;
		case "0":
			$farbe=$rot;
			break;
		default:
			$anzahl=$partliste['PART'][$a]['TYPE'];
			//bei mehr als 10 quellen gleiche farbe wie bei 10
			if($anzahl>10) $anzahl=10;
			$farbe=$blau[$anzahl];
			break;
	}
	//wenn nach einem geladenen part ein noch nicht geladener kommt pruefen,
	// ob gepruefte bereiche vorhanden sind
	if(!empty($_GET['dl_id']) && $fertig_seit!==-1
			&& $partliste['PART'][$a]['TYPE']!=="-1"){
		$x=ceil($fertig_seit/1048576)*1048576;		//anfang von gruen finden
		$anzahl_mb_checked=floor(($a-$x)/1048576);	//nur ganze MB nehmen
		//falls notwendig gruenen teil einzeichnen
		if($anzahl_mb_checked>0 || $a==$filesize){
			$ok_start=($pixelsize*$x);
			if($a!=$filesize)
				$ok_ende=$ok_start+($anzahl_mb_checked*$pixelsize*1048576);
				else
				//wegen letztem part
				$ok_ende=$untenrechts_x;
			//schwarz vor gruen
			if($x!=$fertig_seit)
				imagefilledrectangle($image,($pixelsize*$fertig_seit),
					$obenlinks_y,$ok_start,$untenrechts_y, $fertig);
			//gruen
			imagefilledrectangle($image,$ok_start,$obenlinks_y,
				$ok_ende,$untenrechts_y, $ok);
			//schwarz hinter gruen
			if($ok_ende!=$obenlinks_x)
				imagefilledrectangle($image,$ok_ende,$obenlinks_y,
					$obenlinks_x,$untenrechts_y, $fertig);
		}else{
			//schwarz ohne gruen
			imagefilledrectangle($image,($pixelsize*$fertig_seit),
				$obenlinks_y,$obenlinks_x,$untenrechts_y, $fertig);
		}
		//wert, bei dem geladener bereich anfaengt wieder auf default setzen
		$fertig_seit=-1;
	}
	//"normale" parts einzeichnen
	if(empty($_GET['dl_id']) || $partliste['PART'][$a]['TYPE']!=="-1")
	imagefilledrectangle($image,$obenlinks_x,$obenlinks_y,
		$untenrechts_x,$untenrechts_y, $farbe);
}

$Downloadlist = new Downloads;
//ladende parts einzeichnen
if(!empty($_GET['dl_id'])){
    $dl = $Downloadlist->download($_GET['dl_id']);
    if(!empty($dl['phpaj_loading_parts'])){
        // alle ladenden parts vom dl einzeichnen
        foreach(array_keys($dl['phpaj_loading_parts']) as $a){
            $current_dlpart =& $dl['phpaj_loading_parts'][$a];
            $obenlinks_x=($pixelsize*$current_dlpart['DOWNLOADFROM']);
            $untenrechts_x=($pixelsize*$current_dlpart['DOWNLOADTO']);
            $part_prozent=floor((
                ($current_dlpart['ACTUALDOWNLOADPOSITION']
                    - $current_dlpart['DOWNLOADFROM'])/
                ($current_dlpart['DOWNLOADTO']
                    - $current_dlpart['DOWNLOADFROM']))*10);
            imagefilledrectangle($image,$obenlinks_x,$obenlinks_y,
                $untenrechts_x,$untenrechts_y, $gelb[$part_prozent]);
        }
    }
}elseif(!empty($_GET['usr_id'])){
        $current_dlpart=$Downloadlist->user($_GET['usr_id']);
        if($current_dlpart['DOWNLOADFROM']>-1){
            // wenn was von dem user geladen wird -> part einzeichnen
            $obenlinks_x=($pixelsize*$current_dlpart['DOWNLOADFROM']);
            $untenrechts_x=($pixelsize*$current_dlpart['DOWNLOADTO']);
            $part_prozent=floor((
                ($current_dlpart['ACTUALDOWNLOADPOSITION']
                    - $current_dlpart['DOWNLOADFROM'])/
                ($current_dlpart['DOWNLOADTO']
                    - $current_dlpart['DOWNLOADFROM']))*10);
            imagefilledrectangle($image,$obenlinks_x,$obenlinks_y,
                $untenrechts_x,$untenrechts_y, $gelb[$part_prozent]);
        }
}

//in zeilen aufteilen
$image2 = imagecreate($breite,$hoehe+$zeilen);
$grau = imagecolorallocate($image2,200,200,200);
for($i=0;$i<$zeilen;$i++){
	imagecopy($image2,$image,0,($zeilenhoehe*$i)+$i,$breite*$i,0,
		$breite,$zeilenhoehe);
}

//bild ausgeben
imagepng($image2);

imagedestroy($image);
imagedestroy($image2);
