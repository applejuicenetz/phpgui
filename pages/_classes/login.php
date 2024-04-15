<?php
class login{
	function check_login(){
		if(isset($_GET['login'])) {
			$core = new core();
    		$core_host = $_POST['chost'];
    		$core_pass = $_POST['cpass'];
    		
    		//Überprüfung des Passworts
    		if ($core_pass !== false) {
        		$_SESSION['core_pass'] = md5($core_pass);
        		$_SESSION["core_host"] = $core_host;
        		
        		$core_info = $core->command("xml","settings.xml");				
        	} else {
    		
    		}
    
		}
		if(empty($_SESSION['core_host'])){
			$login = new login();
			$login->login_form();
		}elseif(!empty($_SESSION["core_host"])){
			if(!empty($_GET['login'])) $_GET["site"] = "start";
			include("_includes/index_page.php");
		}
		
	}
	function login_form(){
		include_once("_includes/login_header.php");
	}
}
?>