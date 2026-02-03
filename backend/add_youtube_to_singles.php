<?php
// Add youtube_url field to singles table
require_once 'config/database.php';

echo "<h1>🎵 Add YouTube URL to Singles Table</h1>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Check Current Singles Table Structure</h2>";
    
    // Get current table structure
    $structureQuery = "DESCRIBE singles";
    $stmt = $db->query($structureQuery);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    
    $hasYoutubeField = false;
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "</tr>";
        
        if ($column['Field'] === 'youtube_url') {
            $hasYoutubeField = true;
        }
    }
    echo "</table>";
    
    if ($hasYoutubeField) {
        echo "<h2 style='color: green;'>✅ YouTube URL field already exists!</h2>";
    } else {
        echo "<h2>Step 2: Add YouTube URL Field</h2>";
        
        // Add youtube_url field
        $alterQuery = "ALTER TABLE singles ADD COLUMN youtube_url VARCHAR(500) DEFAULT NULL AFTER cover_image";
        
        try {
            $db->exec($alterQuery);
            echo "<p style='color: green;'>✅ YouTube URL field added successfully!</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Error adding YouTube URL field: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    echo "<h2>Step 3: Update Sample Data</h2>";
    
    // Update existing singles with sample YouTube URLs
    $updateQuery = "UPDATE singles SET youtube_url = CASE 
        WHEN title LIKE '%New Beginning%' THEN 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        WHEN title LIKE '%Summer Vibes%' THEN 'https://www.youtube.com/watch?v=kJQP7kiw5Fk'
        WHEN title LIKE '%Winter%' THEN 'https://www.youtube.com/watch?v=9bZkp7q19f0'
        ELSE NULL
    END WHERE youtube_url IS NULL";
    
    try {
        $stmt = $db->prepare($updateQuery);
        $stmt->execute();
        $affectedRows = $stmt->rowCount();
        
        if ($affectedRows > 0) {
            echo "<p style='color: green;'>✅ Updated $affectedRows singles with sample YouTube URLs</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ No singles needed updating (they already have YouTube URLs)</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Error updating sample data: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<h2>Step 4: Verify Results</h2>";
    
    // Show updated singles
    $selectQuery = "SELECT id, title, artist, youtube_url FROM singles ORDER BY id";
    $stmt = $db->query($selectQuery);
    $singles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Artist</th><th>YouTube URL</th></tr>";
    
    foreach ($singles as $single) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($single['id']) . "</td>";
        echo "<td>" . htmlspecialchars($single['title']) . "</td>";
        echo "<td>" . htmlspecialchars($single['artist']) . "</td>";
        echo "<td>" . ($single['youtube_url'] ? 
            "<a href='" . htmlspecialchars($single['youtube_url']) . "' target='_blank'>" . htmlspecialchars($single['youtube_url']) . "</a>" : 
            "<span style='color: #999;'>No URL</span>") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>✅ Complete!</h2>";
    echo "<p>The singles table now has a youtube_url field. You can now update the admin interface to manage YouTube URLs.</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
