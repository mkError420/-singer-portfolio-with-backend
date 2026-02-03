<?php
echo "<h1>🔍 Test API Endpoint</h1>";

$apiUrl = 'http://localhost/madam-portfolio/backend/services/youtube_audio_extractor.php';

echo "<h2>API Endpoint Test</h2>";
echo "<p><strong>URL:</strong> <a href='" . htmlspecialchars($apiUrl) . "' target='_blank'>" . htmlspecialchars($apiUrl) . "</a></p>";

// Test with cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true); // Just check if endpoint exists

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "<p style='color: red;'>❌ cURL Error: " . htmlspecialchars($error) . "</p>";
} else {
    echo "<p><strong>HTTP Status:</strong> " . $httpCode . "</p>";
    
    if ($httpCode === 200) {
        echo "<p style='color: green;'>✅ API endpoint is accessible</p>";
    } elseif ($httpCode === 405) {
        echo "<p style='color: orange;'>⚠️ API endpoint exists but method not allowed (expected for GET)</p>";
    } else {
        echo "<p style='color: red;'>❌ API endpoint returned status: " . $httpCode . "</p>";
    }
}

// Test POST request
echo "<h2>POST Request Test</h2>";

$postData = json_encode([
    'action' => 'extract_single',
    'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'track_id' => 1,
    'album_id' => 1
]);

echo "<p><strong>POST Data:</strong></p>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo htmlspecialchars($postData);
echo "</pre>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postData)
]);

$postResponse = curl_exec($ch);
$postHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$postError = curl_error($ch);
curl_close($ch);

if ($postError) {
    echo "<p style='color: red;'>❌ POST cURL Error: " . htmlspecialchars($postError) . "</p>";
} else {
    echo "<p><strong>POST HTTP Status:</strong> " . $postHttpCode . "</p>";
    echo "<p><strong>POST Response:</strong></p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    echo htmlspecialchars($postResponse);
    echo "</pre>";
    
    if ($postHttpCode === 200) {
        $responseData = json_decode($postResponse, true);
        if ($responseData && isset($responseData['success'])) {
            if ($responseData['success']) {
                echo "<p style='color: green;'>✅ POST request successful</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ POST request failed: " . htmlspecialchars($responseData['message']) . "</p>";
            }
        }
    }
}

echo "<h2>Troubleshooting Steps</h2>";
echo "<ol>";
echo "<li><strong>Check if Apache is running:</strong> <a href='http://localhost' target='_blank'>http://localhost</a></li>";
echo "<li><strong>Check if file exists:</strong> <a href='" . htmlspecialchars($apiUrl) . "' target='_blank'>" . htmlspecialchars($apiUrl) . "</a></li>";
echo "<li><strong>Check CORS headers:</strong> The API should have CORS headers set</li>";
echo "<li><strong>Check file permissions:</strong> The PHP file should be readable</li>";
echo "</ol>";

echo "<h2>React App Connection Test</h2>";
echo "<p>From React app (port 3000), try accessing:</p>";
echo "<p><a href='http://localhost:3000' target='_blank'>http://localhost:3000</a> → Open browser console and check network tab</p>";
echo "<p>Look for requests to: <code>youtube_audio_extractor.php</code></p>";
?>
