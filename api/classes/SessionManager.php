<?php
/**
 * Session Manager with Timeout and Security Features
 */

namespace appleJuiceNETZ\appleJuice;

class SessionManager
{
    private $sessionTimeout = 1800; // 30 minutes default
    private $maxIdleTime = 900;     // 15 minutes idle timeout
    private $sessionName = 'APPLEJUICE_SESSION';
    
    public function __construct($timeout = null, $idleTime = null)
    {
        if ($timeout) $this->sessionTimeout = $timeout;
        if ($idleTime) $this->maxIdleTime = $idleTime;
        
        $this->configureSession();
    }
    
    /**
     * Configure secure session settings
     */
    private function configureSession()
    {
        // Set session name
        session_name($this->sessionName);
        
        // Configure session security
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', 1);
        ini_set('session.gc_maxlifetime', $this->sessionTimeout);
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialize session security
        $this->initializeSessionSecurity();
    }
    
    /**
     * Initialize session security measures
     */
    private function initializeSessionSecurity()
    {
        $now = time();
        
        // Check if session is new
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = $now;
            $_SESSION['last_activity'] = $now;
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $_SESSION['ip_address'] = $this->getClientIP();
            return;
        }
        
        // Validate session security
        if (!$this->validateSessionSecurity()) {
            $this->destroySession();
            return;
        }
        
        // Check session timeout
        if ($this->isSessionExpired()) {
            $this->destroySession();
            return;
        }
        
        // Check idle timeout
        if ($this->isSessionIdle()) {
            $this->destroySession();
            return;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = $now;
        
        // Regenerate session ID periodically (every 5 minutes)
        if (!isset($_SESSION['last_regeneration']) || 
            ($now - $_SESSION['last_regeneration']) > 300) {
            $this->regenerateSessionId();
        }
    }
    
    /**
     * Validate session security (IP, User Agent)
     */
    private function validateSessionSecurity()
    {
        // Check User Agent
        $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $currentUserAgent) {
            return false;
        }
        
        // Check IP Address (optional - can be disabled for mobile users)
        $currentIP = $this->getClientIP();
        if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $currentIP) {
            // Log suspicious activity but don't block (mobile users change IPs)
            error_log("Session IP mismatch: {$_SESSION['ip_address']} vs {$currentIP}");
        }
        
        return true;
    }
    
    /**
     * Check if session has expired
     */
    private function isSessionExpired()
    {
        if (!isset($_SESSION['created'])) {
            return true;
        }
        
        return (time() - $_SESSION['created']) > $this->sessionTimeout;
    }
    
    /**
     * Check if session is idle
     */
    private function isSessionIdle()
    {
        if (!isset($_SESSION['last_activity'])) {
            return true;
        }
        
        return (time() - $_SESSION['last_activity']) > $this->maxIdleTime;
    }
    
    /**
     * Regenerate session ID
     */
    private function regenerateSessionId()
    {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
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
                // Handle comma-separated IPs (X-Forwarded-For)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Destroy session completely
     */
    public function destroySession()
    {
        $_SESSION = [];
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    /**
     * Check if session is valid
     */
    public function isValidSession()
    {
        return session_status() === PHP_SESSION_ACTIVE && 
               !$this->isSessionExpired() && 
               !$this->isSessionIdle() &&
               $this->validateSessionSecurity();
    }
    
    /**
     * Get session info
     */
    public function getSessionInfo()
    {
        if (!$this->isValidSession()) {
            return null;
        }
        
        return [
            'session_id' => session_id(),
            'created' => $_SESSION['created'] ?? null,
            'last_activity' => $_SESSION['last_activity'] ?? null,
            'expires_in' => $this->sessionTimeout - (time() - ($_SESSION['created'] ?? time())),
            'idle_time' => time() - ($_SESSION['last_activity'] ?? time()),
            'max_idle' => $this->maxIdleTime,
            'ip_address' => $_SESSION['ip_address'] ?? null
        ];
    }
    
    /**
     * Extend session timeout
     */
    public function extendSession($additionalTime = 1800)
    {
        if ($this->isValidSession()) {
            $_SESSION['created'] = time() - ($this->sessionTimeout - $additionalTime);
            return true;
        }
        return false;
    }
    
    /**
     * Set session timeout
     */
    public function setSessionTimeout($seconds)
    {
        $this->sessionTimeout = $seconds;
        ini_set('session.gc_maxlifetime', $seconds);
    }
    
    /**
     * Set idle timeout
     */
    public function setIdleTimeout($seconds)
    {
        $this->maxIdleTime = $seconds;
    }
}
?>