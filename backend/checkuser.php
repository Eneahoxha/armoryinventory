<?php
require '/app/api/config/Database.php';
$db = new Database();
$conn = $db->connect();
$stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute(['admin@armory.local']);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($result, JSON_PRETTY_PRINT);
?>
