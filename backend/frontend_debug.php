<?php
// Frontend Debug - Check Albums and API
echo "<h1>Frontend Debug - Albums Not Showing</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Check Albums in Database</h2>";
    
    $query = "SELECT a.*, 
                     (SELECT COUNT(*) FROM tracks t WHERE t.album_id = a.id AND t.status = 'active') as track_count
              FROM albums a 
              WHERE a.status = 'active' 
              ORDER BY a.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total albums in database: " . count($albums) . "</p>";
    
    if (count($albums) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Year</th><th>Category</th><th>Tracks</th><th>Cover Image</th><th>Created</th></tr>";
        
        foreach ($albums as $album) {
            echo "<tr>";
            echo "<td>{$album['id']}</td>";
            echo "<td><strong>" . htmlspecialchars($album['title']) . "</strong></td>";
            echo "<td>{$album['year']}</td>";
            echo "<td>" . htmlspecialchars($album['category'] ?: 'No Category') . "</td>";
            echo "<td>{$album['track_count']}</td>";
            echo "<td>" . htmlspecialchars($album['cover_image'] ?: 'No Image') . "</td>";
            echo "<td>{$album['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>Sample Album Details:</h3>";
        echo "<pre>" . htmlspecialchars(json_encode($albums[0], JSON_PRETTY_PRINT)) . "</pre>";
    } else {
        echo "<p style='color: red;'>❌ No albums found in database!</p>";
        echo "<p><a href='admin/albums.php' target='_blank'>Add some albums first</a></p>";
    }
    
    echo "<h2>Step 2: Test Albums API Directly</h2>";
    
    // Test the albums API directly
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET = [];
    
    ob_start();
    include __DIR__ . '/api/albums.php';
    $apiOutput = ob_get_clean();
    
    echo "<h3>API Output:</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 400px; overflow-y: auto;'>" . htmlspecialchars($apiOutput) . "</pre>";
    
    // Check if API returns valid JSON
    $apiData = json_decode($apiOutput, true);
    if ($apiData !== null) {
        echo "<p style='color: green;'>✅ API returns valid JSON</p>";
        echo "<p>API returned " . count($apiData) . " albums</p>";
        
        if (count($apiData) > 0) {
            echo "<h3>First Album from API:</h3>";
            echo "<pre>" . htmlspecialchars(json_encode($apiData[0], JSON_PRETTY_PRINT)) . "</pre>";
        }
    } else {
        echo "<p style='color: red;'>❌ API does not return valid JSON</p>";
        echo "<p>Raw output length: " . strlen($apiOutput) . " characters</p>";
    }
    
    echo "<h2>Step 3: Test API with Tracks</h2>";
    
    if (count($albums) > 0) {
        $firstAlbumId = $albums[0]['id'];
        echo "<p>Testing album with tracks (ID: $firstAlbumId)...</p>";
        
        $_GET['album_id'] = $firstAlbumId;
        $_GET['include_tracks'] = '1';
        
        ob_start();
        include __DIR__ . '/api/albums.php';
        $tracksOutput = ob_get_clean();
        
        echo "<h3>Album with Tracks API Output:</h3>";
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 400px; overflow-y: auto;'>" . htmlspecialchars($tracksOutput) . "</pre>";
        
        $albumWithTracks = json_decode($tracksOutput, true);
        if ($albumWithTracks !== null) {
            echo "<p style='color: green;'>✅ Album with tracks API works</p>";
            
            if (isset($albumWithTracks['tracks'])) {
                echo "<p>Album has " . count($albumWithTracks['tracks']) . " tracks</p>";
                if (count($albumWithTracks['tracks']) > 0) {
                    echo "<h3>First Track:</h3>";
                    echo "<pre>" . htmlspecialchars(json_encode($albumWithTracks['tracks'][0], JSON_PRETTY_PRINT)) . "</pre>";
                }
            }
        } else {
            echo "<p style='color: red;'>❌ Album with tracks API failed</p>";
        }
        
        // Reset GET
        $_GET = [];
    }
    
    echo "<h2>Step 4: CORS Headers Check</h2>";
    
    // Test CORS headers
    $corsTestFile = __DIR__ . '/api/cors_test.php';
    if (file_exists($corsTestFile)) {
        ob_start();
        include $corsTestFile;
        $corsOutput = ob_get_clean();
        
        echo "<h3>CORS Test Output:</h3>";
        echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($corsOutput) . "</pre>";
        
        $corsData = json_decode($corsOutput, true);
        if ($corsData !== null) {
            echo "<p style='color: green;'>✅ CORS test works</p>";
        } else {
            echo "<p style='color: red;'>❌ CORS test failed</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ CORS test file not found</p>";
    }
    
    echo "<h2>Step 5: Frontend URL Test</h2>";
    
    echo "<p>Test these URLs in your browser:</p>";
    echo "<ul>";
    echo "<li><a href='http://localhost/madam-portfolio/backend/api/albums.php' target='_blank'>Albums API</a></li>";
    echo "<li><a href='http://localhost/madam-portfolio/backend/api/cors_test.php' target='_blank'>CORS Test</a></li>";
    if (count($albums) > 0) {
        $firstAlbumId = $albums[0]['id'];
        echo "<li><a href='http://localhost/madam-portfolio/backend/api/albums.php?album_id=$firstAlbumId&include_tracks=1' target='_blank'>Album with Tracks</a></li>";
    }
    echo "</ul>";
    
    echo "<h2>Step 6: Frontend Debugging</h2>";
    
    echo "<p><strong>In your React app (http://localhost:3000/music):</strong></p>";
    echo "<ol>";
    echo "<li>Open browser console (F12)</li>";
    echo "<li>Look for these console logs:</li>";
    echo "<ul>";
    echo "<li>'API Request: GET /albums'</li>";
    echo "<li>'Albums loaded successfully' or error messages</li>";
    echo "<li>Any network errors</li>";
    echo "</ul>";
    echo "<li>Check Network tab for failed requests</li>";
    echo "<li>Look at the actual API responses</li>";
    echo "</ol>";
    
    echo "<h2>Step 7: Create Test Album (if needed)</h2>";
    
    if (count($albums) === 0) {
        echo "<p>No albums found. Creating a test album...</p>";
        
        $testAlbum = [
            'title' => 'Frontend Test Album',
            'year' => date('Y'),
            'category' => 'rock',
            'description' => 'Test album for frontend debugging',
            'cover_image' => 'uploads/test_album.jpg',
            'tracks' => [
                [
                    'title' => 'Test Track 1',
                    'youtube_url' => 'https://youtube.com/watch?v=dQw4w9WgXcQ',
                    'duration' => '3:45',
                    'track_number' => 1
                ]
            ]
        ];
        
        // Simulate POST request
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        
        // Mock the POST data
        $postData = json_encode($testAlbum);
        file_put_contents('php://input', $postData);
        
        ob_start();
        include __DIR__ . '/api/albums.php';
        $createOutput = ob_get_clean();
        
        echo "<h3>Create Album Output:</h3>";
        echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($createOutput) . "</pre>";
        
        // Reset
        $_SERVER['REQUEST_METHOD'] = 'GET';
        unset($_SERVER['CONTENT_TYPE']);
    }
    
    echo "<h2>Troubleshooting Summary</h2>";
    
    if (count($albums) === 0) {
        echo "<p style='color: red;'>❌ No albums in database - Add albums via admin dashboard</p>";
    } elseif ($apiData === null) {
        echo "<p style='color: red;'>❌ API not returning valid JSON - Check PHP errors</p>";
    } else {
        echo "<p style='color: green;'>✅ Backend working - Issue is in frontend or CORS</p>";
        echo "<p>Check browser console for CORS errors and network issues</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
