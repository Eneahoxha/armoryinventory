<?php
// Detailed test for POST request
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo json_encode([
    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'N/A',
    'raw_input' => file_get_contents('php://input'),
    'server_keys' => array_keys($_SERVER)
]);
?>
