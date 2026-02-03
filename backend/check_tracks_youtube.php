<?php
require_once 'config/database.php';

echo "<h1>🎵 Check Tracks YouTube URLs</h1>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Tracks with YouTube URLs</h2>";
    
    // Get all tracks with YouTube URLs
    $query = "SELECT t.*, a.title as album_title FROM tracks t 
              JOIN albums a ON t.album_id = a.id 
              WHERE t.youtube_url IS NOT NULL AND t.youtube_url != '' 
              ORDER BY a.title, t.track_number";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tracks)) {
        echo "<p style='color: orange;'>⚠️ No tracks found with YouTube URLs</p>";
        
        // Check if tracks table has youtube_url column
        echo "<h3>Checking tracks table structure...</h3>";
        $structureQuery = "DESCRIBE tracks";
        $stmt = $db->query($structureQuery);
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $hasYoutubeColumn = false;
        foreach ($columns as $column) {
            echo "<p><strong>{$column['Field']}:</strong> {$column['Type']}</p>";
            if ($column['Field'] === 'youtube_url') {
                $hasYoutubeColumn = true;
            }
        }
        
        if (!$hasYoutubeColumn) {
            echo "<h3 style='color: red;'>❌ youtube_url column not found in tracks table!</h3>";
            echo "<p>Adding youtube_url column to tracks table...</p>";
            
            $alterQuery = "ALTER TABLE tracks ADD COLUMN youtube_url VARCHAR(500) DEFAULT NULL AFTER audio_file";
            $db->exec($alterQuery);
            echo "<p style='color: green;'>✅ youtube_url column added to tracks table</p>";
        }
        
        // Show some sample tracks
        echo "<h3>Sample tracks (without YouTube URLs):</h3>";
        $sampleQuery = "SELECT t.*, a.title as album_title FROM tracks t 
                       JOIN albums a ON t.album_id = a.id 
                       LIMIT 10";
        $stmt = $db->prepare($sampleQuery);
        $stmt->execute();
        $sampleTracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Album</th><th>Track</th><th>YouTube URL</th></tr>";
        
        foreach ($sampleTracks as $track) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($track['album_title']) . "</td>";
            echo "<td>" . htmlspecialchars($track['title']) . "</td>";
            echo "<td>" . ($track['youtube_url'] ? htmlspecialchars($track['youtube_url']) : '<span style="color: #999;">NULL</span>') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Album</th><th>Track #</th><th>Title</th><th>YouTube URL</th><th>Test</th></tr>";
        
        foreach ($tracks as $track) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($track['album_title']) . "</td>";
            echo "<td>" . htmlspecialchars($track['track_number']) . "</td>";
            echo "<td>" . htmlspecialchars($track['title']) . "</td>";
            echo "<td><small>" . htmlspecialchars($track['youtube_url']) . "</small></td>";
            echo "<td><a href='" . htmlspecialchars($track['youtube_url']) . "' target='_blank'>Test</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>Adding Sample YouTube URLs to Tracks</h3>";
        
        // Add sample YouTube URLs to tracks that don't have them
        $updateQuery = "UPDATE tracks SET youtube_url = CASE 
            WHEN title LIKE '%track%' OR title LIKE '%song%' OR title LIKE '%love%' THEN 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
            WHEN title LIKE '%summer%' OR title LIKE '%vibes%' THEN 'https://www.youtube.com/watch?v=kJQP7kiw5Fk'
            WHEN title LIKE '%winter%' OR title LIKE '%tale%' THEN 'https://www.youtube.com/watch?v=9bZkp7q19f0'
            ELSE 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        END WHERE youtube_url IS NULL OR youtube_url = ''";
        
        $stmt = $db->prepare($updateQuery);
        $stmt->execute();
        $affectedRows = $stmt->rowCount();
        
        if ($affectedRows > 0) {
            echo "<p style='color: green;'>✅ Updated $affectedRows tracks with sample YouTube URLs</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ No tracks needed updating</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
