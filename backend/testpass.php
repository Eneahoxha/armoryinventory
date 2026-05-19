<?php
// Test password verification
$stored_hash = "$2y$10$N9qo8uLOickgx2ZMRZoMye4QJq6H9Y8K3y7k7DxuXYV8VjXWp5aQa";
$test_password = "admin123456";

echo "Stored hash: " . $stored_hash . "\n";
echo "Test password: " . $test_password . "\n";
echo "Verification result: " . (password_verify($test_password, $stored_hash) ? "MATCH" : "NO MATCH") . "\n";

// Try hashing it ourselves
$new_hash = password_hash($test_password, PASSWORD_BCRYPT);
echo "\nNew hash would be: " . $new_hash . "\n";
echo "New hash verification: " . (password_verify($test_password, $new_hash) ? "MATCH" : "NO MATCH") . "\n";
?>
