<?php
/**
 * AppleJuice Core Communication Class
 * 
 * Modernized version of the original Core class
 * Handles XML parsing and communication with AppleJuice Core
 */

namespace appleJuiceNETZ\appleJuice;

class Core
{
    private $depth;
    private $lastname;
    private $lastsubname;
    private $xml_array;
    private $lastcdata;
    
    public function __construct()
    {
        $this->depth = 0;
        $this->lastname = [];
        $this->lastsubname = [];
        $this->xml_array = [];
        $this->lastcdata = '';
    }
    
    /**
     * XML Parser Start Element Handler
     */
    public function startElement($parser, $name, $attrs)
    {
        $keys = array_keys($attrs);
        $array_name = "[$name]";
        
        if (!empty($keys[0])) {
            $array_name .= "[{$attrs[$keys[0]]}]";
        }
        
        if ($this->depth > 1) {
            for ($h = 0; $h <= ($this->depth - 2); $h++) {
                if ($h == 0) {
                    $array_name = "[{$this->lastsubname[$this->depth]}]" . $array_name;
                }
                $array_name = "[{$this->lastname[$this->depth - $h]}]" . $array_name;
            }
        }
        
        if (!empty($keys[0])) {
            $this->setArrayValue($array_name, []);
            
            foreach ($keys as $key) {
                $this->setArrayValue($array_name . "[$key]", $attrs[$key]);
            }
        }
        
        $this->depth++;
        $this->lastname[$this->depth] = $name;
        
        if (!empty($keys[0])) {
            $this->lastsubname[$this->depth] = $attrs[$keys[0]];
        } else {
            $this->lastsubname[$this->depth] = 'VALUES';
        }
    }
    
    /**
     * XML Parser End Element Handler
     */
    public function endElement($parser, $name)
    {
        unset($this->lastname[$this->depth]);
        unset($this->lastsubname[$this->depth]);
        $this->depth--;
    }
    
    /**
     * XML Parser Character Data Handler
     */
    public function characterData($parser, $data)
    {
        if (strlen(trim($data)) > 0 || $this->lastcdata == $this->lastname[$this->depth] 
            . "*" . $this->lastsubname[$this->depth] . "*" . $this->depth) {
            
            $array_name = "[{$this->lastsubname[$this->depth]}]";
            
            if ($this->depth > 1) {
                for ($g = 0; $g <= ($this->depth - 2); $g++) {
                    $array_name = "[{$this->lastname[$this->depth - $g]}]" . $array_name;
                }
            }
            
            $cdata_key = $this->lastname[$this->depth] . "*" . $this->lastsubname[$this->depth] . "*" . $this->depth;
            
            if ($this->lastcdata == $cdata_key) {
                $current_value = $this->getArrayValue($array_name . "[CDATA]");
                $this->setArrayValue($array_name . "[CDATA]", $current_value . $data);
            } else {
                $this->setArrayValue($array_name, []);
                $this->setArrayValue($array_name . "[CDATA]", $data);
            }
            
            $this->lastcdata = $cdata_key;
        }
    }
    
    /**
     * Safe array value setter (replaces eval)
     */
    private function setArrayValue($path, $value)
    {
        $keys = $this->parsePath($path);
        $current = &$this->xml_array;
        
        foreach ($keys as $key) {
            if (!isset($current[$key])) {
                $current[$key] = [];
            }
            $current = &$current[$key];
        }
        
        $current = $value;
    }
    
    /**
     * Safe array value getter
     */
    private function getArrayValue($path)
    {
        $keys = $this->parsePath($path);
        $current = $this->xml_array;
        
        foreach ($keys as $key) {
            if (!isset($current[$key])) {
                return null;
            }
            $current = $current[$key];
        }
        
        return $current;
    }
    
    /**
     * Parse array path string into keys
     */
    private function parsePath($path)
    {
        preg_match_all('/\[([^\]]+)\]/', $path, $matches);
        return $matches[1];
    }
    
