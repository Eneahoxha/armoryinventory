<?php
// Test PHP setup
echo "PHP Start\n";

try {
    require_once '/app/api/config/Database.php';
    echo "Database loaded\n";
    
    require_once '/app/api/controllers/AuthController.php';
    echo "AuthController loaded\n";
    
    echo "All files loaded successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
