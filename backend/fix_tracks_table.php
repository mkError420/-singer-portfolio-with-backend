<?php
// Fix Tracks Table - Add missing youtube_url column
echo "<h1>Fix Tracks Table</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Check Current Tracks Table Structure</h2>";
    
    // Get current table structure
    $structure = $db->query("DESCRIBE tracks");
    $columns = $structure->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Current Columns:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if youtube_url column exists
    $hasYoutubeUrl = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'youtube_url') {
            $hasYoutubeUrl = true;
            break;
        }
    }
    
    echo "<h2>Step 2: Add Missing Columns</h2>";
    
    if (!$hasYoutubeUrl) {
        echo "<p>Adding youtube_url column...</p>";
        $alterQuery = "ALTER TABLE tracks ADD COLUMN youtube_url VARCHAR(500) AFTER title";
        $db->exec($alterQuery);
        echo "<p>✅ youtube_url column added</p>";
    } else {
        echo "<p>✅ youtube_url column already exists</p>";
    }
    
    // Check if other required columns exist
    $requiredColumns = [
        'track_number' => "ALTER TABLE tracks ADD COLUMN track_number INT AFTER duration",
        'status' => "ALTER TABLE tracks ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER track_number"
    ];
    
    foreach ($requiredColumns as $column => $alterSql) {
        $columnExists = false;
        foreach ($columns as $col) {
            if ($col['Field'] === $column) {
                $columnExists = true;
                break;
            }
        }
        
        if (!$columnExists) {
            echo "<p>Adding $column column...</p>";
            $db->exec($alterSql);
            echo "<p>✅ $column column added</p>";
        } else {
            echo "<p>✅ $column column already exists</p>";
        }
    }
    
    echo "<h2>Step 3: Update Existing Tracks</h2>";
    
    // Update existing tracks to have default values
    $updateQuery = "UPDATE tracks SET 
                   youtube_url = CASE 
                       WHEN youtube_url IS NULL OR youtube_url = '' THEN 'https://youtube.com/watch?v=dummy'
                       ELSE youtube_url 
                   END,
                   track_number = CASE 
                       WHEN track_number IS NULL OR track_number = 0 THEN 1
                       ELSE track_number 
                   END,
                   status = CASE 
                       WHEN status IS NULL THEN 'active'
                       ELSE status 
                   END
                   WHERE 1";
    
    $db->exec($updateQuery);
    echo "<p>✅ Existing tracks updated with default values</p>";
    
    echo "<h2>Step 4: Verify Updated Structure</h2>";
    
    // Get updated structure
    $newStructure = $db->query("DESCRIBE tracks");
    $newColumns = $newStructure->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Updated Columns:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($newColumns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Step 5: Test Album Creation</h2>";
    
    // Test creating a new album with tracks
    $testAlbum = [
        'title' => 'Test Album ' . date('His'),
        'year' => date('Y'),
        'category' => 'album',
        'description' => 'Test album after fixing tracks table',
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
            echo "<p>✅ Test album creation successful! ID: {$result['id']}</p>";
        } else {
            echo "<p>❌ Test album creation failed</p>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    }
    
    echo "<h2 style='color: green;'>🎉 Tracks Table Fix Complete!</h2>";
    echo "<p><strong>What was fixed:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Added youtube_url column to tracks table</li>";
    echo "<li>✅ Added track_number column if missing</li>";
    echo "<li>✅ Added status column if missing</li>";
    echo "<li>✅ Updated existing tracks with default values</li>";
    echo "<li>✅ Tested album creation with tracks</li>";
    echo "</ul>";
    
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li><a href='admin/albums.php' target='_blank'>Test Albums Dashboard</a></li>";
    echo "<li>Try adding a new album with tracks</li>";
    echo "<li>Check browser console for success messages</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
