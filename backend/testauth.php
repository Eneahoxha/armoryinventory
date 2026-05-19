<?php
require '/app/api/config/Database.php';
require '/app/api/controllers/AuthController.php';

$db = new Database();
$conn = $db->connect();

echo "Database: OK\n";
echo "AuthController: OK\n";

$auth_controller = new AuthController($conn);
echo "AuthController instantiated: OK\n";

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/api/auth/login';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Simulate POST data
$input = json_encode([
    'email' => 'admin@armory.local',
    'password' => 'admin123456'
]);

// Mock php://input
$response = $auth_controller->login();
echo json_encode($response, JSON_PRETTY_PRINT);
?>
