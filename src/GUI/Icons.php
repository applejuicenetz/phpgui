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
            "0" => '<img width="16" height="16" src="https://img.icons8.com/color/48/operating-system--v1.png" alt="operating-system--v1"/>',
            "1" => '<img width="16" height="16" src="themes/icons/windows_os.svg" alt="windows-10"/>',
            "2" => '<img width="16" height="16" src="themes/icons/linux_os.svg" alt="linux--v1"/>',
            "3" => '<img width="16" height="16" src="themes/icons/mac_os.svg" alt="mac-logo"/>',
            "4" => "<img src='../style/"
                . $_SESSION['os_solaris_icon'] . "' alt='solaris' />",
            "5" => "<img src='../style/"
                . $_SESSION['os_os2_icon'] . "' alt='os/2' />",
            "6" => '<img width="24" height="24" src="https://img.icons8.com/color/24/free-bsd.png" alt="free-bsd"/>',
            "7" => "<img src='../style/"
                . $_SESSION['os_netware_icon'] . "' alt='netware' />");

        //OS Sstem Icons
        $this->os_system = array(
            "N/A" => '<img width="24" height="24" src="https://img.icons8.com/color/48/operating-system--v1.png" alt="operating-system--v1"/>',
            "Windows" => '<img width="24" height="24" src="https://img.icons8.com/color/24/windows-10.png" alt="windows-10"/>',
            "Linux" => '<img width="24" height="24" src="https://img.icons8.com/color/24/linux--v1.png" alt="linux--v1"/>',
            "Mac" => '<img width="24" height="24" src="https://img.icons8.com/color/24/mac-logo.png" alt="mac-logo"/>',
            "4" => "<img src='../style/"
                . $_SESSION['os_solaris_icon'] . "' alt='solaris' />",
            "5" => "<img src='../style/"
                . $_SESSION['os_os2_icon'] . "' alt='os/2' />",
            "6" => '<img width="24" height="24" src="https://img.icons8.com/color/24/free-bsd.png" alt="free-bsd"/>',
            "7" => "<img src='../style/"
                . $_SESSION['os_netware_icon'] . "' alt='netware' />");

        //direkte/indirekte verbindung
        $this->directstate = array(
            "0" => "<img src='themes/icons/unknow.svg' width='16' alt='?' />",
            "1" => "<img src='themes/icons/direct.svg' width='16' alt='direct' />",
            "2" => "<img src='themes/icons/indirect.svg' width='16' alt='indirect' />",
            "3" => "<img src='themes/icons/indirect.svg' width='16' alt='indirect' />");
    }
}