    /**
     * Main command method to communicate with AppleJuice Core
     */
    public function command($type, $request, $update = null)
    {
        if ($update === null) {
            $this->xml_array = [];
        } else {
            $this->xml_array = is_array($update) ? $update : [];
        }
        
        $this->resetParser();
        
        // Build request URL
        $params = ['password' => $_SESSION['core_pass'] ?? ''];
        
        if (strpos($request, "?") === false) {
            $request .= "?";
        }
        
        $url = ($_SESSION['core_host'] ?? 'http://localhost:9851') . '/' . $type . '/' . $request . '&' . http_build_query($params);
        
        // Fetch data from AppleJuice Core
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'AppleJuice WebUI API/1.0'
            ]
        ]);
        
        $xml_file = @file_get_contents($url, false, $context);
        
        if ($xml_file === false) {
            throw new \Exception("Failed to connect to AppleJuice Core at: " . ($_SESSION['core_host'] ?? 'localhost:9851'));
        }
        
        // Check for authentication error
        if (str_contains($xml_file, "wrong password.")) {
            throw new \Exception("Authentication failed - wrong password");
        }
        
        if ($type === "xml") {
            if (empty($xml_file)) {
                throw new \Exception("Empty response from AppleJuice Core");
            }
            
            return $this->parseXML($xml_file);
        } else {
            return $xml_file;
        }
    }
    
    /**
     * Parse XML data
     */
    private function parseXML($xml_data)
    {
        $xml_parser = xml_parser_create("UTF-8");
        
        if (!$xml_parser) {
            throw new \Exception("Failed to create XML parser");
        }
        
        xml_set_element_handler($xml_parser, [$this, "startElement"], [$this, "endElement"]);
        xml_set_character_data_handler($xml_parser, [$this, "characterData"]);
        
        // Parse XML in chunks to handle large files
        $chunks = str_split($xml_data, 4096);
        
        foreach ($chunks as $chunk) {
            if (!xml_parse($xml_parser, $chunk)) {
                $error = xml_error_string(xml_get_error_code($xml_parser));
                xml_parser_free($xml_parser);
                throw new \Exception("XML Parser Error: " . $error);
            }
        }
        
        // Final parse with empty string to signal end
        xml_parse($xml_parser, '', true);
        xml_parser_free($xml_parser);
        
        return $this->xml_array;
    }
    
    /**
     * Reset parser state
     */
    private function resetParser()
    {
        $this->depth = 0;
        $this->lastname = [];
        $this->lastsubname = [];
        $this->lastcdata = '';
    }
    
    /**
     * Get AppleJuice Core version information
     */
    public function getCoreVersion()
    {
        // Check cache first
        if (!empty($_SESSION['cache']['STATUSBAR']['VERSION'])) {
            return [
                "VERSION" => $_SESSION['cache']['STATUSBAR']['VERSION'],
                "SYSTEM" => $_SESSION['cache']['STATUSBAR']['SYSTEM']
            ];
        }
        
        try {
            $core_info = $this->command("xml", "information.xml");
            
            $version = $core_info['GENERALINFORMATION']['VERSION']['VALUES']['CDATA'] ?? 'Unknown';
            $system = $core_info['GENERALINFORMATION']['SYSTEM']['VALUES']['CDATA'] ?? 'Unknown';
            
            // Cache the results
            $_SESSION['cache']['STATUSBAR']['VERSION'] = $version;
            $_SESSION['cache']['STATUSBAR']['SYSTEM'] = $system;
            
            return [
                "VERSION" => $version,
                "SYSTEM" => $system
            ];
            
        } catch (\Exception $e) {
            return [
                "VERSION" => "Error: " . $e->getMessage(),
                "SYSTEM" => "Unknown"
            ];
        }
    }
    
    /**
     * Test connection to AppleJuice Core
     */
    public function testConnection($host = null, $password = null)
    {
        $original_host = $_SESSION['core_host'] ?? null;
        $original_pass = $_SESSION['core_pass'] ?? null;
        
        if ($host) $_SESSION['core_host'] = $host;
        if ($password !== null) $_SESSION['core_pass'] = $password;
        
        try {
            $result = $this->command("xml", "information.xml");
            $success = !empty($result);
            
            return [
                'success' => $success,
                'version' => $result['GENERALINFORMATION']['VERSION']['VALUES']['CDATA'] ?? null,
                'system' => $result['GENERALINFORMATION']['SYSTEM']['VALUES']['CDATA'] ?? null
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } finally {
            // Restore original settings if test failed
            if ($host && $original_host) $_SESSION['core_host'] = $original_host;
            if ($password !== null && $original_pass !== null) $_SESSION['core_pass'] = $original_pass;
        }
    }
    
    /**
     * Get current connection status
     */
    public function getConnectionStatus()
    {
        try {
            $info = $this->command("xml", "information.xml");
            
            return [
                'connected' => true,
                'host' => $_SESSION['core_host'] ?? 'localhost:9851',
                'version' => $info['GENERALINFORMATION']['VERSION']['VALUES']['CDATA'] ?? 'Unknown',
                'uptime' => intval($info['GENERALINFORMATION']['UPTIME']['VALUES']['CDATA'] ?? 0)
            ];
            
        } catch (\Exception $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage(),
                'host' => $_SESSION['core_host'] ?? 'localhost:9851'
            ];
        }
    }
}
?>