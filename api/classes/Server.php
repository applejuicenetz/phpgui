<?php

namespace appleJuiceNETZ\appleJuice;

/**
 * AppleJuice Server Management Class
 * 
 * Based on the original phpgui Server class
 * Adapted for the modern API with real XML data integration
 */
class Server
{
    var $server_xml;
    var $netstats;
    var $core;
    private $useRealData = false;

    function __construct($noload = 0)
    {
        $this->core = new Core();
        if ($noload) return;
        
        // Try to load real data from Core, fallback to demo data
        if ($this->loadRealData()) {
            $this->useRealData = true;
            error_log("✅ Real server data loaded from AppleJuice Core");
        } else {
            $this->useRealData = false;
            error_log("⚠️ Using demo data - AppleJuice Core not available");
            $this->loadDemoData();
        }
    }

    /**
     * Try to load real data from AppleJuice Core
     */
    private function loadRealData()
    {
        try {
            // Test if Core is available
            $test_result = $this->core->command("xml", "modified.xml?filter=server");
            
            if (!$test_result || empty($test_result)) {
                return false;
            }
            
            // Load server data from AppleJuice Core
            $this->server_xml = $this->core->command("xml", "modified.xml?filter=server;informations");
            
            if (!$this->server_xml || empty($this->server_xml)) {
                return false;
            }
            
            // -1 ist serverid wenn keine serververbindung besteht
            if (!isset($this->server_xml['SERVER']['-1']['NAME'])) {
                $this->server_xml['SERVER']['-1']['NAME'] = 'Nicht verbunden';
            }
            
            $this->netstats = $this->netstats();
            return true;
            
        } catch (\Exception $e) {
            error_log("AppleJuice Core connection failed: " . $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            error_log("AppleJuice Core connection failed (Throwable): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Load demo data when Core is not available
     */
    private function loadDemoData()
    {
        $this->server_xml = [
            'TIME' => [
                'VALUES' => [
                    'CDATA' => time() * 1000
                ]
            ],
            'SERVER' => [
                '-1' => ['NAME' => 'Nicht verbunden'],
                '3' => [
                    'ID' => '3',
                    'NAME' => 'AJ Server DDNSS',
                    'HOST' => 'aj-server.ddnss.de',
                    'PORT' => '7001',
                    'USERS' => '847',
                    'FILES' => '1847392',
                    'PING' => '45',
                    'VERSION' => '0.30.108',
                    'LOCATION' => 'Deutschland'
                ],
                '4' => [
                    'ID' => '4',
                    'NAME' => 'Apple Deluxe Server',
                    'HOST' => 'apple-deluxe.ddns.net',
                    'PORT' => '9855',
                    'USERS' => '1247',
                    'FILES' => '2847392',
                    'PING' => '23',
                    'VERSION' => '0.30.108',
                    'LOCATION' => 'Deutschland'
                ],
                '5' => [
                    'ID' => '5',
                    'NAME' => 'Chimera Server',
                    'HOST' => 'chimera.goip.de',
                    'PORT' => '9856',
                    'USERS' => '634',
                    'FILES' => '1456789',
                    'PING' => '67',
                    'VERSION' => '0.30.108',
                    'LOCATION' => 'Deutschland'
                ],
                '6' => [
                    'ID' => '6',
                    'NAME' => 'appleJuice mainserver',
                    'HOST' => 'knastbruder.applejuicenet.cc',
                    'PORT' => '9855',
                    'USERS' => '1500',
                    'FILES' => '3456789',
                    'PING' => '89',
                    'VERSION' => '0.30.108',
                    'LOCATION' => 'Deutschland'
                ],
                '671' => [
                    'ID' => '671',
                    'NAME' => 'Rainers AJ Server 1',
                    'HOST' => 'rainer11.zapto.org',
                    'PORT' => '9855',
                    'USERS' => '423',
                    'FILES' => '987654',
                    'PING' => '156',
                    'VERSION' => '0.30.107',
                    'LOCATION' => 'Deutschland'
                ]
            ],
            'NETWORKINFO' => [
                'INFO1' => [
                    'CONNECTEDWITHSERVERID' => '4',
                    'CONNECTEDSINCE' => (time() - 3600) * 1000,
                    'TRYCONNECTTOSERVER' => '0',
                    'FIREWALLED' => '0',
                    'USERS' => '1247',
                    'FILES' => '2847392',
                    'FILESIZE' => '2847'
                ],
                'WELCOMEMESSAGE' => [
                    'VALUES' => [
                        'CDATA' => 'Willkommen auf dem Apple Deluxe Server!'
                    ]
                ]
            ],
            'INFORMATION' => [
                'INFO1' => [
                    'VERSION' => '0.30.108',
                    'SYSTEM' => 'Windows 10 x64',
                    'UPTIME' => '3600'
                ]
            ]
        ];
        
        $this->netstats = $this->netstats();
    }

    /**
     * Get current timestamp
     */
    function time()
    {
        return date("j.n.y - H:i:s",
            ($this->server_xml['TIME']['VALUES']['CDATA']) / 1000);
    }

    /**
     * Get network statistics
     */
    function netstats()
    {
        if (!isset($this->server_xml['NETWORKINFO']) || empty($this->server_xml['NETWORKINFO'])) {
            return [
                'servername' => 'Nicht verbunden',
                'timeconnected' => 0,
                'firewalled' => 0,
                'servercount' => count($this->server_xml['SERVER']) - 1,
                'users' => 0,
                'filecount' => 0,
                'filesize' => 0,
                'connectedwith' => -1,
                'trytoconnectto' => 0,
                'welcome' => ''
            ];
        }

        $networkinfo = array_keys($this->server_xml['NETWORKINFO']);
        $netinfo =& $this->server_xml['NETWORKINFO'][$networkinfo[0]];
        
        if (!empty($this->server_xml['SERVER']
        [$netinfo['CONNECTEDWITHSERVERID']]['NAME'])) {
            $servername = htmlspecialchars($this->server_xml['SERVER']
            [$netinfo['CONNECTEDWITHSERVERID']]['NAME']);
        } else {
            //wenn kein servername bekannt ip und port zeigen
            $servername = $this->server_xml['SERVER']
            [$netinfo['CONNECTEDWITHSERVERID']]['HOST'] ?? 'Unbekannt';
        }

        $timeconnected = 0;
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

        $filesize = ($netinfo['FILESIZE'] ?? 0) * 1024 * 1024;
        
        if (!isset($this->server_xml['NETWORKINFO']
            ['WELCOMEMESSAGE']['VALUES']['CDATA']))
            $this->server_xml['NETWORKINFO']
            ['WELCOMEMESSAGE']['VALUES']['CDATA'] = '';
        $welcomemsg = trim($this->server_xml['NETWORKINFO']
        ['WELCOMEMESSAGE']['VALUES']['CDATA']);
        $welcomemsg = strip_tags($welcomemsg, '<br><b><i><u>');
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

    /**
     * Get server IDs
     */
    function ids()
    {
        $idliste = array_keys($this->server_xml['SERVER']);
        asort($idliste);
        array_shift($idliste);    // -1 entfernen
        return $idliste;
    }

    /**
     * Get server information by ID
     */
    function serverinfo($id)
    {
        return $this->server_xml['SERVER'][$id] ?? null;
    }

    /**
     * Get more servers from server list
     */
    function getmore()
    {
        // For API, we'll implement this differently
        // This would normally fetch from a server list URL
        return true;
    }

    /**
     * Get server information
     */
    function info()
    {
        if (!isset($this->server_xml['INFORMATION']) || empty($this->server_xml['INFORMATION'])) {
            return [
                'VERSION' => '0.30.108',
                'SYSTEM' => 'Windows 10 x64',
                'UPTIME' => '3600'
            ];
        }
        
        $info = array_keys($this->server_xml['INFORMATION']);
        return $this->server_xml['INFORMATION'][$info[0]];
    }

    /**
     * Execute server action
     */
    function action($action, $id)
    {
        return $action . ' &rArr; ' . $this->core->command('function', $action . '?id=' . $id);
    }

    /**
     * Get formatted server list for API
     */
    public function getServerList()
    {
        $servers = [];
        $ids = $this->ids();
        $netstats = $this->netstats();
        
        foreach ($ids as $id) {
            $serverInfo = $this->serverinfo($id);
            if (!$serverInfo) continue;
            
            $isConnected = ($netstats['connectedwith'] == $id);
            
            $servers[] = [
                'id' => (int)$id,
                'name' => $serverInfo['NAME'] ?? 'Unbekannt',
                'address' => $serverInfo['HOST'] ?? '',
                'port' => (int)($serverInfo['PORT'] ?? 9851),
                'users' => (int)($serverInfo['USERS'] ?? 0),
                'files' => (int)($serverInfo['FILES'] ?? 0),
                'ping' => (int)($serverInfo['PING'] ?? 999),
                'version' => $serverInfo['VERSION'] ?? 'Unknown',
                'location' => $serverInfo['LOCATION'] ?? 'Unknown',
                'status' => $this->getServerStatus($serverInfo, $isConnected),
                'connected' => $isConnected,
                'uptime' => $isConnected ? $netstats['timeconnected'] : 0,
                'lastSeen' => date('Y-m-d H:i:s'),
                'source' => $this->useRealData ? 'core' : 'demo'
            ];
        }
        
        return $servers;
    }

    /**
     * Get server status based on server info
     */
    public function getServerStatus($serverInfo, $isConnected)
    {
        if ($isConnected) {
            return 'connected';
        }
        
        $users = (int)($serverInfo['USERS'] ?? 0);
        $ping = (int)($serverInfo['PING'] ?? 999);
        
        if ($ping > 500 || $users == 0) {
            return 'offline';
        } elseif ($users > 1000) {
            return 'busy';
        } else {
            return 'available';
        }
    }

    /**
     * Connect to a specific server
     */
    public function connectToServer($serverId)
    {
        try {
            if ($this->useRealData) {
                $result = $this->core->command('function', 'connecttoserver?id=' . $serverId);
                
                // Reload server data after connection attempt
                $this->server_xml = $this->core->command("xml", "modified.xml?filter=server;informations");
                $this->netstats = $this->netstats();
                
                return [
                    'success' => true,
                    'message' => 'Connection initiated',
                    'result' => $result
                ];
            } else {
                // Simulate connection for demo mode
                $serverInfo = $this->serverinfo($serverId);
                if ($serverInfo) {
                    // Update demo data to show connection
                    $this->server_xml['NETWORKINFO']['INFO1']['CONNECTEDWITHSERVERID'] = $serverId;
                    $this->server_xml['NETWORKINFO']['INFO1']['CONNECTEDSINCE'] = time() * 1000;
                    $this->netstats = $this->netstats();
                    
                    return [
                        'success' => true,
                        'message' => 'Demo connection to ' . $serverInfo['NAME'],
                        'result' => 'demo_mode'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Server not found'
                    ];
                }
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Disconnect from current server
     */
    public function disconnectFromServer()
    {
        try {
            if ($this->useRealData) {
                $result = $this->core->command('function', 'disconnect');
                
                // Reload server data after disconnection
                $this->server_xml = $this->core->command("xml", "modified.xml?filter=server;informations");
                $this->netstats = $this->netstats();
                
                return [
                    'success' => true,
                    'message' => 'Disconnected successfully',
                    'result' => $result
                ];
            } else {
                // Simulate disconnection for demo mode
                $this->server_xml['NETWORKINFO']['INFO1']['CONNECTEDWITHSERVERID'] = '-1';
                $this->server_xml['NETWORKINFO']['INFO1']['CONNECTEDSINCE'] = '0';
                $this->netstats = $this->netstats();
                
                return [
                    'success' => true,
                    'message' => 'Demo disconnection successful',
                    'result' => 'demo_mode'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Disconnection failed: ' . $e->getMessage()
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Disconnection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get current connection status
     */
    public function getConnectionStatus()
    {
        $netstats = $this->netstats();
        
        return [
            'connected' => $netstats['connectedwith'] != -1,
            'server_id' => $netstats['connectedwith'],
            'server_name' => $netstats['servername'],
            'users' => $netstats['users'],
            'files' => $netstats['filecount'],
            'filesize' => $netstats['filesize'],
            'uptime' => $netstats['timeconnected'],
            'firewalled' => $netstats['firewalled'],
            'welcome_message' => $netstats['welcome'],
            'data_source' => $this->useRealData ? 'core' : 'demo'
        ];
    }

    /**
     * Get server statistics
     */
    public function getStatistics()
    {
        $netstats = $this->netstats();
        $servers = $this->getServerList();
        
        $connectedServers = array_filter($servers, function($server) {
            return $server['connected'];
        });
        
        $totalUsers = array_sum(array_column($servers, 'users'));
        $totalFiles = array_sum(array_column($servers, 'files'));
        $averagePing = count($servers) > 0 ? 
            array_sum(array_column($servers, 'ping')) / count($servers) : 0;
        
        return [
            'total_servers' => count($servers),
            'connected_servers' => count($connectedServers),
            'total_users' => $totalUsers,
            'total_files' => $totalFiles,
            'average_ping' => round($averagePing),
            'server_count' => $netstats['servercount'],
            'data_source' => $this->useRealData ? 'core' : 'demo'
        ];
    }
}