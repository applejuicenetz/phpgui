<?php
/**
 * Token Manager for JWT-based Authentication
 * Provides secure token generation, validation and management
 */

namespace appleJuiceNETZ\appleJuice;

class TokenManager
{
    private $secretKey;
    private $algorithm = 'HS256';
    private $tokenExpiry = 3600; // 1 hour default
    
    public function __construct($secretKey = null)
    {
        $this->secretKey = $secretKey ?: $this->generateSecretKey();
    }
    
    /**
     * Generate a secure secret key
     */
    private function generateSecretKey()
    {
        return base64_encode(random_bytes(64));
    }
    
    /**
     * Create JWT token
     */
    public function createToken($payload, $expiryTime = null)
    {
        $header = [
            'typ' => 'JWT',
            'alg' => $this->algorithm
        ];
        
        $now = time();
        $expiry = $expiryTime ?: ($now + $this->tokenExpiry);
        
        $payload = array_merge($payload, [
            'iat' => $now,      // Issued at
            'exp' => $expiry,   // Expiry
            'jti' => $this->generateJti(), // JWT ID
        ]);
        
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        
        $signature = $this->createSignature($headerEncoded . '.' . $payloadEncoded);
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
    }
    
    /**
     * Validate and decode JWT token
     */
    public function validateToken($token)
    {
        try {
            $parts = explode('.', $token);
            
            if (count($parts) !== 3) {
                throw new \Exception('Invalid token format');
            }
            
            [$headerEncoded, $payloadEncoded, $signature] = $parts;
            
            // Verify signature
            $expectedSignature = $this->createSignature($headerEncoded . '.' . $payloadEncoded);
            if (!hash_equals($signature, $expectedSignature)) {
                throw new \Exception('Invalid token signature');
            }
            
            // Decode payload
            $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);
            
            if (!$payload) {
                throw new \Exception('Invalid token payload');
            }
            
            // Check expiry
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                throw new \Exception('Token expired');
            }
            
            return [
                'valid' => true,
                'payload' => $payload
            ];
            
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Create signature for JWT
     */
    private function createSignature($data)
    {
        return $this->base64UrlEncode(hash_hmac('sha256', $data, $this->secretKey, true));
    }
    
    /**
     * Generate unique JWT ID
     */
    private function generateJti()
    {
        return bin2hex(random_bytes(16));
    }
    
    /**
     * Base64 URL encode
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL decode
     */
    private function base64UrlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
    
    /**
     * Set token expiry time
     */
    public function setTokenExpiry($seconds)
    {
        $this->tokenExpiry = $seconds;
    }
    
    /**
     * Get token expiry time
     */
    public function getTokenExpiry()
    {
        return $this->tokenExpiry;
    }
    
    /**
     * Extract token from Authorization header
     */
    public function extractTokenFromHeader()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Refresh token (create new token with extended expiry)
     */
    public function refreshToken($token)
    {
        $validation = $this->validateToken($token);
        
        if (!$validation['valid']) {
            return false;
        }
        
        $payload = $validation['payload'];
        
        // Remove old timing claims
        unset($payload['iat'], $payload['exp'], $payload['jti']);
        
        // Create new token
        return $this->createToken($payload);
    }
}
?>