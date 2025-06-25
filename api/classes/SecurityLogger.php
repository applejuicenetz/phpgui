<?php
/**
 * Security Logger for Authentication and Security Events
 * Logs security-related events for monitoring and analysis
 */

namespace appleJuiceNETZ\appleJuice;

class SecurityLogger
{
    private $logFile;
    private $maxLogSize = 10485760; // 10MB
    private $maxLogFiles = 5;
    private $logLevel = 'INFO';
    
    const LEVEL_DEBUG = 0;
    const LEVEL_INFO = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_ERROR = 3;
    const LEVEL_CRITICAL = 4;
    
    private $levels = [
        'DEBUG' => self::LEVEL_DEBUG,
        'INFO' => self::LEVEL_INFO,
        'WARNING' => self::LEVEL_WARNING,
        'ERROR' => self::LEVEL_ERROR,
        'CRITICAL' => self::LEVEL_CRITICAL
    ];
    
    public function __construct($logDir = null, $logLevel = 'INFO')
    {
        $logDir = $logDir ?: (sys_get_temp_dir() . '/applejuice_logs');
        
        // Create log directory if it doesn't exist
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $this->logFile = $logDir . '/security.log';
        $this->logLevel = $logLevel;
    }
    
    /**
     * Log authentication attempt
     */
    public function logAuthAttempt($username, $host, $success, $details = [])
    {
        $event = [
            'event_type' => 'AUTH_ATTEMPT',
            'username' => $username,
            'host' => $host,
            'success' => $success,
            'ip_address' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'details' => $details
        ];
        
        $level = $success ? 'INFO' : 'WARNING';
        $message = $success ? 
            "Successful login for user '{$username}' from {$host}" :
            "Failed login attempt for user '{$username}' from {$host}";
        
        $this->log($level, $message, $event);
    }
    
