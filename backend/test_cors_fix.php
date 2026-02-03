<?php
// Test CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    echo json_encode([
        'success' => true,
        'message' => 'CORS test successful',
        'received_data' => $data,
        'method' => $_SERVER['REQUEST_METHOD'],
        'origin' => $_SERVER['HTTP_ORIGIN'] ?? 'not set'
    ]);
} else {
    echo json_encode([
        'success' => true,
        'message' => 'CORS test endpoint is working',
        'method' => $_SERVER['REQUEST_METHOD']
    ]);
}
?>
