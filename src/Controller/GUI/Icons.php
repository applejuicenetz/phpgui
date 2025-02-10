<?php

namespace appleJuiceNETZ\GUI;

class Icons
{
    var $os;
    var $directstate;
    var $os_system;

    function __construct()
    {
        //Passendes symbol (oder name) fuer betriebssystem der id zuordnen
        $this->os = array(
            "0" => '<img width="16" height="16" src="public/assets/images/icons/os/os_unknow.svg"/>',
            "1" => '<img width="16" height="16" src="public/assets/images/icons/os/os_windows.svg" alt="windows-10"/>',
            "2" => '<img width="16" height="16" src="public/assets/images/icons/os/os_linux.svg" alt="linux--v1"/>',
            "3" => '<img width="16" height="16" src="public/assets/images/icons/os/os_mac.svg" alt="mac-logo"/>',
            "4" => "solaris",
            "5" => "os2",
            "6" => 'bsd',
            "7" => "netware");

        //OS Sstem Icons
        $this->os_system = array(
        	"N-A" => '<img width="16" height="16" src="public/assets/images/icons/os/os_unknow.svg"/>',
            "Windows" => '<img width="16" height="16" src="public/assets/images/icons/os/os_windows.svg" alt="windows-10"/>',
            "Linux" => '<img width="16" height="16" src="public/assets/images/icons/os/os_linux.svg" alt="linuxv1"/>',
            "Mac" => '<img width="16" height="16" src="public/assets/images/icons/os/os_mac.svg" alt="mac-logo"/>',
            "4" => "solaris",
            "5" => "os2",
            "6" => 'bsd',
            "7" => "netware");

        //direkte/indirekte verbindung
        $this->directstate = array(
            "0" => "<img src='public/assets/images/icons/unknow.svg' width='16' alt='?' />",
            "1" => "<img src='public/assets/images/icons/direct.svg' width='16' alt='direct' />",
            "2" => "<img src='public/assets/images/icons/indirect.svg' width='16' alt='indirect' />",
            "3" => "<img src='public/assets/images/icons/indirect.svg' width='16' alt='indirect' />",
            "WAIT" => "<img src='public/assets/images/icons/wait.png' width='16' alt='wait' />",);
    }
}