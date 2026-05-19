<?php
require '/app/api/config/Database.php';

$db = new Database();
$conn = $db->connect();

$password = "admin123456";
$new_hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
$stmt->execute([$new_hash, 'admin@armory.local']);

echo "Password updated successfully!\n";
echo "Hash: " . $new_hash . "\n";

// Verify
$stmt = $conn->prepare("SELECT password_hash FROM users WHERE email = ?");
$stmt->execute(['admin@armory.local']);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Stored hash: " . $result['password_hash'] . "\n";
echo "Verify result: " . (password_verify($password, $result['password_hash']) ? "PASS" : "FAIL") . "\n";
?>
