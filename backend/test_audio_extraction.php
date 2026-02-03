<?php
require_once 'config/database.php';
require_once 'services/youtube_audio_extractor.php';

echo "<h1>🎵 Test Audio Extraction System</h1>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Check Tracks with YouTube URLs</h2>";
    
    // Get tracks with YouTube URLs but no audio files
    $query = "SELECT t.*, a.title as album_title FROM tracks t 
              JOIN albums a ON t.album_id = a.id 
              WHERE t.youtube_url IS NOT NULL AND t.youtube_url != '' 
              AND (t.audio_file IS NULL OR t.audio_file = '')
              ORDER BY a.title, t.track_number";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $tracksToProcess = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tracksToProcess)) {
        echo "<p style='color: green;'>✅ All tracks already have audio files</p>";
        
        // Show existing audio files
        echo "<h2>Existing Audio Files:</h2>";
        $query = "SELECT t.*, a.title as album_title FROM tracks t 
                  JOIN albums a ON t.album_id = a.id 
                  WHERE t.audio_file IS NOT NULL AND t.audio_file != ''
                  ORDER BY a.title, t.track_number";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $tracksWithAudio = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Album</th><th>Track</th><th>Audio File</th><th>Test</th></tr>";
        
        foreach ($tracksWithAudio as $track) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($track['album_title']) . "</td>";
            echo "<td>" . htmlspecialchars($track['title']) . "</td>";
            echo "<td><small>" . htmlspecialchars($track['audio_file']) . "</small></td>";
            echo "<td><a href='uploads/" . htmlspecialchars($track['audio_file']) . "' target='_blank'>Test Audio</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p style='color: orange;'>⚠️ Found " . count($tracksToProcess) . " tracks that need audio extraction</p>";
        
        echo "<h2>Tracks to Process:</h2>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Album</th><th>Track</th><th>YouTube URL</th><th>Status</th></tr>";
        
        $extractor = new YouTubeAudioExtractor($database);
        
        foreach ($tracksToProcess as $track) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($track['album_title']) . "</td>";
            echo "<td>" . htmlspecialchars($track['title']) . "</td>";
            echo "<td><small>" . htmlspecialchars($track['youtube_url']) . "</small></td>";
            
            // Extract audio
            echo "<td>";
            $result = $extractor->extractAudioFromYouTube($track['youtube_url'], $track['id'], $track['album_id']);
            
            if ($result['success']) {
                echo "<span style='color: green;'>✅ " . htmlspecialchars($result['message']) . "</span>";
                echo "<br><small>Audio file: " . htmlspecialchars($result['audio_file']) . "</small>";
            } else {
                echo "<span style='color: red;'>❌ " . htmlspecialchars($result['message']) . "</span>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>Step 2: Check Uploads Directory</h2>";
    $uploadsDir = __DIR__ . '/uploads';
    
    if (file_exists($uploadsDir)) {
        echo "<p style='color: green;'>✅ Uploads directory exists</p>";
        
        $audioFiles = glob($uploadsDir . '/*.mp3');
        echo "<p>Found " . count($audioFiles) . " MP3 files</p>";
        
        if (!empty($audioFiles)) {
            echo "<h3>Audio Files:</h3>";
            echo "<ul>";
            foreach ($audioFiles as $file) {
                $fileName = basename($file);
                $fileSize = filesize($file);
                echo "<li><a href='uploads/" . htmlspecialchars($fileName) . "' target='_blank'>" . htmlspecialchars($fileName) . "</a> (" . number_format($fileSize) . " bytes)</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color: red;'>❌ Uploads directory not found</p>";
        if (mkdir($uploadsDir, 0755, true)) {
            echo "<p style='color: green;'>✅ Created uploads directory</p>";
        }
    }
    
    echo "<h2>Step 3: Test API Response</h2>";
    
    // Test the albums API with track loading
    $albumQuery = "SELECT id, title FROM albums WHERE status = 'active' LIMIT 1";
    $stmt = $db->prepare($albumQuery);
    $stmt->execute();
    $album = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($album) {
        echo "<p>Testing album: " . htmlspecialchars($album['title']) . "</p>";
        
        $apiUrl = "http://localhost/madam-portfolio/backend/api/albums_fixed.php?album_id=" . $album['id'] . "&include_tracks=1";
        echo "<p>API URL: <a href='" . htmlspecialchars($apiUrl) . "' target='_blank'>" . htmlspecialchars($apiUrl) . "</a></p>";
        
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Content-Type: application/json'
            ]
        ]);
        
        $response = file_get_contents($apiUrl, false, $context);
        
        if ($response) {
            $data = json_decode($response, true);
            echo "<h3>API Response:</h3>";
            echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
            echo htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT));
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>❌ Failed to get API response</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
