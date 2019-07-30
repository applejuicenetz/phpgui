<?php
include_once "classes/class_core.php";

class Uploads{
	var $cache;
	var $core;
	
	function __construct(){
		$this->cache =& $_SESSION['cache']['UPLOADS'];
		$this->core = new Core();
	}
	
	//stand der daten
	function time(){
		return date("j.n.y - H:i:s",
			($this->cache['TIME']['VALUES']['CDATA'])/1000);
	}
	
	//neue infos vom core holen
	function refresh_cache(){
		//liste mit alten ids l�schen
		if(!empty($this->cache['IDS']))
			unset($this->cache['IDS']);
		if(empty($this->cache['LASTTIMESTAMP']))
			$this->cache['LASTTIMESTAMP']=0;
		$this->cache=
			$this->core->command("xml","modified.xml?timestamp="
			.$this->cache['LASTTIMESTAMP']."&filter=uploads;ids",$this->cache);
		//timestamp f�r n�chste abfrage
		$this->cache['LASTTIMESTAMP']=$this->cache['TIME']['VALUES']['CDATA'];
		$this->process_uploads();
	}

	function process_uploads(){
		//listen zuruecksetzen
			$this->cache['phpaj_ul']=0;
			$this->cache['phpaj_ids_ul']=array();
			$this->cache['phpaj_queue']=0;
			$this->cache['phpaj_ids_queue']=array();
		if(!empty($this->cache['UPLOAD'])){
			foreach(array_keys($this->ids()) as $a){
				//ueberprfen, ob ids noch existieren, wenn nicht -> loeschen
				if(empty($this->cache['IDS']['VALUES']['UPLOADID'][$a])){
					unset($this->cache['UPLOAD'][$a]);
					continue;
				}
				$current_upload=&$this->get_upload($a);
				if($current_upload['STATUS']==="1"){
					//laufende uploads
					$this->cache['phpaj_ul']++;
					array_push($this->cache['phpaj_ids_ul'],
						$current_upload['ID']);
				}else{
					//warteschlange
					$this->cache['phpaj_queue']++;
					array_push($this->cache['phpaj_ids_queue'],
						$current_upload['ID']);
				}
			}
		}
	}

	function ids(){
		$liste=array();
		if(!empty($this->cache['UPLOAD']))
			$liste=ajsort($this->cache['UPLOAD'],'PRIORITY',SORT_NUMERIC,1);
		return $liste;
	}

	function get_upload($id){
		return $this->cache['UPLOAD'][$id];
	}

}
	
