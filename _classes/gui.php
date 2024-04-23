<?php
$_SESSION['phpaj']['PHP_GUI_VERSION'] = "0.29.0 Beta";

class gui {
	public $data;

	function __construct() {
    	$data = file_get_contents("_includes/.gui/settings/settings.json");
    	$this->data = json_decode($data);
	}
	function output(){
		return $this->data;
	}
	function check_version($var){
		$language = new language($_ENV['GUI_LANGUAGE']);
		$lang = $language->translate();
		$template = new template();


		$lik = "https://raw.githubusercontent.com/Kddk22/phpgui/main/CHANGELOG.md";
		$akt_ver = file($lik);
		
		foreach ($akt_ver as $line_num => $line) {
    		if($line_num == 4){
    			$version = str_replace("#", "", $line);
    			
    			$version = trim($version);
    			
    			$_SESSION['phpaj']['akt_version'] = $version;
			}else{
			}
			
		}
		
		if($_SESSION['phpaj']['akt_version'] != $_SESSION['phpaj']['PHP_GUI_VERSION']){
			$template->alert("warning", $lang->System->version_1, $lang->System->version_akt.$_SESSION['phpaj']['akt_version']);
			
		}else{
		} 
    }
	function versions_update($var){
		$language = new language($_ENV['GUI_LANGUAGE']);
		$lang = $language->translate();
		$template = new template();


		$lik = "https://raw.githubusercontent.com/Kddk22/phpgui/main/CHANGELOG.md";
		$akt_ver = file($lik);
		
		foreach ($akt_ver as $line_num => $line) {
    		if($line_num == 4){
    			$version = str_replace("#", "", $line);
    			
    			$version = trim($version);
    			
    			$_SESSION['phpaj']['akt_version'] = $version;
			}else{
			}
			
		}
		
		if($_SESSION['phpaj']['akt_version'] != $_SESSION['phpaj']['PHP_GUI_VERSION']){
			return '<span class="col-danger font-bold">'.$_SESSION['phpaj']['PHP_GUI_VERSION'].'</span>';
		}else{
			return $_SESSION['phpaj']['PHP_GUI_VERSION'];
		} 
    }
		
		
	
}
?>