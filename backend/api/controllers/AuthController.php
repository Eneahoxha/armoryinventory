<?php
// backend/api/controllers/AuthController.php

class AuthController {
    private $db;
    private $user_model;
    private $auth_middleware;

    public function __construct($db) {
        $this->db = $db;
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../middleware/Auth.php';
        
        $this->user_model = new User($db);
        $this->auth_middleware = new Auth();
    }

    /**
     * Login endpoint
     * POST /api/auth/login
     * Body: { "email": "user@example.com", "password": "password" }
     */
    public function login() {
        error_log("login() called");
        error_log("CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'NOT SET'));
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        
        // Read POST data
        $raw_data = file_get_contents('php://input');
        error_log("Raw data length: " . strlen($raw_data));
        error_log("Raw data: " . substr($raw_data, 0, 100));
        
        if ($_SERVER['CONTENT_TYPE'] === 'application/json' || strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') === 0) {
            $data = json_decode($raw_data, true);
        } else {
            $data = $_POST;
        }
        
        error_log("Parsed data: " . json_encode($data));
        
        if (!$data || !isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            return ['error' => 'Email and password are required'];
        }
        
        try {
            error_log("About to find user");
            $user = $this->user_model->findByEmail($data['email']);
            error_log("User found: " . json_encode($user));
            
            if (!$user) {
                error_log("User not found for email: " . $data['email']);
                http_response_code(401);
                return ['error' => 'Invalid email or password'];
            }
            
            if ($user['stato'] !== 'Attivo') {
                error_log("User inactive");
                http_response_code(403);
                return ['error' => 'User account is inactive'];
            }
            
            error_log("About to verify password");
            if (!$this->user_model->verifyPassword($data['password'], $user['password_hash'])) {
                error_log("Password verification failed");
                http_response_code(401);
                return ['error' => 'Invalid email or password'];
            }
            
            error_log("About to generate token");
            $token = $this->auth_middleware->generateToken(
                $user['user_id'],
                $user['email'],
                $user['ruolo']
            );
            error_log("Token generated: " . substr($token, 0, 20) . "...");
            
            http_response_code(200);
            return [
                'success' => true,
                'token' => $token,
                'user' => [
                    'user_id' => $user['user_id'],
                    'email' => $user['email'],
                    'nome' => $user['nome'],
                    'cognome' => $user['cognome'],
                    'ruolo' => $user['ruolo']
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Exception in login: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            http_response_code(500);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Validate token endpoint
     * POST /api/auth/validate
     * Headers: Authorization: Bearer <token>
     */
    public function validate() {
        try {
            $payload = Auth::verify();
            
            http_response_code(200);
            return [
                'valid' => true,
                'user' => [
                    'user_id' => $payload['user_id'],
                    'email' => $payload['email'],
                    'ruolo' => $payload['ruolo']
                ]
            ];
            
        } catch (Exception $e) {
            http_response_code(401);
            return ['error' => 'Invalid token'];
        }
    }

    /**
     * Register endpoint (admin only)
     * POST /api/auth/register
     */
    public function register() {
        try {
            $payload = Auth::verify('admin');
            
            $data = json_decode(file_get_contents("php://input"), true);
            
            $required = ['email', 'password', 'nome', 'cognome', 'ruolo'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    http_response_code(400);
                    return ['error' => "$field is required"];
                }
            }
            
            $valid_roles = ['capo_sm', 'armaiolo', 'ufficiale', 'operatore'];
            if (!in_array($data['ruolo'], $valid_roles)) {
                http_response_code(400);
                return ['error' => 'Invalid role'];
            }
            
            $user = $this->user_model->create(
                $data['email'],
                $data['password'],
                $data['nome'],
                $data['cognome'],
                $data['ruolo']
            );
            
            http_response_code(201);
            return [
                'success' => true,
                'user' => $user
            ];
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'already registered') !== false) {
                http_response_code(409);
            } else {
                http_response_code(500);
            }
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Logout endpoint
     * POST /api/auth/logout
     */
    public function logout() {
        try {
            Auth::verify();
            
            http_response_code(200);
            return ['success' => true, 'message' => 'Logged out successfully'];
            
        } catch (Exception $e) {
            http_response_code(401);
            return ['error' => 'Unauthorized'];
        }
    }
}
?>
