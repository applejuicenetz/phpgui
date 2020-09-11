<?php
require_once "classes/class_core.php";

class Search{
	var $cache;
	var $core;

	function __construct(){
		$this->cache =& $_SESSION['cache']['SEARCH'];
		$this->core = new Core();
	}

	//stand der daten
	function time(){
		return date("j.n.y - H:i:s",
			($this->cache['TIME']['VALUES']['CDATA'])/1000);
	}

	function refresh_cache(){
		if(empty($this->cache['LASTTIMESTAMP']))
			$this->cache['LASTTIMESTAMP']=0;
		$this->cache=
			$this->core->command("xml","modified.xml?filter=search&timestamp="
			.$this->cache['LASTTIMESTAMP'], $this->cache);
		$this->cache['LASTTIMESTAMP']=$this->cache['TIME']['VALUES']['CDATA'];
	}

	function process_results(){
		$alle_suchen_anzahl=0;
		if(!empty($this->cache['SEARCH'])){
			foreach(array_keys($this->cache['SEARCH']) as $b){
				// counter fuer anzahl der ergebnisse der
				// einzelnen suchen auf 0 setzen
				$this->cache['SEARCH'][$b]['phpaj_FOUNDFILES']=0;
			}
		}

		if(!empty($this->cache['SEARCHENTRY'])){
			foreach(array_keys($this->cache['SEARCHENTRY'])
					as $a){
				//counter fuer alle ergebnisse hochzaehlen
				$alle_suchen_anzahl++;
				//anzahl der quellen fuer das suchergebnis auf 0 setzen
				$gesamt_anzahl=0;
				$sort_names=array();
				$suchergebnis=&$this->cache['SEARCHENTRY'][$a];
				foreach(array_keys($suchergebnis['FILENAME']) as $b){
					$sort_names["$b"]=$suchergebnis['FILENAME'][$b]['USER'];
					//quellen fuer diesen dateinamen dazuz�hlen
					$gesamt_anzahl+=$suchergebnis['FILENAME'][$b]['USER'];
				}
				//dateiname mit den meisten quellen finden
				arsort($sort_names,SORT_NUMERIC);
				$names=array_keys($sort_names);
				//dateiname der angezeigt wird
				$suchergebnis['phpaj_FILENAME']=$names[0];
				//anzahl aller dateinamen zusammen
				$suchergebnis['phpaj_COUNT']=$gesamt_anzahl;
				//counter fuer ergebnisse der jeweiligen suche hochz�hlen
				$this->cache['SEARCH']
					[$suchergebnis['SEARCHID']]['phpaj_FOUNDFILES']++;
			}
		}
		$this->cache['SEARCHENTRY_count']=$alle_suchen_anzahl;
	}

	function start($string){
		//suche starten
		$this->core->command("function","search?search=".rawurlencode($string));
	}

	function delete($id){
		//suche aus cache loeschen
		$ausgabe="";
		if($this->cache['SEARCH'][$id]['RUNNING']==="false")
			$ausgabe = "cancelsearch &rArr; "
				.$this->core->command("function","cancelsearch?id=".$id);
		unset($this->cache['SEARCH'][$_GET['deleteid']]);
		if(!empty($this->cache['SEARCHENTRY'])){
			foreach(array_keys($this->cache['SEARCHENTRY'])
					as $a ){
				if($id===$this->cache['SEARCHENTRY']
						[$a]['SEARCHID'])
					unset($this->cache['SEARCHENTRY'][$a]);
			}
		}
		return $ausgabe;
	}

	function delete_all(){
		$ausgabe="";
		if(!empty($this->cache['SEARCH'])){
			foreach(array_keys($this->cache['SEARCH']) as $b){
				if($this->cache['SEARCH'][$b]['RUNNING']
						!=="canceled")
					$ausgabe .= "cancelsearch &rArr; "
						.$this->core->command("function","cancelsearch?id=".$b,
						"0")."<br>";
				unset($this->cache['SEARCH'][$b]);
			}
			unset($this->cache['SEARCH']);
			unset($this->cache['SEARCHENTRY']);
		}
		return $ausgabe;
	}

	function cancel($id){
		$this->cache['SEARCH'][$id]['RUNNING']="canceled";
		return "cancelsearch &rArr; ".$this->core->command("function",
			"cancelsearch?id=".$id);
	}

	function sortieren($type="count"){
		$searchsort=array();
		switch($type){
			case "name":
				$searchsort=ajsort($this->cache['SEARCHENTRY'],
					'phpaj_FILENAME',SORT_STRING,0);
				break;
			case "size":
				$searchsort=ajsort($this->cache['SEARCHENTRY'],
					'SIZE',SORT_NUMERIC,1);
				break;
			default:
				$searchsort=ajsort($this->cache['SEARCHENTRY'],
					'phpaj_COUNT',SORT_NUMERIC,1);
				break;
		}
		return $searchsort;
	}

}

