<?php

namespace appleJuiceNETZ\GUI;

class Icons
{
    var $os;
    var $serverstatus;
    var $directstate;
    var $os_system;

    function __construct()
    {
        //Passendes symbol (oder name) fuer betriebssystem der id zuordnen
        $this->os = array(
            "0" => '<img width="24" height="24" src="https://img.icons8.com/color/48/operating-system--v1.png" alt="operating-system--v1"/>',
            "1" => '<img width="24" height="24" src="https://img.icons8.com/color/24/windows-10.png" alt="windows-10"/>',
            "2" => '<img width="24" height="24" src="https://img.icons8.com/color/24/linux--v1.png" alt="linux--v1"/>',
            "3" => '<img width="24" height="24" src="https://img.icons8.com/color/24/mac-logo.png" alt="mac-logo"/>',
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

        //ALT Zum Serverstatus passendes symbol anzeigen
        $this->serverstatus = array(
            "alt" => "<img src='../style/"
                . $_SESSION['server_old_icon'] . "' alt='old' />",
            "neu" => "<img src='../style/"
                . $_SESSION['server_new_icon'] . "' alt='new' />",
            "verbinde" => "<img src='../style/"
                . $_SESSION['server_connecting_icon'] . "' alt='connecting' />",
            "verbunden" => "<img src='../style/"
                . $_SESSION['server_connected_icon'] . "' alt='connected' />");

        //direkte/indirekte verbindung
        $this->directstate = array(
            "0" => "<img src='/themes/icons/unknow.png' alt='?' />",
            "1" => "<img src='/themes/icons/direct.png' alt='direct' />",
            "2" => "<img src='/themes/icons/indirect.png' alt='indirect' />",
            "3" => "<img src='/themes/icons/indirect.png' alt='indirect' />");
    }
}