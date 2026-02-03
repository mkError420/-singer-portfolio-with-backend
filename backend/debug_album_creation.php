<?php
// Debug Album Creation and Display
echo "<h1>Album Creation & Display Debug</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Check Albums Table</h2>";
    
    // Check current albums
    $query = "SELECT * FROM albums ORDER BY id DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total albums: " . count($albums) . "</p>";
    
    if (count($albums) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Year</th><th>Category</th><th>Status</th><th>Cover Image</th><th>Created</th></tr>";
        foreach ($albums as $album) {
            echo "<tr>";
            echo "<td>{$album['id']}</td>";
            echo "<td>" . htmlspecialchars($album['title']) . "</td>";
            echo "<td>{$album['year']}</td>";
            echo "<td>{$album['category']}</td>";
            echo "<td>{$album['status']}</td>";
            echo "<td>" . htmlspecialchars($album['cover_image'] ?: 'NULL') . "</td>";
            echo "<td>{$album['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No albums found</p>";
    }
    
    echo "<h2>Step 2: Test Albums API</h2>";
    
    // Test the API
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/madam-portfolio/backend/api/albums.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = file_get_contents($apiUrl, false, $context);
    
    if ($response) {
        echo "<p>✅ Albums API responding</p>";
        $apiData = json_decode($response, true);
        
        if (is_array($apiData)) {
            echo "<p>✅ API returned " . count($apiData) . " albums</p>";
            
            if (count($apiData) > 0) {
                echo "<h3>Sample API Response:</h3>";
                echo "<pre>" . htmlspecialchars(json_encode($apiData[0], JSON_PRETTY_PRINT)) . "</pre>";
            }
        } else {
            echo "<p>❌ API returned invalid data</p>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    } else {
        echo "<p>❌ Albums API not responding</p>";
    }
    
    echo "<h2>Step 3: Test Album Creation</h2>";
    
    // Test creating a new album
    $testAlbum = [
        'title' => 'Test Album ' . date('His'),
        'year' => date('Y'),
        'category' => 'album',
        'description' => 'This is a test album created at ' . date('Y-m-d H:i:s'),
        'cover_image' => 'uploads/test_album.jpg',
        'tracks' => [
            [
                'title' => 'Test Track 1',
                'youtube_url' => 'https://youtube.com/watch?v=test1',
                'duration' => '3:45',
                'track_number' => 1
            ],
            [
                'title' => 'Test Track 2',
                'youtube_url' => 'https://youtube.com/watch?v=test2',
                'duration' => '4:20',
                'track_number' => 2
            ]
        ]
    ];
    
    $postData = json_encode($testAlbum);
    $postContext = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $postData
        ]
    ]);
    
    $postResponse = file_get_contents($apiUrl, false, $postContext);
    
    if ($postResponse) {
        echo "<p>✅ Album creation API responding</p>";
        $postResult = json_decode($postResponse, true);
        echo "<pre>" . htmlspecialchars(json_encode($postResult, JSON_PRETTY_PRINT)) . "</pre>";
        
        if (isset($postResult['id'])) {
            echo "<p>✅ Test album created with ID: {$postResult['id']}</p>";
            
            // Verify it was created
            echo "<h2>Step 4: Verify New Album</h2>";
            $verifyQuery = "SELECT * FROM albums WHERE id = ?";
            $verifyStmt = $db->prepare($verifyQuery);
            $verifyStmt->execute([$postResult['id']]);
            $newAlbum = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($newAlbum) {
                echo "<p>✅ New album found in database</p>";
                echo "<pre>" . htmlspecialchars(json_encode($newAlbum, JSON_PRETTY_PRINT)) . "</pre>";
                
                // Check tracks
                $tracksQuery = "SELECT * FROM tracks WHERE album_id = ?";
                $tracksStmt = $db->prepare($tracksQuery);
                $tracksStmt->execute([$postResult['id']]);
                $tracks = $tracksStmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<p>Tracks created: " . count($tracks) . "</p>";
                if (count($tracks) > 0) {
                    echo "<pre>" . htmlspecialchars(json_encode($tracks, JSON_PRETTY_PRINT)) . "</pre>";
                }
            } else {
                echo "<p>❌ New album NOT found in database</p>";
            }
        }
    } else {
        echo "<p>❌ Album creation API not responding</p>";
    }
    
    echo "<h2>Step 5: Test API Again After Creation</h2>";
    
    // Test the API again to see if new album appears
    $newResponse = file_get_contents($apiUrl, false, $context);
    
    if ($newResponse) {
        $newApiData = json_decode($newResponse, true);
        if (is_array($newApiData)) {
            echo "<p>✅ API now returns " . count($newApiData) . " albums</p>";
        }
    }
    
    echo "<h2>Next Steps:</h2>";
    echo "<ol>";
    echo "<li><a href='admin/albums.php' target='_blank'>Test Albums Dashboard</a></li>";
    echo "<li>Try adding a new album manually</li>";
    echo "<li>Check browser console (F12) for JavaScript errors</li>";
    echo "<li>Check Network tab for API calls</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
