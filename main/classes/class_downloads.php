<?php
require_once "classes/class_core.php";
require_once "subs.php";

class Downloads{
	var $cache;
	var $core;
	var $subdirs;

	function __construct(){
		$this->cache =& $_SESSION['cache']['DOWNLOADS'];
		$this->core = new Core();
	}

	//stand der daten
	function time(){
		return date("j.n.y - H:i:s",
			($this->cache['TIME']['VALUES']['CDATA'])/1000);
	}

	//neue infos vom core holen
	function refresh_cache(){
		$corecommand_filter="down;ids;user";
		//alte id-liste aus cache loeschen
		if(!empty($this->cache['IDS']))
			unset($this->cache['IDS']);
		if(empty($this->cache['LASTTIMESTAMP']))
			$this->cache['LASTTIMESTAMP']=0;
		$this->cache=
			$this->core->command("xml","modified.xml?timestamp="
			.$this->cache['LASTTIMESTAMP']
			."&filter=".$corecommand_filter,$this->cache);
		$this->cache['LASTTIMESTAMP']=$this->cache['TIME']['VALUES']['CDATA'];
		$this->subdirs=array();
		$this->process_sources();
	}

	//quelleninfos verarbeiten
	function process_sources(){
		if(!empty($this->cache['DOWNLOAD'])){
			//infos der downloads aus quellen zur�cksetzen
			foreach(array_keys($this->cache['DOWNLOAD']) as $a){
				//alte downloads loeschen
				if(empty($this->cache['IDS']['VALUES']['DOWNLOADID'][$a])){
					unset($this->cache['DOWNLOAD'][$a]);
					continue;
				}
				$download=&$this->cache['DOWNLOAD'][$a];
				$download['phpaj_quellen_gesamt']=0;
				$download['phpaj_quellen_dl']=0;
				$download['phpaj_dl_speed']=0;
				$download['phpaj_ids_quellen_queue']=array();
				$download['phpaj_ids_quellen_dl']=array();
				$download['phpaj_ids_quellen_rest']=array();
				$download['phpaj_quellen_queue']=0;
				$download['phpaj_loading_parts']=array();
				$download['phpaj_STATUS']=$download['STATUS'];
				//Fortschritt auf 100% wenn Status = Fertig
				if($download['STATUS']==="14")
					$download['READY']=$download['SIZE'];
				$download['phpaj_READY']=$download['READY'];
			}

			if(!empty($this->cache['USER'])){
				foreach(array_keys($this->cache['USER']) as $b){
					//pruefen, ob quelle noch existiert, wenn nicht -> loeschen
					if(empty($this->cache['IDS']['DOWNLOADID']
							[$this->cache['USER'][$b]['DOWNLOADID']]
							['USERID'][$b])){
						unset($this->cache['USER'][$b]);
						continue;
						}
					$quelle=&$this->cache['USER'][$b];
					$this->cache['DOWNLOAD']
						[$quelle['DOWNLOADID']]['phpaj_quellen_gesamt']++;
					//laufende uebertragungen
					if($quelle['STATUS']==="7"){
						//quellen zaehlen + geschwindigkeit berechnen
							$this->cache['DOWNLOAD']
								[$quelle['DOWNLOADID']]['phpaj_quellen_dl']++;
							$this->cache['DOWNLOAD']
								[$quelle['DOWNLOADID']]['phpaj_dl_speed']+=
									$quelle['SPEED'];
							$this->cache['DOWNLOAD']
								[$quelle['DOWNLOADID']]['phpaj_READY']+=
								$quelle['ACTUALDOWNLOADPOSITION']-$quelle['DOWNLOADFROM'];
						//ladende parts eines downloads fuer anzeige merken
							$this->cache['DOWNLOAD']
								[$quelle['DOWNLOADID']]['phpaj_loading_parts']
								[$b]['DOWNLOADFROM']=$quelle['DOWNLOADFROM'];
							$this->cache['DOWNLOAD']
								[$quelle['DOWNLOADID']]['phpaj_loading_parts']
								[$b]['DOWNLOADTO']=$quelle['DOWNLOADTO'];
							$this->cache['DOWNLOAD']
								[$quelle['DOWNLOADID']]['phpaj_loading_parts']
								[$b]['ACTUALDOWNLOADPOSITION']=
									$quelle['ACTUALDOWNLOADPOSITION'];
						//id merken
						array_push($this->cache['DOWNLOAD']
							[$quelle['DOWNLOADID']]['phpaj_ids_quellen_dl'],$b);
					} elseif($quelle['STATUS']==="5"
							|| $quelle['STATUS']==="14"){
					//quellen in warteschlange zaehlen
						$this->cache['DOWNLOAD']
							[$quelle['DOWNLOADID']]['phpaj_quellen_queue']++;
						array_push($this->cache['DOWNLOAD']
							[$quelle['DOWNLOADID']]
							['phpaj_ids_quellen_queue'],$b);
					}else{
						//rest
						array_push($this->cache['DOWNLOAD']
							[$quelle['DOWNLOADID']]
							['phpaj_ids_quellen_rest'],$b);
					}
				}
			}

			foreach(array_keys($this->cache['DOWNLOAD']) as $a){
				$download=&$this->cache['DOWNLOAD'][$a];
				$this->subdirs[$download['TARGETDIRECTORY']][$a]=&$download;
                $download['LINK'] = sprintf('ajfsp://file|%s|%s|%s/', $download['FILENAME'], $download['HASH'], $download['SIZE']);

                //werte zum sortieren
				$download['phpaj_REST']=
					$download['SIZE']-$download['phpaj_READY'];
				$download['phpaj_DONE']=
					($download['phpaj_READY']/$download['SIZE'])*100;
				//zwischen Suchen und Uebertrage unterscheiden
				if($download['STATUS']=="0"){
					if($download['phpaj_quellen_dl']>0)
						$download['phpaj_STATUS']='0_2';
						else
						$download['phpaj_STATUS']='0_1';
				}
			}

		}
	}


