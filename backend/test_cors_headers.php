<?php
// Test CORS Headers
echo "<h1>CORS Headers Test</h1>";

echo "<h2>Current Headers:</h2>";
$headers = headers_list();
foreach ($headers as $header) {
    echo "<p>" . htmlspecialchars($header) . "</p>";
}

echo "<h2>Setting CORS Headers:</h2>";

// Remove all previous headers
header_remove();

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

echo "<h2>Headers After Setting:</h2>";
$headers = headers_list();
foreach ($headers as $header) {
    echo "<p>" . htmlspecialchars($header) . "</p>";
}

echo "<h2>Test Response:</h2>";
echo json_encode([
    "message" => "CORS test successful",
    "timestamp" => date("Y-m-d H:i:s"),
    "headers_sent" => headers_sent()
]);
?>
