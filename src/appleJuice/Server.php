<?php

namespace appleJuiceNETZ\appleJuice;

use appleJuiceNETZ\Kernel;

class Server
{
    var $server_xml;
    var $netstats;
    var $core;

    function __construct($noload = 0)
    {
        $lang = Kernel::getLanguage();

        $this->core = new Core();
        if ($noload) return;
        $this->server_xml = $this->core->command("xml","modified.xml?filter=server;informations");
        // -1 ist serverid wenn keine serververbindung besteht
        $this->server_xml['SERVER']['-1']['NAME'] = strtr($lang('Server.no_server'), array_flip(get_html_translation_table(HTML_ENTITIES)));
        $this->netstats = $this->netstats();
    }

    //stand der daten
    function time()
    {
        return date("j.n.y - H:i:s",
            ($this->server_xml['TIME']['VALUES']['CDATA']) / 1000);
    }

    function netstats()
    {
        $networkinfo = array_keys($this->server_xml['NETWORKINFO']);
        $netinfo =& $this->server_xml['NETWORKINFO'][$networkinfo[0]];
        if (!empty($this->server_xml['SERVER']
        [$netinfo['CONNECTEDWITHSERVERID']]['NAME'])) {
            $servername = htmlspecialchars($this->server_xml['SERVER']
            [$netinfo['CONNECTEDWITHSERVERID']]['NAME']);
        } else {
            //wenn kein servername bekannt ip und port zeigen
            $servername = $this->server_xml['SERVER']
            [$netinfo['CONNECTEDWITHSERVERID']]['HOST'];
        }

        $timeconnected = '?';
        if (isset($netinfo['CONNECTEDSINCE'])
            && $netinfo['CONNECTEDSINCE'] != 0) {
            $timeconnected = ($this->server_xml['TIME']['VALUES']['CDATA']
                    - $netinfo['CONNECTEDSINCE']) / 1000;
        }

        $connectedwith =& $netinfo['CONNECTEDWITHSERVERID'];
        $trytoconnectto =& $netinfo['TRYCONNECTTOSERVER'];
        $firewalled =& $netinfo['FIREWALLED'];
        $servercount = count($this->server_xml['SERVER']) - 1;
        $users =& $netinfo['USERS'];
        $filecount =& $netinfo['FILES'];

        $filesize = $netinfo['FILESIZE'] * 1024 * 1024;
        //print_r($filesize);
        if (!isset($this->server_xml['NETWORKINFO']
            ['WELCOMEMESSAGE']['VALUES']['CDATA']))
            $this->server_xml['NETWORKINFO']
            ['WELCOMEMESSAGE']['VALUES']['CDATA'] = '';
        $welcomemsg = trim($this->server_xml['NETWORKINFO']
        ['WELCOMEMESSAGE']['VALUES']['CDATA']);
        $welcomemsg = strip_tags($welcomemsg, $_ENV['ALLOWED_SERVERMSG_TAGS']);
        $welcomemsg = str_replace("<br>", "<br />", $welcomemsg);

        return array('servername' => $servername,
            'timeconnected' => $timeconnected,
            'firewalled' => $firewalled,
            'servercount' => $servercount,
            'users' => $users,
            'filecount' => $filecount,
            'filesize' => $filesize,
            'connectedwith' => $connectedwith,
            'trytoconnectto' => $trytoconnectto,
            'welcome' => $welcomemsg);
    }

    function ids()
    {
        $idliste = array_keys($this->server_xml['SERVER']);
        asort($idliste);
        array_shift($idliste);    // -1 entfernen
        return $idliste;
    }

    function serverinfo($id)
    {
        return $this->server_xml['SERVER'][$id];
    }

    function getmore()
    {
        $new_servers = file_get_contents($_ENV['SERVERLIST_URL']);

        if (empty($new_servers)) {
            return;
        }

        preg_match_all("/ajfsp:\/\/server\\|(.*)\//U", $new_servers, $new_servers_array);
        $new_rand_server = [];

        //10 zufaellige nummern nehmen, ist zwar moeglich, aber unwahrscheinlich, oefters die gleiche zu erwischen
        for ($h = 0; $h < 10; $h++) {
            $new_rand_server[] = random_int(0, count($new_servers_array[1]) - 1);
        }

        foreach ($new_rand_server as $rand_nummer) {
            if (!empty($new_servers_array[1][$rand_nummer])) {
                $this->core->command('function', 'processlink?link=ajfsp://server|' . $new_servers_array[1][$rand_nummer] . '/');
            }
        }
    }

    function info()
    {
        $info = array_keys($this->server_xml['INFORMATION']);
        return $this->server_xml['INFORMATION'][$info[0]];
    }

    function action($action, $id)
    {
        return $action . ' &rArr; ' . $this->core->command('function', $action . '?id=' . $id);
    }

}

