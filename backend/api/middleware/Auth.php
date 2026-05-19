<?php
// backend/api/middleware/Auth.php

class Auth {
    private $secret_key = 'your-secret-key-change-this';
    
    /**
     * Generate JWT token
     */
    public function generateToken($user_id, $email, $ruolo) {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'user_id' => $user_id,
            'email' => $email,
            'ruolo' => $ruolo,
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60) // 24 hours
        ];
        
        $header_encoded = base64_encode(json_encode($header));
        $payload_encoded = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', $header_encoded . '.' . $payload_encoded, $this->secret_key, true);
        $signature_encoded = base64_encode($signature);
        
        return $header_encoded . '.' . $payload_encoded . '.' . $signature_encoded;
    }

    /**
     * Verify and decode JWT token
     */
    public function verifyToken($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return null;
        }
        
        list($header_encoded, $payload_encoded, $signature_encoded) = $parts;
        
        // Verify signature
        $signature = hash_hmac('sha256', $header_encoded . '.' . $payload_encoded, $this->secret_key, true);
        $signature_expected = base64_encode($signature);
        
        if ($signature_encoded !== $signature_expected) {
            return null;
        }
        
        // Decode payload
        $payload = json_decode(base64_decode($payload_encoded), true);
        
        // Check expiration
        if ($payload['exp'] < time()) {
            return null;
        }
        
        return $payload;
    }

    /**
     * Get token from Authorization header
     */
    public function getTokenFromHeader() {
        $headers = getallheaders();
        
        if (isset($headers['Authorization'])) {
            $auth_header = $headers['Authorization'];
            
            if (strpos($auth_header, 'Bearer ') === 0) {
                return substr($auth_header, 7);
            }
        }
        
        return null;
    }

    /**
     * Check if user has required role
     */
    public function hasRole($payload, $required_roles) {
        if (!is_array($required_roles)) {
            $required_roles = [$required_roles];
        }
        
        return in_array($payload['ruolo'], $required_roles);
    }

    /**
     * Middleware to verify token and set user context
     */
    public static function verify($required_roles = []) {
        $auth = new self();
        $token = $auth->getTokenFromHeader();
        
        if (!$token) {
            http_response_code(401);
            die(json_encode(['error' => 'No authorization token provided']));
        }
        
        $payload = $auth->verifyToken($token);
        
        if (!$payload) {
            http_response_code(401);
            die(json_encode(['error' => 'Invalid or expired token']));
        }
        
        if (!empty($required_roles) && !$auth->hasRole($payload, $required_roles)) {
            http_response_code(403);
            die(json_encode(['error' => 'Insufficient permissions']));
        }
        
        return $payload;
    }
}
?>
