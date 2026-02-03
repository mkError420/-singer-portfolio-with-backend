<?php
// Debug Album Category Display Issue
echo "<h1>Album Category Display Debug</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Check Albums with Categories</h2>";
    
    $query = "SELECT id, title, year, category, status FROM albums ORDER BY id DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total albums: " . count($albums) . "</p>";
    
    if (count($albums) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Year</th><th>Category</th><th>Status</th></tr>";
        foreach ($albums as $album) {
            echo "<tr>";
            echo "<td>{$album['id']}</td>";
            echo "<td>" . htmlspecialchars($album['title']) . "</td>";
            echo "<td>{$album['year']}</td>";
            echo "<td><strong>" . htmlspecialchars($album['category']) . "</strong></td>";
            echo "<td>{$album['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No albums found</p>";
    }
    
    echo "<h2>Step 2: Check Available Categories</h2>";
    
    $categoryQuery = "SELECT * FROM album_categories WHERE status = 'active' ORDER BY name";
    $categoryStmt = $db->prepare($categoryQuery);
    $categoryStmt->execute();
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total categories: " . count($categories) . "</p>";
    
    if (count($categories) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Name</th><th>Description</th></tr>";
        foreach ($categories as $category) {
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($category['name']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($category['description']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No categories found</p>";
    }
    
    echo "<h2>Step 3: Test Albums API Response</h2>";
    
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
                
                // Check category field
                if (isset($apiData[0]['category'])) {
                    echo "<p>✅ Category field exists: " . htmlspecialchars($apiData[0]['category']) . "</p>";
                } else {
                    echo "<p>❌ Category field missing from API response</p>";
                }
            }
        } else {
            echo "<p>❌ API returned invalid data</p>";
        }
    } else {
        echo "<p>❌ Albums API not responding</p>";
    }
    
    echo "<h2>Step 4: Test Creating Album with Category</h2>";
    
    $testAlbum = [
        'title' => 'Test Album ' . date('His'),
        'year' => date('Y'),
        'category' => 'rock', // Use a known category
        'description' => 'Test album for category display',
        'cover_image' => 'uploads/test_album.jpg',
        'tracks' => []
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
            echo "<p>✅ Test album created with category 'rock'</p>";
            
            // Verify it was created with correct category
            $verifyQuery = "SELECT id, title, category FROM albums WHERE id = ?";
            $verifyStmt = $db->prepare($verifyQuery);
            $verifyStmt->execute([$postResult['id']]);
            $newAlbum = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($newAlbum) {
                echo "<p>✅ New album category in database: " . htmlspecialchars($newAlbum['category']) . "</p>";
            }
        } else {
            echo "<p>❌ Album creation failed</p>";
        }
    } else {
        echo "<p>❌ Album creation API not responding</p>";
    }
    
    echo "<h2>Step 5: JavaScript Debugging</h2>";
    echo "<p><strong>To debug in Albums Dashboard:</strong></p>";
    echo "<ol>";
    echo "<li>Open <a href='admin/albums.php' target='_blank'>Albums Dashboard</a></li>";
    echo "<li>Open browser console (F12)</li>";
    echo "<li>Type: <code>albums</code> to see albums data</li>";
    echo "<li>Type: <code>albums[0].category</code> to check first album's category</li>";
    echo "<li>Check if category displays in the table</li>";
    echo "</ol>";
    
    echo "<h2>Possible Issues:</h2>";
    echo "<ul>";
    echo "<li>Album category field is NULL or empty in database</li>";
    echo "<li>JavaScript not rendering category correctly</li>";
    echo "<li>CSS hiding the category column</li>";
    echo "<li>API not returning category data</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
