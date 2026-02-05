<?php
// Test API endpoints
require_once 'backend/config/config.php';

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TESTING API ENDPOINTS ===\n\n";
    
    // Test POST endpoint
    echo "1. Testing POST (create video):\n";
    $postData = json_encode([
        'title' => 'Test Video from API',
        'description' => 'This is a test video',
        'video_url' => 'https://www.youtube.com/watch?v=test123',
        'category' => 'music'
    ]);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $postData
        ]
    ]);
    
    $response = file_get_contents('http://localhost/madam-portfolio/backend/api/videos.php', false, $context);
    echo "Response: " . $response . "\n\n";
    
    // Test GET endpoint
    echo "2. Testing GET (all videos):\n";
    $response = file_get_contents('http://localhost/madam-portfolio/backend/api/videos.php');
    echo "Response: " . $response . "\n\n";
    
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch(Exception $e) {
    echo "General error: " . $e->getMessage() . "\n";
}
?>
