<?php
echo "START\n";
header('Content-Type: application/json');

// Output error info
ini_set('display_errors', 1);
error_reporting(E_ALL);

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo json_encode(['error' => $errstr, 'file' => $errfile, 'line' => $errline]);
    exit;
});

set_exception_handler(function($e) {
    echo json_encode(['exception' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
    exit;
});

try {
    echo "Before require\n";
    require_once __DIR__ . '/api/config/Database.php';
    echo "DB config loaded\n";
    
    require_once __DIR__ . '/api/controllers/AuthController.php';
    echo "AuthController loaded\n";
    
    $db = new Database();
    echo "DB instantiated\n";
    
    $conn = $db->connect();
    echo "DB connected\n";
    
    $auth_controller = new AuthController($conn);
    echo "AuthController instantiated\n";
    
    echo json_encode(['success' => true, 'message' => 'All systems ready']);
    
} catch (Throwable $e) {
    echo json_encode([
        'exception' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
