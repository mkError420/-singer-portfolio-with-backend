<?php
// Test CORS Headers
echo "<h1>CORS Headers Test</h1>";

try {
    // Test albums API
    $albumsUrl = 'http://localhost/madam-portfolio/backend/api/albums.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json',
            'ignore_errors' => true
        ]
    ]);
    
    echo "<h2>Testing Albums API</h2>";
    $response = file_get_contents($albumsUrl, false, $context);
    
    // Get response headers
    if (isset($http_response_header)) {
        echo "<h3>Response Headers:</h3>";
        echo "<pre>";
        foreach ($http_response_header as $header) {
            echo htmlspecialchars($header) . "\n";
        }
        echo "</pre>";
    }
    
    if ($response) {
        echo "<h3>Response Body:</h3>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
        
        $data = json_decode($response, true);
        if (is_array($data)) {
            echo "<p style='color: green;'>✅ Albums API working correctly</p>";
            echo "<p>Returned " . count($data) . " albums</p>";
        } else {
            echo "<p style='color: red;'>❌ Albums API returned invalid data</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Albums API not responding</p>";
    }
    
    echo "<h2>Testing CORS Preflight (OPTIONS)</h2>";
    
    $optionsContext = stream_context_create([
        'http' => [
            'method' => 'OPTIONS',
            'header' => 'Content-Type: application/json',
            'ignore_errors' => true
        ]
    ]);
    
    $optionsResponse = file_get_contents($albumsUrl, false, $optionsContext);
    
    if (isset($http_response_header)) {
        echo "<h3>OPTIONS Response Headers:</h3>";
        echo "<pre>";
        foreach ($http_response_header as $header) {
            echo htmlspecialchars($header) . "\n";
        }
        echo "</pre>";
    }
    
    echo "<h2>Frontend Test Instructions</h2>";
    echo "<p><strong>To test CORS in browser:</strong></p>";
    echo "<ol>";
    echo "<li>Open browser console (F12)</li>";
    echo "<li>Run this code:</li>";
    echo "<pre>";
    echo "fetch('http://localhost/madam-portfolio/backend/api/albums.php')";
    echo "  .then(response => response.json())";
    echo "  .then(data => console.log('Success:', data))";
    echo "  .catch(error => console.error('Error:', error));";
    echo "</pre>";
    echo "</ol>";
    
    echo "<h2>CORS Headers Added</h2>";
    echo "<p>The following headers have been added to all API endpoints:</p>";
    echo "<ul>";
    echo "<li><code>Access-Control-Allow-Origin: *</code></li>";
    echo "<li><code>Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS</code></li>";
    echo "<li><code>Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With</code></li>";
    echo "</ul>";
    
    echo "<h2>Next Steps</h2>";
    echo "<ol>";
    echo "<li>Refresh your React application</li>";
    echo "<li>Check browser console for CORS errors</li>";
    echo "<li>Music page should now load albums from backend</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
