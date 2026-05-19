<?php
require '/app/api/config/Database.php';
try {
    $db = new Database();
    $conn = $db->connect();
    echo 'Database connected successfully!';
    echo "\n\nTest query:\n";
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "Users table count: " . $result['count'];
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