    /**
     * Log rate limiting event
     */
    public function logRateLimit($identifier, $action, $blocked, $attempts)
    {
        $event = [
            'event_type' => 'RATE_LIMIT',
            'identifier' => $identifier,
            'action' => $action,
            'blocked' => $blocked,
            'attempts' => $attempts,
            'ip_address' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
        
        $level = $blocked ? 'ERROR' : 'WARNING';
        $message = $blocked ?
            "Rate limit exceeded for {$action} - client blocked" :
            "Rate limit warning for {$action} - {$attempts} attempts";
        
        $this->log($level, $message, $event);
    }
    
    /**
     * Log session event
     */
    public function logSessionEvent($eventType, $sessionId, $details = [])
    {
        $event = [
            'event_type' => 'SESSION_' . strtoupper($eventType),
            'session_id' => $sessionId,
            'ip_address' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'details' => $details
        ];
        
        $level = in_array($eventType, ['expired', 'invalid', 'hijack']) ? 'WARNING' : 'INFO';
        $message = "Session {$eventType}: {$sessionId}";
        
        $this->log($level, $message, $event);
    }
    
    /**
     * Log security violation
     */
    public function logSecurityViolation($violationType, $description, $details = [])
    {
        $event = [
            'event_type' => 'SECURITY_VIOLATION',
            'violation_type' => $violationType,
            'description' => $description,
            'ip_address' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'details' => $details
        ];
        
        $this->log('ERROR', "Security violation: {$violationType} - {$description}", $event);
    }
    
    /**
     * Log API access
     */
    public function logApiAccess($endpoint, $method, $authenticated, $responseCode, $details = [])
    {
        $event = [
            'event_type' => 'API_ACCESS',
            'endpoint' => $endpoint,
            'method' => $method,
            'authenticated' => $authenticated,
            'response_code' => $responseCode,
            'ip_address' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'details' => $details
        ];
        
        $level = $responseCode >= 400 ? 'WARNING' : 'INFO';
        $message = "API access: {$method} {$endpoint} - {$responseCode}";
        
        $this->log($level, $message, $event);
    }
    
    /**
     * Log token event
     */
    public function logTokenEvent($eventType, $tokenId, $details = [])
    {
        $event = [
            'event_type' => 'TOKEN_' . strtoupper($eventType),
            'token_id' => $tokenId,
            'ip_address' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'details' => $details
        ];
        
        $level = in_array($eventType, ['expired', 'invalid', 'revoked']) ? 'WARNING' : 'INFO';
        $message = "Token {$eventType}: {$tokenId}";
        
        $this->log($level, $message, $event);
    }
    
    /**
     * Main logging method
     */
    public function log($level, $message, $context = [])
    {
        if (!$this->shouldLog($level)) {
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $levelPadded = str_pad($level, 8);
        
        $logEntry = [
            'timestamp' => $timestamp,
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'server' => [
                'request_id' => $this->getRequestId(),
                'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? '',
                'request_method' => $_SERVER['REQUEST_METHOD'] ?? '',
                'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
                'http_host' => $_SERVER['HTTP_HOST'] ?? ''
            ]
        ];
        
        $logLine = "[{$timestamp}] {$levelPadded} {$message} " . json_encode($context) . "\n";
        
        $this->writeToFile($logLine);
        
        // Also log to system log for critical events
        if ($this->levels[$level] >= self::LEVEL_ERROR) {
            error_log("AppleJuice Security: [{$level}] {$message}");
        }
    }
    
    /**
     * Check if should log based on level
     */
    private function shouldLog($level)
    {
        return $this->levels[$level] >= $this->levels[$this->logLevel];
    }
    
    /**
     * Write log entry to file
     */
    private function writeToFile($logLine)
    {
        // Check if log rotation is needed
        if (file_exists($this->logFile) && filesize($this->logFile) > $this->maxLogSize) {
            $this->rotateLogFile();
        }
        
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Rotate log files
     */
    private function rotateLogFile()
    {
        // Move existing log files
        for ($i = $this->maxLogFiles - 1; $i > 0; $i--) {
            $oldFile = $this->logFile . '.' . $i;
            $newFile = $this->logFile . '.' . ($i + 1);
            
            if (file_exists($oldFile)) {
                if ($i == $this->maxLogFiles - 1) {
                    unlink($oldFile); // Delete oldest file
                } else {
                    rename($oldFile, $newFile);
                }
            }
        }
        
        // Move current log file
        if (file_exists($this->logFile)) {
            rename($this->logFile, $this->logFile . '.1');
        }
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP()
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Generate unique request ID
     */
    private function getRequestId()
    {
        static $requestId = null;
        
        if ($requestId === null) {
            $requestId = uniqid('req_', true);
        }
        
        return $requestId;
    }
    
    /**
     * Get recent security events
     */
    public function getRecentEvents($limit = 100, $level = null)
    {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $events = [];
        
        // Process lines in reverse order (newest first)
        $lines = array_reverse($lines);
        
        foreach ($lines as $line) {
            if (count($events) >= $limit) {
                break;
            }
            
            // Parse log line
            if (preg_match('/^\[(.*?)\]\s+(\w+)\s+(.*?)\s+(\{.*\})$/', $line, $matches)) {
                $timestamp = $matches[1];
                $logLevel = trim($matches[2]);
                $message = $matches[3];
                $context = json_decode($matches[4], true);
                
                // Filter by level if specified
                if ($level && $logLevel !== $level) {
                    continue;
                }
                
                $events[] = [
                    'timestamp' => $timestamp,
                    'level' => $logLevel,
                    'message' => $message,
                    'context' => $context
                ];
            }
        }
        
        return $events;
    }
    
    /**
     * Get security statistics
     */
    public function getSecurityStats($hours = 24)
    {
        $events = $this->getRecentEvents(1000);
        $cutoff = time() - ($hours * 3600);
        
        $stats = [
            'total_events' => 0,
            'auth_attempts' => 0,
            'failed_logins' => 0,
            'successful_logins' => 0,
            'rate_limits' => 0,
            'security_violations' => 0,
            'unique_ips' => [],
            'top_events' => []
        ];
        
        foreach ($events as $event) {
            $eventTime = strtotime($event['timestamp']);
            if ($eventTime < $cutoff) {
                continue;
            }
            
            $stats['total_events']++;
            
            $context = $event['context'];
            $eventType = $context['event_type'] ?? 'UNKNOWN';
            
            // Count by event type
            if (!isset($stats['top_events'][$eventType])) {
                $stats['top_events'][$eventType] = 0;
            }
            $stats['top_events'][$eventType]++;
            
            // Specific counters
            switch ($eventType) {
                case 'AUTH_ATTEMPT':
                    $stats['auth_attempts']++;
                    if ($context['success']) {
                        $stats['successful_logins']++;
                    } else {
                        $stats['failed_logins']++;
                    }
                    break;
                case 'RATE_LIMIT':
                    $stats['rate_limits']++;
                    break;
                case 'SECURITY_VIOLATION':
                    $stats['security_violations']++;
                    break;
            }
            
            // Track unique IPs
            if (isset($context['ip_address'])) {
                $stats['unique_ips'][$context['ip_address']] = true;
            }
        }
        
        $stats['unique_ips'] = count($stats['unique_ips']);
        arsort($stats['top_events']);
        
        return $stats;
    }
    
    /**
     * Set log level
     */
    public function setLogLevel($level)
    {
        if (isset($this->levels[$level])) {
            $this->logLevel = $level;
        }
    }
}
?>