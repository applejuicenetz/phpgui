<?php
include_once "classes/class_core.php";
include_once "subs.php";

class Share{
	var $core;
	var $dirxml;
	var $cache;
	var $separator;
	var $spentprio;
	var $sharemode;
	
	function __construct(){
		$this->core = new Core();
		$this->cache =& $_SESSION['cache']['SHARE'];
		$this->separator =& $_SESSION['SEPARATOR'];
		//Um den checkbox-status beim share richtig zu zeigen
		$this->sharemode = array("subdirectory" => "checked=\"checked\"",
			"singledirectory" => "");
	}
	
	function add_share($name, $sharesubs=0){
		$oldshares=$this->get_shared_dirs();
		$countshares=count($oldshares)+1;
		$share_args="countshares=".$countshares."&";
		$i=0;
		foreach($oldshares as $a){
			$i++;
			$cur_dir=$this->get_shared_dir($a);
			$share_args.="sharedirectory".$i."=".urlencode($cur_dir['NAME'])
				."&sharesub".$i."="
				.(($cur_dir['SHAREMODE']=="subdirectory")? "True&":"False&");
		}
		$share_args.="sharedirectory".$countshares."=".urlencode($name)
			."&sharesub".$countshares."=";
		$share_args.=!empty($sharesubs) ? "True&" : "False&";
		$this->core->command("function","setsettings?".$share_args);
	}
	
	function del_share($name){
		$oldshares=$this->get_shared_dirs();
		$countshares=count($oldshares)-1;
		$share_args="countshares=".$countshares."&";
		$i=0;
		foreach($oldshares as $a){
			$i++;
			$cur_dir=$this->get_shared_dir($a);
			if($cur_dir['NAME']!=$name){
				$share_args.="sharedirectory$i=".urlencode($cur_dir['NAME'])
					."&sharesub$i="
					.(($cur_dir['SHAREMODE']=="subdirectory")? "True&":"False&");
			}else{
				$i--;
			}
		}
		$this->core->command("function","setsettings?".$share_args);
	}
	
	function changesub($name, $sharesubs=0){
		$shares=$this->get_shared_dirs();
		$countshares=count($shares);
		$share_args="countshares=".$countshares."&";
		$i=0;
		foreach($shares as $a){
			$i++;
			$cur_dir=$this->get_shared_dir($a);
			if($cur_dir['NAME']!=$name){
				$share_args.="sharedirectory$i=".urlencode($cur_dir['NAME'])
					."&sharesub$i="
					.(($cur_dir['SHAREMODE']=="subdirectory")? "True&":"False&");
			}else{
				$share_args.="sharedirectory$i=".urlencode($cur_dir['NAME'])
					."&sharesub$i=".(($sharesubs)? "True&":"False&");
			}
		}
		$this->core->command("function","setsettings?".$share_args);
	}
	
	function get_temp(){
		if(empty($this->dirxml)) $this->get_shared_dirs();
		$tempdirname=$this->dirxml['TEMPORARYDIRECTORY']['VALUES']['CDATA'];
		$tempdirname=substr($tempdirname,0,strlen($tempdirname)-1);
		return $tempdirname;
	}
	
	function get_shared_dirs($force=0){
		if(empty($this->dirxml) || $force) 
			$this->dirxml=$this->core->command("xml","settings.xml");
		ksort($this->dirxml['SHARE']['VALUES']['DIRECTORY']);	//sortieren
		return array_keys($this->dirxml['SHARE']['VALUES']['DIRECTORY']);
	}
	
	function get_shared_dir($id){
		return $this->dirxml['SHARE']['VALUES']['DIRECTORY'][$id];
	}
	
	function refresh_cache($zeit){
		//share-cache neu laden, falls er aelter als $zeit minuten ist
		if(empty($_SESSION['phpaj']['share_LASTTIMESTAMP'])
				|| ((time()- $_SESSION['phpaj']['share_LASTTIMESTAMP'])
				> ($zeit*60))){
			$_SESSION['phpaj']['share_LASTTIMESTAMP']=time();
			$this->cache=array();
			// liste der dateien im share holen
			$this->cache=$this->core->command("xml","share.xml");
		}
	}

