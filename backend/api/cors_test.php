<?php
// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

echo json_encode([
    "message" => "CORS test successful",
    "timestamp" => date("Y-m-d H:i:s"),
    "method" => $_SERVER["REQUEST_METHOD"],
    "origin" => $_SERVER["HTTP_ORIGIN"] ?? "Not set"
]);
?>
