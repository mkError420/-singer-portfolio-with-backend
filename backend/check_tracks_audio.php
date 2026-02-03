<?php
require_once 'config/database.php';

echo "<h1>🎵 Check Tracks Audio Files</h1>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Tracks Table Structure</h2>";
    
    // Check tracks table structure
    $structureQuery = "DESCRIBE tracks";
    $stmt = $db->query($structureQuery);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasAudioColumn = false;
    foreach ($columns as $column) {
        echo "<p><strong>{$column['Field']}:</strong> {$column['Type']}</p>";
        if ($column['Field'] === 'audio_file') {
            $hasAudioColumn = true;
        }
    }
    
    if (!$hasAudioColumn) {
        echo "<h3 style='color: red;'>❌ audio_file column not found in tracks table!</h3>";
        echo "<p>Adding audio_file column to tracks table...</p>";
        
        $alterQuery = "ALTER TABLE tracks ADD COLUMN audio_file VARCHAR(500) DEFAULT NULL AFTER youtube_url";
        $db->exec($alterQuery);
        echo "<p style='color: green;'>✅ audio_file column added to tracks table</p>";
    }
    
    echo "<h2>Tracks with Audio Files</h2>";
    
    // Get all tracks with audio files
    $query = "SELECT t.*, a.title as album_title FROM tracks t 
              JOIN albums a ON t.album_id = a.id 
              WHERE t.audio_file IS NOT NULL AND t.audio_file != '' 
              ORDER BY a.title, t.track_number";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tracks)) {
        echo "<p style='color: orange;'>⚠️ No tracks found with audio files</p>";
        
        // Show some sample tracks
        echo "<h3>Sample tracks (without audio files):</h3>";
        $sampleQuery = "SELECT t.*, a.title as album_title FROM tracks t 
                       JOIN albums a ON t.album_id = a.id 
                       LIMIT 10";
        $stmt = $db->prepare($sampleQuery);
        $stmt->execute();
        $sampleTracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Album</th><th>Track #</th><th>Title</th><th>Audio File</th><th>YouTube URL</th></tr>";
        
        foreach ($sampleTracks as $track) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($track['album_title']) . "</td>";
            echo "<td>" . htmlspecialchars($track['track_number']) . "</td>";
            echo "<td>" . htmlspecialchars($track['title']) . "</td>";
            echo "<td>" . ($track['audio_file'] ? htmlspecialchars($track['audio_file']) : '<span style="color: #999;">NULL</span>') . "</td>";
            echo "<td>" . ($track['youtube_url'] ? '<span style="color: #ff0000;">' . htmlspecialchars($track['youtube_url']) . '</span>' : '<span style="color: #999;">NULL</span>') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>Adding Sample Audio Files to Tracks</h3>";
        
        // Add sample audio files to tracks that don't have them
        $updateQuery = "UPDATE tracks SET audio_file = CASE 
            WHEN id % 3 = 1 THEN 'uploads/sample_audio_1.mp3'
            WHEN id % 3 = 2 THEN 'uploads/sample_audio_2.mp3'
            ELSE 'uploads/sample_audio_3.mp3'
        END WHERE audio_file IS NULL OR audio_file = ''";
        
        $stmt = $db->prepare($updateQuery);
        $stmt->execute();
        $affectedRows = $stmt->rowCount();
        
        if ($affectedRows > 0) {
            echo "<p style='color: green;'>✅ Updated $affectedRows tracks with sample audio file paths</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ No tracks needed updating</p>";
        }
        
        // Create sample audio files
        echo "<h3>Creating Sample Audio Files</h3>";
        $sampleAudioDir = __DIR__ . '/uploads';
        
        if (!file_exists($sampleAudioDir)) {
            mkdir($sampleAudioDir, 0755, true);
            echo "<p style='color: green;'>✅ Created uploads directory</p>";
        }
        
        $sampleFiles = ['sample_audio_1.mp3', 'sample_audio_2.mp3', 'sample_audio_3.mp3'];
        foreach ($sampleFiles as $file) {
            $filePath = $sampleAudioDir . '/' . $file;
            if (!file_exists($filePath)) {
                // Create a small dummy audio file
                $audioContent = "dummy audio content for " . $file;
                file_put_contents($filePath, $audioContent);
                echo "<p style='color: green;'>✅ Created sample audio file: $file</p>";
            } else {
                echo "<p style='color: #999;'>Sample audio file already exists: $file</p>";
            }
        }
        
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Album</th><th>Track #</th><th>Title</th><th>Audio File</th><th>Test</th></tr>";
        
        foreach ($tracks as $track) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($track['album_title']) . "</td>";
            echo "<td>" . htmlspecialchars($track['track_number']) . "</td>";
            echo "<td>" . htmlspecialchars($track['title']) . "</td>";
            echo "<td><small>" . htmlspecialchars($track['audio_file']) . "</small></td>";
            echo "<td><a href='" . htmlspecialchars($track['audio_file']) . "' target='_blank'>Test</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>Check Uploads Directory</h2>";
    $uploadsDir = __DIR__ . '/uploads';
    
    if (file_exists($uploadsDir)) {
        echo "<p style='color: green;'>✅ Uploads directory exists: " . htmlspecialchars($uploadsDir) . "</p>";
        
        $audioFiles = glob($uploadsDir . '/*.mp3');
        echo "<p>Found " . count($audioFiles) . " MP3 files in uploads directory</p>";
        
        if (!empty($audioFiles)) {
            echo "<ul>";
            foreach ($audioFiles as $file) {
                echo "<li>" . htmlspecialchars(basename($file)) . "</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color: red;'>❌ Uploads directory not found</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
