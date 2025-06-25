<?php
/**
 * Security Configuration for AppleJuice API
 * Centralized security settings and parameters
 */

return [
    // JWT Token Settings
    'jwt' => [
        'secret_key' => $_ENV['JWT_SECRET'] ?? 'your-super-secret-jwt-key-change-this-in-production',
        'algorithm' => 'HS256',
        'expiry' => 3600, // 1 hour
        'refresh_threshold' => 300, // Refresh if less than 5 minutes remaining
    ],
    
    // Session Management
    'session' => [
        'timeout' => 1800, // 30 minutes
        'idle_timeout' => 900, // 15 minutes
        'regenerate_interval' => 300, // 5 minutes
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
    ],
    
    // Rate Limiting
    'rate_limiting' => [
        'login' => [
            'max_attempts' => 5,
            'time_window' => 900, // 15 minutes
            'block_duration' => 3600, // 1 hour
        ],
        'api' => [
            'max_requests' => 100,
            'time_window' => 3600, // 1 hour
            'block_duration' => 1800, // 30 minutes
        ],
        'password_reset' => [
            'max_attempts' => 3,
            'time_window' => 3600, // 1 hour
            'block_duration' => 7200, // 2 hours
        ],
    ],
    
    // Security Logging
    'logging' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'INFO',
        'max_file_size' => 10485760, // 10MB
        'max_files' => 5,
        'log_directory' => $_ENV['LOG_DIR'] ?? sys_get_temp_dir() . '/applejuice_logs',
        'events' => [
            'auth_attempts' => true,
            'rate_limits' => true,
            'security_violations' => true,
            'api_access' => false, // Set to true for detailed API logging
            'token_events' => true,
            'session_events' => true,
        ],
    ],
    
    // Password Security
    'password' => [
        'min_length' => 8,
        'require_uppercase' => false, // AppleJuice Core compatibility
        'require_lowercase' => false,
        'require_numbers' => false,
        'require_symbols' => false,
        'hash_algorithm' => 'md5', // AppleJuice Core uses MD5
    ],
    
    // IP Security
    'ip_security' => [
        'check_user_agent' => true,
        'check_ip_consistency' => false, // Disabled for mobile users
        'allowed_ip_ranges' => [
            // '192.168.0.0/16',
            // '10.0.0.0/8',
            // '172.16.0.0/12',
        ],
        'blocked_ips' => [
            // Add IPs to block
        ],
    ],
    
    // CORS Settings
    'cors' => [
        'allowed_origins' => [
            'http://localhost:3000',
            'http://localhost:8080',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:8080',
        ],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
        'allow_credentials' => true,
    ],
    
    // Security Headers
    'headers' => [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';",
    ],
    
    // Monitoring & Alerts
    'monitoring' => [
        'failed_login_threshold' => 10, // Alert after 10 failed logins in time window
        'failed_login_window' => 3600, // 1 hour
        'security_violation_threshold' => 5,
        'security_violation_window' => 1800, // 30 minutes
        'alert_email' => $_ENV['SECURITY_ALERT_EMAIL'] ?? null,
        'webhook_url' => $_ENV['SECURITY_WEBHOOK_URL'] ?? null,
    ],
    
    // Cleanup & Maintenance
    'cleanup' => [
        'log_retention_days' => 30,
        'session_cleanup_interval' => 3600, // 1 hour
        'rate_limit_cleanup_interval' => 7200, // 2 hours
        'auto_cleanup_enabled' => true,
    ],
    
    // Development Settings
    'development' => [
        'debug_mode' => $_ENV['DEBUG'] ?? false,
        'disable_rate_limiting' => $_ENV['DISABLE_RATE_LIMITING'] ?? false,
        'log_all_requests' => $_ENV['LOG_ALL_REQUESTS'] ?? false,
        'mock_core_connection' => $_ENV['MOCK_CORE'] ?? false,
    ],
    
    // Feature Flags
    'features' => [
        'token_authentication' => true,
        'session_authentication' => true,
        'rate_limiting' => true,
        'security_logging' => true,
        'ip_validation' => false,
        'two_factor_auth' => false, // Future feature
        'api_key_auth' => false, // Future feature
    ],
];
?>