<?php
/**
 * Debug Script für Server-API
 * 
 * Testet die Server-Klasse und zeigt Debug-Informationen
 */

// Start session
session_start();

// Include classes
require_once 'classes/Core.php';
require_once 'classes/Server.php';
require_once 'classes/ApiResponse.php';

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Server;

echo "<h1>🔍 AppleJuice Server Debug</h1>";

// Test 1: Core-Verbindung
echo "<h2>1. Core-Verbindung testen</h2>";

$core = new Core();

try {
    echo "<p>🧪 Teste Core-Verbindung...</p>";
    
    // Test XML command
    $result = $core->command("xml", "modified.xml?filter=server;informations");
    
    if ($result) {
        echo "<p>✅ Core-Verbindung erfolgreich!</p>";
        echo "<h3>XML-Daten erhalten:</h3>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    } else {
        echo "<p>❌ Core-Verbindung fehlgeschlagen - Keine Daten erhalten</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Core-Exception: " . $e->getMessage() . "</p>";
}

// Test 2: Server-Klasse ohne Core
echo "<h2>2. Server-Klasse mit noload=1 (ohne Core)</h2>";

try {
    $server_noload = new Server(1); // noload = 1
    echo "<p>✅ Server-Klasse ohne Core-Load erstellt</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Server-Klasse Exception: " . $e->getMessage() . "</p>";
}

// Test 3: Server-Klasse mit Core
echo "<h2>3. Server-Klasse mit Core-Load</h2>";

try {
    echo "<p>🧪 Erstelle Server-Klasse mit Core-Load...</p>";
    $server = new Server(0); // noload = 0, versucht Core zu laden
    
    echo "<p>✅ Server-Klasse mit Core erstellt</p>";
    
    // Test Server-Liste
    echo "<h3>Server-Liste:</h3>";
    $servers = $server->getServerList();
    echo "<pre>" . print_r($servers, true) . "</pre>";
    
    // Test Netzwerk-Status
    echo "<h3>Netzwerk-Status:</h3>";
    $netstats = $server->netstats();
    echo "<pre>" . print_r($netstats, true) . "</pre>";
    
    // Test Verbindungs-Status
    echo "<h3>Verbindungs-Status:</h3>";
    $connectionStatus = $server->getConnectionStatus();
    echo "<pre>" . print_r($connectionStatus, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ Server-Klasse Exception: " . $e->getMessage() . "</p>";
    echo "<p>🔄 Fallback auf Demo-Daten wird verwendet</p>";
}

// Test 4: XML-Datei direkt testen
echo "<h2>4. Test-XML-Datei laden</h2>";

if (file_exists('test/server.xml')) {
    echo "<p>✅ Test-XML-Datei gefunden</p>";
    
    $xml_content = file_get_contents('test/server.xml');
    echo "<h3>XML-Inhalt:</h3>";
    echo "<pre>" . htmlspecialchars($xml_content) . "</pre>";
    
    // Test XML-Parsing
    try {
        $xml = simplexml_load_string($xml_content);
        if ($xml) {
            echo "<p>✅ XML-Parsing erfolgreich</p>";
            echo "<h3>Geparste XML-Struktur:</h3>";
            echo "<pre>" . print_r($xml, true) . "</pre>";
        } else {
            echo "<p>❌ XML-Parsing fehlgeschlagen</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ XML-Parsing Exception: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p>❌ Test-XML-Datei nicht gefunden: test/server.xml</p>";
}

// Test 5: Core-Konfiguration
echo "<h2>5. Core-Konfiguration</h2>";

echo "<p><strong>Session-Daten:</strong></p>";
echo "<pre>";
echo "core_host: " . ($_SESSION['core_host'] ?? 'nicht gesetzt') . "\n";
echo "core_pass: " . ($_SESSION['core_pass'] ?? 'nicht gesetzt') . "\n";
echo "</pre>";

echo "<p><strong>PHP-Konfiguration:</strong></p>";
echo "<pre>";
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'aktiviert' : 'deaktiviert') . "\n";
echo "user_agent: " . ini_get('user_agent') . "\n";
echo "default_socket_timeout: " . ini_get('default_socket_timeout') . "\n";
echo "</pre>";

// Test 6: API-Endpoint direkt testen
echo "<h2>6. API-Endpoint direkt testen</h2>";

try {
    // Simuliere API-Aufruf
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['path'] = 'servers';
    
    echo "<p>🧪 Teste /api/servers Endpoint...</p>";
    
    // Include API
    ob_start();
    include 'index.php';
    $api_output = ob_get_clean();
    
    echo "<h3>API-Response:</h3>";
    echo "<pre>" . htmlspecialchars($api_output) . "</pre>";
    
    // Parse JSON
    $json_data = json_decode($api_output, true);
    if ($json_data) {
        echo "<p>✅ JSON-Response erfolgreich geparst</p>";
        
        if (isset($json_data['data']['servers'])) {
            $server_count = count($json_data['data']['servers']);
            echo "<p>📊 Anzahl Server: $server_count</p>";
            
            if ($server_count > 0) {
                $first_server = $json_data['data']['servers'][0];
                echo "<p>🖥️ Erster Server: " . ($first_server['name'] ?? 'Unbekannt') . "</p>";
                
                // Prüfe ob es Demo-Daten sind
                if (strpos($first_server['name'], 'AppleJuice Server DE1') !== false) {
                    echo "<p>⚠️ <strong>DEMO-DATEN ERKANNT!</strong></p>";
                    echo "<p>🔍 Grund: Core-Verbindung fehlgeschlagen, Fallback auf Demo-Daten</p>";
                } else {
                    echo "<p>✅ Echte Server-Daten erhalten</p>";
                }
            }
        }
    } else {
        echo "<p>❌ JSON-Response konnte nicht geparst werden</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ API-Test Exception: " . $e->getMessage() . "</p>";
}

// Test 7: Lösungsvorschläge
echo "<h2>7. 🛠️ Lösungsvorschläge</h2>";

echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #0066cc;'>";
echo "<h3>Mögliche Ursachen für Demo-Daten:</h3>";
echo "<ol>";
echo "<li><strong>AppleJuice Core nicht gestartet</strong><br>→ Core auf Port 9851 starten</li>";
echo "<li><strong>Falsche Core-URL</strong><br>→ Session-Variable 'core_host' prüfen</li>";
echo "<li><strong>Netzwerk-Probleme</strong><br>→ Firewall/Proxy-Einstellungen prüfen</li>";
echo "<li><strong>XML-Parsing-Fehler</strong><br>→ Core-Response-Format prüfen</li>";
echo "<li><strong>PHP-Konfiguration</strong><br>→ allow_url_fopen aktivieren</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #f0fff0; padding: 15px; border-left: 4px solid #00cc66; margin-top: 10px;'>";
echo "<h3>Nächste Schritte:</h3>";
echo "<ol>";
echo "<li>AppleJuice Core starten (ajcore.exe)</li>";
echo "<li>Core-Verbindung in Session konfigurieren</li>";
echo "<li>XML-Response vom Core testen</li>";
echo "<li>Server-Klasse mit echten Daten testen</li>";
echo "</ol>";
echo "</div>";

echo "<h2>✅ Debug-Analyse abgeschlossen</h2>";
echo "<p><em>Timestamp: " . date('Y-m-d H:i:s') . "</em></p>";
?><?php
/**
 * Debug Script für Server-API
 * 
 * Testet die Server-Klasse und zeigt Debug-Informationen
 */

// Start session
session_start();

// Include classes
require_once 'classes/Core.php';
require_once 'classes/Server.php';
require_once 'classes/ApiResponse.php';

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Server;

echo "<h1>🔍 AppleJuice Server Debug</h1>";

// Test 1: Core-Verbindung
echo "<h2>1. Core-Verbindung testen</h2>";

$core = new Core();

try {
    echo "<p>🧪 Teste Core-Verbindung...</p>";
    
    // Test XML command
    $result = $core->command("xml", "modified.xml?filter=server;informations");
    
    if ($result) {
        echo "<p>✅ Core-Verbindung erfolgreich!</p>";
        echo "<h3>XML-Daten erhalten:</h3>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    } else {
        echo "<p>❌ Core-Verbindung fehlgeschlagen - Keine Daten erhalten</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Core-Exception: " . $e->getMessage() . "</p>";
}

// Test 2: Server-Klasse ohne Core
echo "<h2>2. Server-Klasse mit noload=1 (ohne Core)</h2>";

try {
    $server_noload = new Server(1); // noload = 1
    echo "<p>✅ Server-Klasse ohne Core-Load erstellt</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Server-Klasse Exception: " . $e->getMessage() . "</p>";
}

// Test 3: Server-Klasse mit Core
echo "<h2>3. Server-Klasse mit Core-Load</h2>";

try {
    echo "<p>🧪 Erstelle Server-Klasse mit Core-Load...</p>";
    $server = new Server(0); // noload = 0, versucht Core zu laden
    
    echo "<p>✅ Server-Klasse mit Core erstellt</p>";
    
    // Test Server-Liste
    echo "<h3>Server-Liste:</h3>";
    $servers = $server->getServerList();
    echo "<pre>" . print_r($servers, true) . "</pre>";
    
    // Test Netzwerk-Status
    echo "<h3>Netzwerk-Status:</h3>";
    $netstats = $server->netstats();
    echo "<pre>" . print_r($netstats, true) . "</pre>";
    
    // Test Verbindungs-Status
    echo "<h3>Verbindungs-Status:</h3>";
    $connectionStatus = $server->getConnectionStatus();
    echo "<pre>" . print_r($connectionStatus, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ Server-Klasse Exception: " . $e->getMessage() . "</p>";
    echo "<p>🔄 Fallback auf Demo-Daten wird verwendet</p>";
}

// Test 4: XML-Datei direkt testen
echo "<h2>4. Test-XML-Datei laden</h2>";

if (file_exists('test/server.xml')) {
    echo "<p>✅ Test-XML-Datei gefunden</p>";
    
    $xml_content = file_get_contents('test/server.xml');
    echo "<h3>XML-Inhalt:</h3>";
    echo "<pre>" . htmlspecialchars($xml_content) . "</pre>";
    
    // Test XML-Parsing
    try {
        $xml = simplexml_load_string($xml_content);
        if ($xml) {
            echo "<p>✅ XML-Parsing erfolgreich</p>";
            echo "<h3>Geparste XML-Struktur:</h3>";
            echo "<pre>" . print_r($xml, true) . "</pre>";
        } else {
            echo "<p>❌ XML-Parsing fehlgeschlagen</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ XML-Parsing Exception: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p>❌ Test-XML-Datei nicht gefunden: test/server.xml</p>";
}

// Test 5: Core-Konfiguration
echo "<h2>5. Core-Konfiguration</h2>";

echo "<p><strong>Session-Daten:</strong></p>";
echo "<pre>";
echo "core_host: " . ($_SESSION['core_host'] ?? 'nicht gesetzt') . "\n";
echo "core_pass: " . ($_SESSION['core_pass'] ?? 'nicht gesetzt') . "\n";
echo "</pre>";

echo "<p><strong>PHP-Konfiguration:</strong></p>";
echo "<pre>";
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'aktiviert' : 'deaktiviert') . "\n";
echo "user_agent: " . ini_get('user_agent') . "\n";
echo "default_socket_timeout: " . ini_get('default_socket_timeout') . "\n";
echo "</pre>";

// Test 6: API-Endpoint direkt testen
echo "<h2>6. API-Endpoint direkt testen</h2>";

try {
    // Simuliere API-Aufruf
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['path'] = 'servers';
    
    echo "<p>🧪 Teste /api/servers Endpoint...</p>";
    
    // Include API
    ob_start();
    include 'index.php';
    $api_output = ob_get_clean();
    
    echo "<h3>API-Response:</h3>";
    echo "<pre>" . htmlspecialchars($api_output) . "</pre>";
    
    // Parse JSON
    $json_data = json_decode($api_output, true);
    if ($json_data) {
        echo "<p>✅ JSON-Response erfolgreich geparst</p>";
        
        if (isset($json_data['data']['servers'])) {
            $server_count = count($json_data['data']['servers']);
            echo "<p>📊 Anzahl Server: $server_count</p>";
            
            if ($server_count > 0) {
                $first_server = $json_data['data']['servers'][0];
                echo "<p>🖥️ Erster Server: " . ($first_server['name'] ?? 'Unbekannt') . "</p>";
                
                // Prüfe ob es Demo-Daten sind
                if (strpos($first_server['name'], 'AppleJuice Server DE1') !== false) {
                    echo "<p>⚠️ <strong>DEMO-DATEN ERKANNT!</strong></p>";
                    echo "<p>🔍 Grund: Core-Verbindung fehlgeschlagen, Fallback auf Demo-Daten</p>";
                } else {
                    echo "<p>✅ Echte Server-Daten erhalten</p>";
                }
            }
        }
    } else {
        echo "<p>❌ JSON-Response konnte nicht geparst werden</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ API-Test Exception: " . $e->getMessage() . "</p>";
}

// Test 7: Lösungsvorschläge
echo "<h2>7. 🛠️ Lösungsvorschläge</h2>";

echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #0066cc;'>";
echo "<h3>Mögliche Ursachen für Demo-Daten:</h3>";
echo "<ol>";
echo "<li><strong>AppleJuice Core nicht gestartet</strong><br>→ Core auf Port 9851 starten</li>";
echo "<li><strong>Falsche Core-URL</strong><br>→ Session-Variable 'core_host' prüfen</li>";
echo "<li><strong>Netzwerk-Probleme</strong><br>→ Firewall/Proxy-Einstellungen prüfen</li>";
echo "<li><strong>XML-Parsing-Fehler</strong><br>→ Core-Response-Format prüfen</li>";
echo "<li><strong>PHP-Konfiguration</strong><br>→ allow_url_fopen aktivieren</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #f0fff0; padding: 15px; border-left: 4px solid #00cc66; margin-top: 10px;'>";
echo "<h3>Nächste Schritte:</h3>";
echo "<ol>";
echo "<li>AppleJuice Core starten (ajcore.exe)</li>";
echo "<li>Core-Verbindung in Session konfigurieren</li>";
echo "<li>XML-Response vom Core testen</li>";
echo "<li>Server-Klasse mit echten Daten testen</li>";
echo "</ol>";
echo "</div>";

echo "<h2>✅ Debug-Analyse abgeschlossen</h2>";
echo "<p><em>Timestamp: " . date('Y-m-d H:i:s') . "</em></p>";
?>