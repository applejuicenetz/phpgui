<?php
class Icons{
	var $os;
	var $serverstatus;
	var $directstate;
	
	function __construct(){
		//Passendes symbol (oder name) fuer betriebssystem der id zuordnen
		$this->os = array(
			"0" => "<img src='../style/"
				.$_SESSION['os_unknown_icon']."' alt='?' />",
			"1" => "<img src='../style/"
				.$_SESSION['os_win_icon']."' alt='win' />",
			"2" => "<img src='../style/"
				.$_SESSION['os_linux_icon']."' alt='linux' />",
			"3" => "<img src='../style/"
				.$_SESSION['os_mac_icon']."' alt='mac' />",
			"4" => "<img src='../style/"
				.$_SESSION['os_solaris_icon']."' alt='solaris' />",
			"5" => "<img src='../style/"
				.$_SESSION['os_os2_icon']."' alt='os/2' />",
			"6" => "<img src='../style/"
				.$_SESSION['os_bsd_icon']."' alt='bsd' />",
			"7" => "<img src='../style/"
				.$_SESSION['os_netware_icon']."' alt='netware' />");

		//Zum Serverstatus passendes symbol anzeigen
		$this->serverstatus = array(
			"alt" => "<img src='../style/"
				.$_SESSION['server_old_icon']."' alt='old' />",
			"neu" => "<img src='../style/"
				.$_SESSION['server_new_icon']."' alt='new' />",
			"verbinde" => "<img src='../style/"
				.$_SESSION['server_connecting_icon']."' alt='connecting' />",
			"verbunden" => "<img src='../style/"
				.$_SESSION['server_connected_icon']."' alt='connected' />");
		
		//direkte/indirekte verbindung
		$this->directstate = array(
			"0" => "<img src='../style/"
				.$_SESSION['directstate_unknown']."' alt='?' />",
			"1" => "<img src='../style/"
				.$_SESSION['directstate_direct']."' alt='direct' />",
			"2" => "<img src='../style/"
				.$_SESSION['directstate_indirect']."' alt='indirect' />",
			"3" => "<img src='../style/"
				.$_SESSION['directstate_indirect']."' alt='indirect' />");
	}
}
