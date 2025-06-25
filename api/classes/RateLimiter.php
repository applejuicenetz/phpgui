<?php
/**
 * Rate Limiter for Login Attempts and API Calls
 * Prevents brute force attacks and API abuse
 */

namespace appleJuiceNETZ\appleJuice;

class RateLimiter
{
    private $storageFile;
    private $maxAttempts = 5;
    private $timeWindow = 900; // 15 minutes
    private $blockDuration = 3600; // 1 hour
    
    public function __construct($storageDir = null)
    {
        $storageDir = $storageDir ?: sys_get_temp_dir();
        $this->storageFile = $storageDir . '/applejuice_rate_limit.json';
        $this->cleanupOldEntries();
    }
    
    /**
     * Check if IP/identifier is rate limited
     */
    public function isRateLimited($identifier, $action = 'login')
    {
        $data = $this->loadData();
        $key = $this->generateKey($identifier, $action);
        
        if (!isset($data[$key])) {
            return false;
        }
        
        $entry = $data[$key];
        $now = time();
        
        // Check if block period has expired
        if (isset($entry['blocked_until']) && $now > $entry['blocked_until']) {
            unset($data[$key]);
            $this->saveData($data);
            return false;
        }
        
        // Check if currently blocked
        if (isset($entry['blocked_until']) && $now <= $entry['blocked_until']) {
            return [
                'blocked' => true,
                'blocked_until' => $entry['blocked_until'],
                'remaining_time' => $entry['blocked_until'] - $now,
                'attempts' => $entry['attempts'],
                'reason' => 'Too many failed attempts'
            ];
        }
        
        return false;
    }
    
    /**
     * Record an attempt (successful or failed)
     */
    public function recordAttempt($identifier, $action = 'login', $success = false)
    {
        $data = $this->loadData();
        $key = $this->generateKey($identifier, $action);
        $now = time();
        
        if (!isset($data[$key])) {
            $data[$key] = [
                'attempts' => 0,
                'first_attempt' => $now,
                'last_attempt' => $now,
                'successful_attempts' => 0,
                'failed_attempts' => 0
            ];
        }
        
        $entry = &$data[$key];
        
        // Reset if time window has passed
        if (($now - $entry['first_attempt']) > $this->timeWindow) {
            $entry = [
                'attempts' => 0,
                'first_attempt' => $now,
                'last_attempt' => $now,
                'successful_attempts' => 0,
                'failed_attempts' => 0
            ];
        }
        
        // Record attempt
        $entry['attempts']++;
        $entry['last_attempt'] = $now;
        
        if ($success) {
            $entry['successful_attempts']++;
            // Reset failed attempts on success
            $entry['failed_attempts'] = 0;
        } else {
            $entry['failed_attempts']++;
            
            // Check if should be blocked
            if ($entry['failed_attempts'] >= $this->maxAttempts) {
                $entry['blocked_until'] = $now + $this->blockDuration;
            }
        }
        
        $this->saveData($data);
        
        return [
            'attempts' => $entry['attempts'],
            'failed_attempts' => $entry['failed_attempts'],
            'remaining_attempts' => max(0, $this->maxAttempts - $entry['failed_attempts']),
            'blocked' => isset($entry['blocked_until']) && $entry['blocked_until'] > $now
        ];
    }
    
    /**
     * Get attempt statistics
     */
    public function getAttemptStats($identifier, $action = 'login')
    {
        $data = $this->loadData();
        $key = $this->generateKey($identifier, $action);
        
        if (!isset($data[$key])) {
            return [
                'attempts' => 0,
                'failed_attempts' => 0,
                'successful_attempts' => 0,
                'remaining_attempts' => $this->maxAttempts,
                'blocked' => false,
                'time_window_remaining' => $this->timeWindow
            ];
        }
        
        $entry = $data[$key];
        $now = time();
        
        return [
            'attempts' => $entry['attempts'],
            'failed_attempts' => $entry['failed_attempts'],
            'successful_attempts' => $entry['successful_attempts'],
            'remaining_attempts' => max(0, $this->maxAttempts - $entry['failed_attempts']),
            'blocked' => isset($entry['blocked_until']) && $entry['blocked_until'] > $now,
            'blocked_until' => $entry['blocked_until'] ?? null,
            'time_window_remaining' => max(0, $this->timeWindow - ($now - $entry['first_attempt']))
        ];
    }
    
    /**
     * Clear rate limit for identifier
     */
    public function clearRateLimit($identifier, $action = 'login')
    {
        $data = $this->loadData();
        $key = $this->generateKey($identifier, $action);
        
        if (isset($data[$key])) {
            unset($data[$key]);
            $this->saveData($data);
            return true;
        }
        
        return false;
    }
    
    /**
     * Get client identifier (IP + User Agent hash)
     */
    public function getClientIdentifier()
    {
        $ip = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Create a hash of IP + User Agent for privacy
        return hash('sha256', $ip . '|' . $userAgent);
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
     * Generate storage key
     */
    private function generateKey($identifier, $action)
    {
        return hash('sha256', $identifier . '|' . $action);
    }
    
    /**
     * Load rate limit data from storage
     */
    private function loadData()
    {
        if (!file_exists($this->storageFile)) {
            return [];
        }
        
        $content = file_get_contents($this->storageFile);
        $data = json_decode($content, true);
        
        return is_array($data) ? $data : [];
    }
    
    /**
     * Save rate limit data to storage
     */
    private function saveData($data)
    {
        $content = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($this->storageFile, $content, LOCK_EX);
    }
    
    /**
     * Clean up old entries
     */
    private function cleanupOldEntries()
    {
        $data = $this->loadData();
        $now = time();
        $changed = false;
        
        foreach ($data as $key => $entry) {
            // Remove entries older than block duration + time window
            $maxAge = $this->blockDuration + $this->timeWindow;
            if (($now - $entry['first_attempt']) > $maxAge) {
                unset($data[$key]);
                $changed = true;
            }
        }
        
        if ($changed) {
            $this->saveData($data);
        }
    }
    
    /**
     * Configure rate limiting parameters
     */
    public function configure($maxAttempts = null, $timeWindow = null, $blockDuration = null)
    {
        if ($maxAttempts !== null) $this->maxAttempts = $maxAttempts;
        if ($timeWindow !== null) $this->timeWindow = $timeWindow;
        if ($blockDuration !== null) $this->blockDuration = $blockDuration;
    }
    
    /**
     * Get configuration
     */
    public function getConfiguration()
    {
        return [
            'max_attempts' => $this->maxAttempts,
            'time_window' => $this->timeWindow,
            'block_duration' => $this->blockDuration
        ];
    }
}
?>