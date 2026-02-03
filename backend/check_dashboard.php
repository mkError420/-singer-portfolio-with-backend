<?php
// Check Albums Dashboard Status
echo "<h1>Albums Dashboard Check</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Database Connection:</h2>";
    echo "<p>✅ Connected successfully</p>";
    
    echo "<h2>Albums in Database:</h2>";
    $query = "SELECT id, title, cover_image, status FROM albums ORDER BY id";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($albums) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Cover Image</th><th>Status</th><th>File Exists</th></tr>";
        
        foreach ($albums as $album) {
            $fullPath = __DIR__ . '/' . $album['cover_image'];
            $exists = file_exists($fullPath);
            
            echo "<tr>";
            echo "<td>{$album['id']}</td>";
            echo "<td>" . htmlspecialchars($album['title']) . "</td>";
            echo "<td>" . htmlspecialchars($album['cover_image'] ?: 'NULL') . "</td>";
            echo "<td>{$album['status']}</td>";
            echo "<td>" . ($exists ? '✅ YES' : '❌ NO') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No albums found in database</p>";
    }
    
    echo "<h2>API Test:</h2>";
    
    // Test the albums API
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/madam-portfolio/backend/api/albums.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = file_get_contents($apiUrl, false, $context);
    
    if ($response) {
        echo "<p>✅ API responding</p>";
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
        echo "<p>❌ API not responding</p>";
    }
    
    echo "<h2>File Paths:</h2>";
    echo "<p>Backend directory: " . __DIR__ . "</p>";
    echo "<p>Uploads directory: " . __DIR__ . '/uploads' . "</p>";
    echo "<p>Uploads exists: " . (is_dir(__DIR__ . '/uploads') ? 'YES' : 'NO') . "</p>";
    
    if (is_dir(__DIR__ . '/uploads')) {
        $files = scandir(__DIR__ . '/uploads');
        echo "<p>Files in uploads: " . implode(', ', array_slice($files, 0, 10)) . "</p>";
    }
    
    echo "<h2>Next Steps:</h2>";
    echo "<ol>";
    echo "<li><a href='admin/albums.php' target='_blank'>Open Albums Dashboard</a></li>";
    echo "<li><a href='simple_fix.php' target='_blank'>Run Simple Fix if needed</a></li>";
    echo "<li>Check browser console (F12) for JavaScript errors</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
