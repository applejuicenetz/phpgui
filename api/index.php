<?php
/**
 * AppleJuice P2P WebUI - REST API
 * 
 * Modern PHP API that reads XML data from AppleJuice Core
 * and converts it to JSON for the Vue.js frontend
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Start session for core authentication
session_start();

// Include required classes
require_once 'classes/Core.php';
require_once 'classes/Server.php';
require_once 'classes/ApiResponse.php';
require_once 'classes/TokenManager.php';
require_once 'classes/SessionManager.php';
require_once 'classes/RateLimiter.php';
require_once 'classes/SecurityLogger.php';

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\appleJuice\ApiResponse;
use appleJuiceNETZ\appleJuice\TokenManager;
use appleJuiceNETZ\appleJuice\SessionManager;
use appleJuiceNETZ\appleJuice\RateLimiter;
use appleJuiceNETZ\appleJuice\SecurityLogger;

// Initialize API
$api = new AppleJuiceAPI();
$api->handleRequest();

class AppleJuiceAPI
{
    private $core;
    private $server;
    private $response;
    private $tokenManager;
    private $sessionManager;
    private $rateLimiter;
    private $securityLogger;
    
    public function __construct()
    {
        $this->core = new Core();
        $this->server = new Server();
        $this->response = new ApiResponse();
        $this->tokenManager = new TokenManager();
        $this->sessionManager = new SessionManager(1800, 900); // 30min session, 15min idle
        $this->rateLimiter = new RateLimiter();
        $this->securityLogger = new SecurityLogger();
        
        // Configure rate limiter
        $this->rateLimiter->configure(5, 900, 3600); // 5 attempts, 15min window, 1h block
        
        // Set default core connection if not in session
        if (empty($_SESSION['core_host'])) {
            $_SESSION['core_host'] = 'http://localhost:9851';
        }
        if (empty($_SESSION['core_pass'])) {
            $_SESSION['core_pass'] = '';
        }
    }
    
    public function handleRequest()
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $path = $this->getPath();
            
            switch ($method) {
                case 'GET':
                    $this->handleGet($path);
                    break;
                case 'POST':
                    $this->handlePost($path);
                    break;
                default:
                    $this->response->error('Method not allowed', 405);
            }
        } catch (Exception $e) {
            $this->response->error($e->getMessage(), 500);
        }
    }
    
    private function getPath()
    {
        $path = $_GET['path'] ?? '';
        return trim($path, '/');
    }
    
    private function handleGet($path)
    {
        $parts = explode('/', $path);
        $endpoint = $parts[0] ?? '';
        
        // Public endpoints that don't require authentication
        $publicEndpoints = ['auth'];
        
        // Check authentication for protected endpoints
        if (!in_array($endpoint, $publicEndpoints) && !$this->isAuthenticated()) {
            $this->response->error([
                'message' => 'Authentication required',
                'details' => 'Please login first'
            ], 401);
            return;
        }
        
        switch ($endpoint) {
            case 'servers':
                $this->getServers();
                break;
            case 'server':
                $this->getServerInfo($parts[1] ?? null);
                break;
            case 'downloads':
                $this->getDownloads();
                break;
            case 'uploads':
                $this->getUploads();
                break;
            case 'search':
                $this->getSearchResults();
                break;
            case 'status':
                $this->getStatus();
                break;
            case 'info':
                $this->getCoreInfo();
                break;
            case 'auth':
                $this->getAuthStatus();
                break;
            default:
                $this->response->error('Endpoint not found', 404);
        }
    }
    
    private function handlePost($path)
    {
        $parts = explode('/', $path);
        $endpoint = $parts[0] ?? '';
        
        switch ($endpoint) {
            case 'login':
                $this->handleLogin();
                break;
            case 'logout':
                $this->handleLogout();
                break;
            case 'refresh-token':
                $this->handleTokenRefresh();
                break;
            case 'server':
                $action = $parts[1] ?? '';
                if ($action === 'connect') {
                    $this->connectToServer();
                } elseif ($action === 'disconnect') {
                    $this->disconnectFromServer();
                } else {
                    $this->response->error('Invalid server action', 400);
                }
                break;
            case 'search':
                $this->performSearch();
                break;
            case 'download':
                $this->startDownload();
                break;
            default:
                $this->response->error('Endpoint not found', 404);
        }
    }
    
    // Server endpoints
    private function getServers()
    {
        try {
            // Get servers from the original Server class
            $servers = $this->server->getServerList();
            $statistics = $this->server->getStatistics();
            
            $this->response->success([
                'servers' => $servers,
                'total' => count($servers),
                'statistics' => $statistics,
                'timestamp' => time()
            ]);
            
        } catch (Exception $e) {
            $this->response->error('Failed to fetch servers: ' . $e->getMessage());
        }
    }
    
    private function getServerInfo($serverId)
    {
        if (!$serverId) {
            $this->response->error('Server ID required', 400);
            return;
        }
        
        try {
            // Get server info from the original Server class
            $serverInfo = $this->server->serverinfo($serverId);
            
            if (!$serverInfo) {
                $this->response->notFound('Server not found');
                return;
            }
            
            $netstats = $this->server->netstats();
            $isConnected = ($netstats['connectedwith'] == $serverId);
            
            $server = [
                'id' => (int)$serverId,
                'name' => $serverInfo['NAME'] ?? 'Unbekannt',
                'address' => $serverInfo['HOST'] ?? '',
                'port' => (int)($serverInfo['PORT'] ?? 9851),
                'users' => (int)($serverInfo['USERS'] ?? 0),
                'files' => (int)($serverInfo['FILES'] ?? 0),
                'ping' => (int)($serverInfo['PING'] ?? 999),
                'version' => $serverInfo['VERSION'] ?? 'Unknown',
                'location' => $serverInfo['LOCATION'] ?? 'Unknown',
                'status' => $this->server->getServerStatus($serverInfo, $isConnected),
                'connected' => $isConnected,
                'uptime' => $isConnected ? $netstats['timeconnected'] : 0,
                'lastSeen' => date('Y-m-d H:i:s')
            ];
            
            $this->response->success($server);
            
        } catch (Exception $e) {
            $this->response->error('Failed to fetch server info: ' . $e->getMessage());
        }
    }
    
    // Downloads endpoint
    private function getDownloads()
    {
        try {
            // Demo download data
            $downloads = [
                [
                    'id' => 1,
                    'filename' => 'Ubuntu-22.04.3-desktop-amd64.iso',
                    'size' => 4700372992,
                    'loaded' => 2350186496,
                    'status' => 'downloading',
                    'speed' => 1572864,
                    'sources' => 5,
                    'priority' => 150,
                    'progress' => 50.0,
                    'eta' => '25min 30s',
                    'user' => 'LinuxFan2023'
                ],
                [
                    'id' => 2,
                    'filename' => 'Movie.Collection.2023.1080p.BluRay.x264.mkv',
                    'size' => 8589934592,
                    'loaded' => 6871947674,
                    'status' => 'downloading',
                    'speed' => 2097152,
                    'sources' => 3,
                    'priority' => 200,
                    'progress' => 80.0,
                    'eta' => '15min 45s',
                    'user' => 'MovieCollector'
                ]
            ];
            
            $this->response->success([
                'downloads' => $downloads,
                'total' => count($downloads),
                'timestamp' => time()
            ]);
            
        } catch (Exception $e) {
            $this->response->error('Failed to fetch downloads: ' . $e->getMessage());
        }
    }
    
    // Uploads endpoint
    private function getUploads()
    {
        try {
            // Demo upload data
            $uploads = [
                [
                    'id' => 1,
                    'filename' => 'Ubuntu-22.04.3-desktop-amd64.iso',
                    'size' => 4700372992,
                    'uploaded' => 2350186496,
                    'user' => 'LinuxFan2023',
                    'status' => 'uploading',
                    'speed' => 1572864,
                    'priority' => 150,
                    'progress' => 50.0,
                    'queue_position' => 0
                ],
                [
                    'id' => 2,
                    'filename' => 'Adobe.Creative.Suite.2023.zip',
                    'size' => 3221225472,
                    'uploaded' => 966367641,
                    'user' => 'DesignPro',
                    'status' => 'uploading',
                    'speed' => 819200,
                    'priority' => 120,
                    'progress' => 30.0,
                    'queue_position' => 0
                ]
            ];
            
            $this->response->success([
                'uploads' => $uploads,
                'total' => count($uploads),
                'timestamp' => time()
            ]);
            
        } catch (Exception $e) {
            $this->response->error('Failed to fetch uploads: ' . $e->getMessage());
        }
    }
    
    // Status endpoint
    private function getStatus()
    {
        try {
            // Get real status from Server class
            $connectionStatus = $this->server->getConnectionStatus();
            $statistics = $this->server->getStatistics();
            $coreInfo = $this->server->info();
            
            $status = [
                'core' => [
                    'version' => $coreInfo['VERSION'] ?? '0.30.108',
                    'system' => $coreInfo['SYSTEM'] ?? 'Unknown',
                    'uptime' => $coreInfo['UPTIME'] ?? 0
                ],
                'network' => [
                    'connected' => $connectionStatus['connected'],
                    'server_name' => $connectionStatus['server_name'],
                    'users' => $connectionStatus['users'],
                    'files' => $connectionStatus['files'],
                    'filesize' => $connectionStatus['filesize'],
                    'uptime' => $connectionStatus['uptime'],
                    'firewalled' => $connectionStatus['firewalled'],
                    'welcome_message' => $connectionStatus['welcome_message']
                ],
                'statistics' => $statistics,
                'timestamp' => time()
            ];
            
            $this->response->success($status);
            
        } catch (Exception $e) {
            $this->response->error('Failed to fetch status: ' . $e->getMessage());
        }
    }
    
    // Authentication endpoints
    private function handleLogin()
    {
        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->response->error('Invalid JSON input', 400);
                return;
            }
            
            $host = $input['host'] ?? '';
            $password = $input['password'] ?? '';
            
            // Validate input
            if (empty($host) || empty($password)) {
                $this->response->error('Host and password are required', 400);
                return;
            }
            
            // Ensure host has http:// prefix
            if (!preg_match('/^https?:\/\//', $host)) {
                $host = 'http://' . $host;
            }
            
            // Test connection to AppleJuice Core
            $connectionTest = $this->core->testConnection($host, $password);
            
            if ($connectionTest['success']) {
                // Save credentials in session
                $_SESSION['core_host'] = $host;
                $_SESSION['core_pass'] = $password;
                $_SESSION['authenticated'] = true;
                $_SESSION['login_time'] = time();
                
                // Get core information for response
                $coreInfo = [
                    'version' => $connectionTest['version'] ?? 'Unknown',
                    'system' => $connectionTest['system'] ?? 'Unknown'
                ];
                
                $this->response->success([
                    'message' => 'Login successful',
                    'host' => $host,
                    'core_info' => $coreInfo,
                    'session_id' => session_id(),
                    'timestamp' => time()
                ], 200);
                
            } else {
                // Clear any existing session data
                $this->clearSession();
                
                $errorMessage = $connectionTest['error'] ?? 'Connection failed';
                
                // Determine appropriate HTTP status code
                $statusCode = 401; // Unauthorized by default
                if (strpos($errorMessage, 'timeout') !== false) {
                    $statusCode = 408; // Request Timeout
                } elseif (strpos($errorMessage, 'connection') !== false) {
                    $statusCode = 503; // Service Unavailable
                }
                
                $this->response->error([
                    'message' => 'Authentication failed',
                    'details' => $errorMessage,
                    'host' => $host
                ], $statusCode);
            }
            
        } catch (Exception $e) {
            $this->clearSession();
            $this->response->error([
                'message' => 'Login error',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    private function handleLogout()
    {
        try {
            $this->clearSession();
            
            $this->response->success([
                'message' => 'Logout successful',
                'timestamp' => time()
            ], 200);
            
        } catch (Exception $e) {
            $this->response->error([
                'message' => 'Logout error',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    private function clearSession()
    {
        unset($_SESSION['core_host']);
        unset($_SESSION['core_pass']);
        unset($_SESSION['authenticated']);
        unset($_SESSION['login_time']);
    }
    
    private function getAuthStatus()
    {
        try {
            $isAuthenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
            $host = $_SESSION['core_host'] ?? null;
            $loginTime = $_SESSION['login_time'] ?? null;
            
            if ($isAuthenticated && $host) {
                // Test if connection is still valid
                $connectionTest = $this->core->testConnection();
                
                if ($connectionTest['success']) {
                    $this->response->success([
                        'authenticated' => true,
                        'host' => $host,
                        'login_time' => $loginTime,
                        'session_duration' => $loginTime ? (time() - $loginTime) : 0,
                        'core_info' => [
                            'version' => $connectionTest['version'] ?? 'Unknown',
                            'system' => $connectionTest['system'] ?? 'Unknown'
                        ],
                        'timestamp' => time()
                    ], 200);
                } else {
                    // Connection lost, clear session
                    $this->clearSession();
                    $this->response->success([
                        'authenticated' => false,
                        'message' => 'Connection to core lost',
                        'timestamp' => time()
                    ], 200);
                }
            } else {
                $this->response->success([
                    'authenticated' => false,
                    'message' => 'Not authenticated',
                    'timestamp' => time()
                ], 200);
            }
            
        } catch (Exception $e) {
            $this->response->error([
                'message' => 'Auth status check failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    // Core info endpoint
    private function getCoreInfo()
    {
        try {
            $coreInfo = [
                'VERSION' => '0.30.108',
                'SYSTEM' => 'Windows 10 x64'
            ];
            
            $this->response->success($coreInfo);
        } catch (Exception $e) {
            $this->response->error('Failed to fetch core info: ' . $e->getMessage());
        }
    }
    
    // Server actions
    private function connectToServer()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $serverId = $input['server_id'] ?? null;
        
        if (!$serverId) {
            $this->response->error('Server ID required', 400);
            return;
        }
        
        try {
            // Use the original Server class to connect
            $result = $this->server->connectToServer($serverId);
            
            if ($result['success']) {
                $this->response->success([
                    'message' => $result['message'],
                    'server_id' => $serverId,
                    'timestamp' => time()
                ]);
            } else {
                $this->response->error($result['message'], 500);
            }
            
        } catch (Exception $e) {
            $this->response->error('Failed to connect to server: ' . $e->getMessage());
        }
    }
    
    private function disconnectFromServer()
    {
        try {
            // Use the original Server class to disconnect
            $result = $this->server->disconnectFromServer();
            
            if ($result['success']) {
                $this->response->success([
                    'message' => $result['message'],
                    'timestamp' => time()
                ]);
            } else {
                $this->response->error($result['message'], 500);
            }
            
        } catch (Exception $e) {
            $this->response->error('Failed to disconnect from server: ' . $e->getMessage());
        }
    }
    
    // Search endpoints
    private function getSearchResults()
    {
        $query = $_GET['q'] ?? '';
        
        if (empty($query)) {
            $this->response->error('Search query required', 400);
            return;
        }
        
        try {
            // Demo search results
            $results = [
                [
                    'id' => 1,
                    'filename' => 'Ubuntu-22.04.3-desktop-amd64.iso',
                    'size' => 4700372992,
                    'user' => 'LinuxFan2023',
                    'sources' => 5,
                    'type' => 'iso',
                    'hash' => 'a1b2c3d4e5f6'
                ],
                [
                    'id' => 2,
                    'filename' => 'Movie.Collection.2023.1080p.BluRay.x264.mkv',
                    'size' => 8589934592,
                    'user' => 'MovieCollector',
                    'sources' => 3,
                    'type' => 'video',
                    'hash' => 'f6e5d4c3b2a1'
                ]
            ];
            
            $this->response->success([
                'results' => $results,
                'query' => $query,
                'total' => count($results),
                'timestamp' => time()
            ]);
            
        } catch (Exception $e) {
            $this->response->error('Search failed: ' . $e->getMessage());
        }
    }
    
    private function performSearch()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $query = $input['query'] ?? '';
        
        if (empty($query)) {
            $this->response->error('Search query required', 400);
            return;
        }
        
        try {
            $this->response->success([
                'message' => 'Search initiated successfully',
                'query' => $query,
                'timestamp' => time()
            ]);
            
        } catch (Exception $e) {
            $this->response->error('Failed to start search: ' . $e->getMessage());
        }
    }
    
    // Download actions
    private function startDownload()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $fileId = $input['file_id'] ?? null;
        
        if (!$fileId) {
            $this->response->error('File ID required', 400);
            return;
        }
        
        try {
            $this->response->success([
                'message' => 'Download started successfully',
                'file_id' => $fileId,
                'timestamp' => time()
            ]);
            
        } catch (Exception $e) {
            $this->response->error('Failed to start download: ' . $e->getMessage());
        }
    }
    
    // Token refresh endpoint
    private function handleTokenRefresh()
    {
        try {
            $token = $this->tokenManager->extractTokenFromHeader();
            
            if (!$token) {
                $this->response->error([
                    'message' => 'No token provided',
                    'details' => 'Authorization header with Bearer token required'
                ], 401);
                return;
            }
            
            $newToken = $this->tokenManager->refreshToken($token);
            
            if ($newToken) {
                $this->securityLogger->logTokenEvent('refreshed', 'token_refreshed');
                
                $this->response->success([
                    'message' => 'Token refreshed successfully',
                    'token' => $newToken,
                    'expires_in' => $this->tokenManager->getTokenExpiry(),
                    'timestamp' => time()
                ], 200);
            } else {
                $this->securityLogger->logTokenEvent('refresh_failed', 'invalid_token');
                
                $this->response->error([
                    'message' => 'Token refresh failed',
                    'details' => 'Invalid or expired token'
                ], 401);
            }
            
        } catch (Exception $e) {
            $this->securityLogger->logSecurityViolation('TOKEN_REFRESH_ERROR', $e->getMessage());
            
            $this->response->error([
                'message' => 'Token refresh error',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    // Enhanced authentication with token support
    private function isAuthenticated()
    {
        // Check session-based authentication
        $sessionAuth = isset($_SESSION['authenticated']) && 
                      $_SESSION['authenticated'] === true &&
                      !empty($_SESSION['core_host']) &&
                      !empty($_SESSION['core_pass']);
        
        // Check token-based authentication
        $token = $this->tokenManager->extractTokenFromHeader();
        $tokenAuth = false;
        
        if ($token) {
            $validation = $this->tokenManager->validateToken($token);
            if ($validation['valid']) {
                $tokenAuth = true;
                $this->securityLogger->logTokenEvent('validated', $validation['payload']['jti'] ?? 'unknown');
            } else {
                $this->securityLogger->logTokenEvent('invalid', 'unknown', ['error' => $validation['error']]);
            }
        }
        
        return $sessionAuth || $tokenAuth;
    }
    
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
}
?>