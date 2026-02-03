<?php
// Test Category Submission in Album Creation
echo "<h1>Test Category Submission</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Test Album Creation with Category</h2>";
    
    // Test different categories
    $testCategories = ['rock', 'pop', 'jazz', 'album', 'acoustic'];
    
    foreach ($testCategories as $category) {
        echo "<h3>Testing with category: '$category'</h3>";
        
        $testAlbum = [
            'title' => "Test Album $category " . date('His'),
            'year' => date('Y'),
            'category' => $category,
            'description' => "Test album with category $category",
            'cover_image' => 'uploads/test_album.jpg',
            'tracks' => [
                [
                    'title' => 'Test Track 1',
                    'youtube_url' => 'https://youtube.com/watch?v=test1',
                    'duration' => '3:45',
                    'track_number' => 1
                ]
            ]
        ];
        
        $postData = json_encode($testAlbum);
        $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/madam-portfolio/backend/api/albums.php';
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => $postData
            ]
        ]);
        
        $response = file_get_contents($apiUrl, false, $context);
        
        if ($response) {
            $result = json_decode($response, true);
            
            if (isset($result['id'])) {
                echo "<p>✅ Album created with ID: {$result['id']}</p>";
                
                // Verify the category was saved correctly
                $verifyQuery = "SELECT id, title, category FROM albums WHERE id = ?";
                $verifyStmt = $db->prepare($verifyQuery);
                $verifyStmt->execute([$result['id']]);
                $savedAlbum = $verifyStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($savedAlbum) {
                    $savedCategory = $savedAlbum['category'];
                    if ($savedCategory === $category) {
                        echo "<p>✅ Category saved correctly: '$savedCategory'</p>";
                    } else {
                        echo "<p>❌ Category mismatch. Expected: '$category', Got: '$savedCategory'</p>";
                    }
                } else {
                    echo "<p>❌ Could not verify saved album</p>";
                }
            } else {
                echo "<p>❌ Album creation failed: " . ($result['message'] ?? 'Unknown error') . "</p>";
            }
        } else {
            echo "<p>❌ API not responding</p>";
        }
        
        echo "<hr>";
    }
    
    echo "<h2>Step 2: Check All Albums with Categories</h2>";
    
    $query = "SELECT id, title, category, created_at FROM albums ORDER BY created_at DESC LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $recentAlbums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Category</th><th>Created</th></tr>";
    
    foreach ($recentAlbums as $album) {
        $categoryDisplay = $album['category'] ?: '<em>NULL</em>';
        echo "<tr>";
        echo "<td>{$album['id']}</td>";
        echo "<td>" . htmlspecialchars($album['title']) . "</td>";
        echo "<td><strong>" . htmlspecialchars($categoryDisplay) . "</strong></td>";
        echo "<td>{$album['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Step 3: Test API Response Format</h2>";
    
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/madam-portfolio/backend/api/albums.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = file_get_contents($apiUrl, false, $context);
    
    if ($response) {
        $apiData = json_decode($response, true);
        
        if (is_array($apiData) && count($apiData) > 0) {
            echo "<h3>Sample Album from API:</h3>";
            echo "<pre>" . htmlspecialchars(json_encode($apiData[0], JSON_PRETTY_PRINT)) . "</pre>";
            
            // Check if category field exists
            if (isset($apiData[0]['category'])) {
                echo "<p>✅ Category field exists in API response: " . htmlspecialchars($apiData[0]['category']) . "</p>";
            } else {
                echo "<p>❌ Category field missing from API response</p>";
            }
        }
    }
    
    echo "<h2>Step 4: Browser Test Instructions</h2>";
    echo "<p><strong>To test in Albums Dashboard:</strong></p>";
    echo "<ol>";
    echo "<li>Open <a href='admin/albums.php' target='_blank'>Albums Dashboard</a></li>";
    echo "<li>Open browser console (F12)</li>";
    echo "<li>Click 'Add New Album'</li>";
    echo "<li>Fill in title, year, and select a category</li>";
    echo "<li>Click 'Save Album'</li>";
    echo "<li>Watch console for 'Selected category value' message</li>";
    echo "<li>Check if album appears in table with correct category</li>";
    echo "</ol>";
    
    echo "<h2>Possible Issues:</h2>";
    echo "<ul>";
    echo "<li>Category dropdown not populated (check previous fixes)</li>";
    echo "<li>Form not sending category value (check console logs)</li>";
    echo "<li>API not saving category (check this test)</li>";
    echo "<li>Frontend not displaying category (check rendering)</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