	//ids aller downloads sortiert zurueckgeben
	function ids($sort = "name", $subdir=""){
		$dlsort=array();
		if(!empty($this->subdirs[$subdir])){
			//sortieren
			switch($sort){
				case "sources":
					$dlsort=ajsort($this->subdirs[$subdir],
						'phpaj_quellen_gesamt',SORT_NUMERIC,1);
					break;
				case "status":
					$dlsort=ajsort($this->subdirs[$subdir],
						'phpaj_STATUS',SORT_STRING,0);
					break;
				case "speed":
					$dlsort=ajsort($this->subdirs[$subdir],
						'phpaj_dl_speed',SORT_NUMERIC,1);
					break;
				case "pdl":
					$dlsort=ajsort($this->subdirs[$subdir],
						'POWERDOWNLOAD',SORT_NUMERIC,1);
					break;
				case "size":
					$dlsort=ajsort($this->subdirs[$subdir],
						'SIZE',SORT_NUMERIC,1);
					break;
				case "rest":
					$dlsort=ajsort($this->subdirs[$subdir],
						'phpaj_REST',SORT_NUMERIC,0);
					break;
				case "done":
					$dlsort=ajsort($this->subdirs[$subdir],
						'phpaj_DONE',SORT_NUMERIC,1);
					break;
				default:
					$dlsort=ajsort($this->subdirs[$subdir],
						'FILENAME',SORT_STRING,0);
					break;
			}
		}
		return $dlsort;
	}

	//infos zu bestimmtem download zurueckgeben
	function download($id){
		$download =& $this->cache['DOWNLOAD'][$id];
		return $download;
	}

	//infos zu bestimmter quelle zurueckgeben
	function user($id){
		$quelle =& $this->cache['USER'][$id];
		return $quelle;
	}

	//action
	function action($action, $ids=array(), $value=""){
		$info="";

		if($action=="settargetdir"){
			//core kann im moment das zeilverzeichnis nur fuer einen download
				//gleichzeitig aendern...
			for($v=0, $vMax = count($ids); $v< $vMax; $v++){
				$info .= $action." &rArr; "
					.$this->core->command("function",$action
					."?id=".$ids[$v]
					."&dir=".rawurlencode($value))."<br/>";
			}
		}else{
			$changedl='';
			for($v=0, $vMax = count($ids); $v< $vMax; $v++)
				$changedl.="&id$v=".$ids[$v];
			$changedl=str_replace("&id0=","id=",$changedl);
			if($action=="setpowerdownload"){
				$value = str_replace(",",".",$value);
				if($value>1 && $value<2.2)
					$value=2.2;
				$value = ($value*10)-10;
				$changedl.="&Powerdownload=".$value;
			}
			if($action=="renamedownload")
				$changedl.="&name=".rawurlencode($value);

			$info = $action." &rArr; "
				.$this->core->command("function",$action."?".$changedl);
		}
		return $info;
	}
}
