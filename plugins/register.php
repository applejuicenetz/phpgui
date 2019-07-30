<?php
class Plugin{
	var $liste;
	
	function Plugin(){
		$this->liste=array();
	}
	
	function Find_Plugins(){
		$dirlist=opendir("../plugins");
		while (false !== ($file = readdir($dirlist))) { 
			if(is_dir("../plugins/$file")
					&& file_exists("../plugins/$file/info.php")){
				include("../plugins/$file/info.php");
			}
		}
	}
	
	function register($name,$icon="",$filename){
		array_push($this->liste,array($name,$icon,$filename));
	}
}	
