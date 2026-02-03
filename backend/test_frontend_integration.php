<?php
// Test Frontend Integration with Backend
echo "<h1>Frontend Integration Test</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Check Albums with Tracks</h2>";
    
    // Get all albums with their track counts
    $query = "SELECT a.*, 
                     (SELECT COUNT(*) FROM tracks t WHERE t.album_id = a.id AND t.status = 'active') as track_count
              FROM albums a 
              WHERE a.status = 'active' 
              ORDER BY a.year DESC, a.title ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total albums: " . count($albums) . "</p>";
    
    if (count($albums) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Year</th><th>Category</th><th>Tracks</th><th>Cover Image</th><th>API Test</th></tr>";
        
        foreach ($albums as $album) {
            echo "<tr>";
            echo "<td>{$album['id']}</td>";
            echo "<td><strong>" . htmlspecialchars($album['title']) . "</strong></td>";
            echo "<td>{$album['year']}</td>";
            echo "<td>" . htmlspecialchars($album['category'] ?: 'No Category') . "</td>";
            echo "<td>{$album['track_count']}</td>";
            echo "<td>" . htmlspecialchars($album['cover_image'] ?: 'No Image') . "</td>";
            echo "<td><a href='test_album_api.php?album_id={$album['id']}' target='_blank'>Test API</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No albums found</p>";
        echo "<p><a href='admin/albums.php' target='_blank'>Add some albums first</a></p>";
    }
    
    echo "<h2>Step 2: Test Albums API</h2>";
    
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
                echo "<h3>Sample Album from API:</h3>";
                echo "<pre>" . htmlspecialchars(json_encode($apiData[0], JSON_PRETTY_PRINT)) . "</pre>";
                
                // Test album with tracks
                $firstAlbumId = $apiData[0]['id'];
                echo "<h3>Testing Album with Tracks (ID: $firstAlbumId):</h3>";
                
                $tracksUrl = $apiUrl . "?album_id=$firstAlbumId&include_tracks=1";
                $tracksResponse = file_get_contents($tracksUrl, false, $context);
                
                if ($tracksResponse) {
                    $albumWithTracks = json_decode($tracksResponse, true);
                    echo "<pre>" . htmlspecialchars(json_encode($albumWithTracks, JSON_PRETTY_PRINT)) . "</pre>";
                    
                    if (isset($albumWithTracks['tracks']) && is_array($albumWithTracks['tracks'])) {
                        echo "<p>✅ Album has " . count($albumWithTracks['tracks']) . " tracks</p>";
                    } else {
                        echo "<p>⚠️ Album has no tracks or tracks format is incorrect</p>";
                    }
                } else {
                    echo "<p>❌ Tracks API not responding</p>";
                }
            }
        } else {
            echo "<p>❌ API returned invalid data</p>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    } else {
        echo "<p>❌ Albums API not responding</p>";
    }
    
    echo "<h2>Step 3: Frontend Test</h2>";
    
    echo "<p><strong>To test the frontend:</strong></p>";
    echo "<ol>";
    echo "<li><a href='../src/index.html' target='_blank'>Open Frontend Application</a></li>";
    echo "<li>Navigate to the Music page</li>";
    echo "<li>Check if albums appear from the backend</li>";
    echo "<li>Click on albums to see tracks</li>";
    echo "<li>Test YouTube links (🎵 buttons)</li>";
    echo "</ol>";
    
    echo "<h2>Step 4: Expected Frontend Behavior</h2>";
    
    echo "<p><strong>What should work:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Albums load from backend API</li>";
    echo "<li>✅ Album covers display correctly</li>";
    echo "<li>✅ Track count shows per album</li>";
    echo "<li>✅ Click album to expand tracks</li>";
    echo "<li>✅ Track titles and durations show</li>";
    echo "<li>✅ YouTube links open in new tab</li>";
    echo "<li>✅ Play/pause buttons work</li>";
    echo "</ul>";
    
    echo "<h2>Step 5: Troubleshooting</h2>";
    
    echo "<p><strong>If albums don't appear:</strong></p>";
    echo "<ul>";
    echo "<li>Check browser console (F12) for API errors</li>";
    echo "<li>Verify CORS is enabled (should be with current setup)</li>";
    echo "<li>Check if backend server is running</li>";
    echo "<li>Test API endpoints directly above</li>";
    echo "</ul>";
    
    echo "<p><strong>If images don't load:</strong></p>";
    echo "<ul>";
    echo "<li>Check image paths in database</li>";
    echo "<li>Verify files exist in uploads folder</li>";
    echo "<li>Check browser network tab for 404 errors</li>";
    echo "</ul>";
    
    echo "<p><strong>If tracks don't show:</strong></p>";
    echo "<ul>";
    echo "<li>Check if tracks exist in database</li>";
    echo "<li>Verify tracks API endpoint works</li>";
    echo "<li>Check frontend console for track loading errors</li>";
    echo "</ul>";
    
    echo "<h2>Step 6: Create Test Album (if needed)</h2>";
    
    if (count($albums) === 0) {
        echo "<p>No albums found. Creating a test album...</p>";
        
        $testAlbum = [
            'title' => 'Frontend Test Album',
            'year' => date('Y'),
            'category' => 'rock',
            'description' => 'Test album for frontend integration',
            'cover_image' => 'uploads/test_album.jpg',
            'tracks' => [
                [
                    'title' => 'Test Track 1',
                    'youtube_url' => 'https://youtube.com/watch?v=dQw4w9WgXcQ',
                    'duration' => '3:45',
                    'track_number' => 1
                ],
                [
                    'title' => 'Test Track 2',
                    'youtube_url' => 'https://youtube.com/watch?v=9bZkp7q19f0',
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
            $result = json_decode($postResponse, true);
            if (isset($result['id'])) {
                echo "<p>✅ Test album created with ID: {$result['id']}</p>";
                echo "<p><a href=''>Refresh this page</a> to see the new album</p>";
            } else {
                echo "<p>❌ Test album creation failed</p>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
