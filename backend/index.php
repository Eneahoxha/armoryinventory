<?php
// backend/index.php - API Router

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Set timezone and error reporting
date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Load configuration
require_once __DIR__ . '/api/config/Database.php';
require_once __DIR__ . '/api/controllers/AuthController.php';

// Parse the request
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove /api/ prefix from path
$path = str_replace('/api/', '', $request_uri);
$path_parts = explode('/', trim($path, '/'));

// Simple routing
$endpoint = $path_parts[0] ?? '';
$action = $path_parts[1] ?? '';
$id = $path_parts[2] ?? null;

// Response helper
function json_response($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit();
}

// Initialize database connection
try {
    $db = new Database();
    $conn = $db->connect();
} catch (Exception $e) {
    json_response(['error' => 'Database Error: ' . $e->getMessage()], 500);
    exit;
}

// Debug endpoint for testing
if ($endpoint === 'debug') {
    $debug_info = [
        'ok' => true,
        'endpoint' => $endpoint,
        'action' => $action
    ];
    
    // Test database
    try {
        $stmt = $conn->prepare("SELECT 1");
        $stmt->execute();
        $debug_info['database'] = 'connected';
    } catch (Exception $e) {
        $debug_info['database_error'] = $e->getMessage();
    }
    
    // Test AuthController
    try {
        require_once __DIR__ . '/api/controllers/AuthController.php';
        $auth_controller = new AuthController($conn);
        $debug_info['auth_controller'] = 'loaded';
    } catch (Exception $e) {
        $debug_info['auth_error'] = $e->getMessage();
    }
    
    json_response($debug_info, 200);
    exit;
}

// API Routes
try {
    switch ($endpoint) {
        case 'auth':
            $auth_controller = new AuthController($conn);
            
            switch ($action) {
                case 'login':
                    if ($request_method !== 'POST') {
                        json_response(['error' => 'Method not allowed'], 405);
                    }
                    $response = $auth_controller->login();
                    json_response($response);
                    break;
                
                case 'validate':
                    if ($request_method !== 'POST') {
                        json_response(['error' => 'Method not allowed'], 405);
                    }
                    $response = $auth_controller->validate();
                    json_response($response);
                    break;
                
                case 'register':
                    if ($request_method !== 'POST') {
                        json_response(['error' => 'Method not allowed'], 405);
                    }
                    $response = $auth_controller->register();
                    json_response($response);
                    break;
                
                case 'logout':
                    if ($request_method !== 'POST') {
                        json_response(['error' => 'Method not allowed'], 405);
                    }
                    $response = $auth_controller->logout();
                    json_response($response);
                    break;
                
                default:
                    json_response(['error' => 'Auth action not found'], 404);
            }
            break;
        
        case 'personale':
            json_response(['message' => 'Personale API endpoint', 'method' => $request_method]);
            break;
        
        case 'squadre':
            json_response(['message' => 'Squadre API endpoint', 'method' => $request_method]);
            break;
        
        case 'equipaggiamento':
            json_response(['message' => 'Equipaggiamento API endpoint', 'method' => $request_method]);
            break;
        
        case 'dotazioni':
            json_response(['message' => 'Dotazioni API endpoint', 'method' => $request_method]);
            break;
        
        default:
            json_response(['error' => 'Endpoint not found'], 404);
    }
} catch (Exception $e) {
    json_response(['error' => 'Server Error: ' . $e->getMessage()], 500);
}
?>