	function get_fileids($verzeichnis=''){
		if(empty($this->cache['SHARES']['VALUES']['SHARE'])) return;
		if(empty($this->separator)) $this->directory("",1);
		$ids=array();
		$sfsort=array();
		$verzeichnis=$verzeichnis.$this->separator;
		$temp=strlen($verzeichnis);
		$this->spentprio=0;
		foreach(array_keys($this->cache['SHARES']['VALUES']['SHARE']) as $a){
			$file=&$this->get_file($a);
			if($file['PRIORITY']>1)
				$this->spentprio+=$file['PRIORITY'];
			if(substr($file['FILENAME'],0,$temp)==$verzeichnis
					&& strpos($file['FILENAME'],$this->separator,$temp)===false)
				$ids[$a]=&$file;
		}
		if(!empty($ids))
			$sfsort=ajsort($ids,'SHORTFILENAME',SORT_STRING,0);
		return array_keys($sfsort);
	}
		
	function get_file($id){
		if(empty($this->cache['SHARES']['VALUES']['SHARE'][$id])){
			$fileobject=$this->core->command("xml","getobject.xml?id=$id");
			$this->cache['SHARES']['VALUES']['SHARE'][$id]=$fileobject['SHARE'][$id];
		}
		$file=&$this->cache['SHARES']['VALUES']['SHARE'][$id];
		return $file;
	}
	
	function setpriority($ids, $prio){
		$changeprio_ids='';
		for($i=0;$i<count($ids);$i++){
			$changeprio_ids.="&id$i=".$ids[$i];
		}
		$changeprio_ids=str_replace("&id0=","id=",$changeprio_ids);
		$this->core->command("function","setpriority?".$changeprio_ids
			."&priority=".$prio);
		//geaenderte dateien neu laden
		foreach($ids as $i){
			$fileobject=$this->core->command("xml","getobject.xml?id=$i");
			$this->cache['SHARES']['VALUES']['SHARE'][$i]=$fileobject['SHARE'][$i];
		}
	}
	
	function directory($dir="",$getseponly=0){
		$dirlist=array();
		if(!empty($dir))
			$dirarg="&directory=".rawurlencode($dir);
		else
			$dirarg='';
		$dirxml=$this->core->command("xml","directory.xml?$dirarg");
		//pfad seperator holen
		$sep=array_keys($dirxml['FILESYSTEM']);
		$this->separator=$sep[0];
		$sep=&$this->separator;
		if($getseponly==1) return;
		if(!empty($dirxml['DIR'])){
			//workarround fuer den windows desktop->arbeitsplatz mist
			$deskname=array_keys($dirxml['DIR']);
			if(!empty($deskname) && !empty($dirxml['DIR'][$deskname[0]]['DIR'])
					&& $dirxml['DIR'][$deskname[0]]['TYPE']==="5")
				$dirxml['DIR']=$dirxml['DIR'][$deskname[0]]['DIR'];
			//schoen sortieren ;)
			ksort($dirxml['DIR']);
		}
			
		//eintrag ".."
		$dirup='';
		if(!empty($dir)){
			$dirup = explode($sep,$dir);
			array_pop($dirup);
			$dirup = join($sep,$dirup);
			array_push($dirlist,array($dirup,".."));
		}
			
		//restliche eintraege
		if(!empty($dirxml['DIR'])){
			foreach(array_keys($dirxml['DIR']) as $a){
				if(empty($dirxml['DIR'][$a]['PATH'])
						&& $dirxml['DIR'][$a]['TYPE']=='4'){
					//pfad falls noetig bestimmen
					$dirxml['DIR'][$a]['PATH']=
						preg_replace("/\\".$sep."+/",
							$sep,$dir.$sep.$dirxml['DIR'][$a]['NAME']);
				}
				//pfad + name in array packen
				array_push($dirlist,array($dirxml['DIR'][$a]['PATH'],
					$dirxml['DIR'][$a]['NAME']));
			}
		}
		
		return $dirlist;
	}
}

