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
$db = new Database();
$conn = $db->connect();

// API Routes (placeholder)
switch ($endpoint) {
    case 'personale':
        json_response(['message' => 'Personale API endpoint', 'method' => $request_method]);
        break;
    
    case 'squadre':
        json_response(['message' => 'Squadre API endpoint', 'method' => $request_method]);
        break;
    
    case 'equipaggiamento':
        json_response(['message' => 'Equipaggiamento API endpoint', 'method' => $request_method]);
        break;
    
    default:
        json_response(['error' => 'Endpoint not found'], 404);
}
?>
