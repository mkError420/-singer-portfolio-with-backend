<?php
require_once 'config/database.php';
require_once 'services/youtube_audio_extractor.php';

echo "<h1>🎵 Test Single Audio Extraction</h1>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Find a Track with YouTube URL</h2>";
    
    // Get a track with YouTube URL
    $query = "SELECT t.*, a.title as album_title FROM tracks t 
              JOIN albums a ON t.album_id = a.id 
              WHERE t.youtube_url IS NOT NULL AND t.youtube_url != '' 
              ORDER BY a.title, t.track_number 
              LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $track = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$track) {
        echo "<p style='color: red;'>❌ No tracks found with YouTube URLs</p>";
        
        // Add a test YouTube URL to a track
        echo "<h3>Adding test YouTube URL to first track...</h3>";
        $updateQuery = "UPDATE tracks SET youtube_url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' WHERE youtube_url IS NULL OR youtube_url = '' LIMIT 1";
        $stmt = $db->prepare($updateQuery);
        $stmt->execute();
        
        // Get the updated track
        $stmt = $db->prepare($query);
        $stmt->execute();
        $track = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($track) {
            echo "<p style='color: green;'>✅ Added test YouTube URL to track: " . htmlspecialchars($track['title']) . "</p>";
        }
    }
    
    if ($track) {
        echo "<h3>Track Found:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Album</th><th>Track</th><th>YouTube URL</th><th>Audio File</th></tr>";
        echo "<tr>";
        echo "<td>" . htmlspecialchars($track['album_title']) . "</td>";
        echo "<td>" . htmlspecialchars($track['title']) . "</td>";
        echo "<td><small>" . htmlspecialchars($track['youtube_url']) . "</small></td>";
        echo "<td>" . ($track['audio_file'] ? htmlspecialchars($track['audio_file']) : '<span style="color: #999;">NULL</span>') . "</td>";
        echo "</tr>";
        echo "</table>";
        
        echo "<h2>Step 2: Test Audio Extraction</h2>";
        
        $extractor = new YouTubeAudioExtractor($database);
        
        echo "<p>Extracting audio for track: " . htmlspecialchars($track['title']) . "</p>";
        echo "<p>YouTube URL: " . htmlspecialchars($track['youtube_url']) . "</p>";
        
        $result = $extractor->extractAudioFromYouTube($track['youtube_url'], $track['id'], $track['album_id']);
        
        if ($result['success']) {
            echo "<h3 style='color: green;'>✅ Extraction Successful!</h3>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($result['message']) . "</p>";
            echo "<p><strong>Audio File:</strong> " . htmlspecialchars($result['audio_file']) . "</p>";
            echo "<p><strong>Audio Path:</strong> " . htmlspecialchars($result['audio_path']) . "</p>";
            
            // Check if file was actually created
            if (file_exists($result['audio_path'])) {
                echo "<p style='color: green;'>✅ Audio file created successfully</p>";
                echo "<p><strong>File Size:</strong> " . number_format(filesize($result['audio_path'])) . " bytes</p>";
                echo "<p><a href='uploads/" . htmlspecialchars($result['audio_file']) . "' target='_blank'>Test Audio File</a></p>";
            } else {
                echo "<p style='color: red;'>❌ Audio file was not created</p>";
            }
        } else {
            echo "<h3 style='color: red;'>❌ Extraction Failed!</h3>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($result['message']) . "</p>";
        }
        
        echo "<h2>Step 3: Test API Endpoint</h2>";
        
        // Test the API endpoint
        $apiUrl = 'http://localhost/madam-portfolio/backend/services/youtube_audio_extractor.php';
        $postData = json_encode([
            'action' => 'extract_single',
            'youtube_url' => $track['youtube_url'],
            'track_id' => $track['id'],
            'album_id' => $track['album_id']
        ]);
        
        echo "<p>API URL: <a href='" . htmlspecialchars($apiUrl) . "' target='_blank'>" . htmlspecialchars($apiUrl) . "</a></p>";
        echo "<p>POST Data: <pre>" . htmlspecialchars($postData) . "</pre></p>";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postData)
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo "<p style='color: red;'>❌ cURL Error: " . htmlspecialchars($error) . "</p>";
        } else {
            echo "<p><strong>HTTP Status:</strong> " . $httpCode . "</p>";
            echo "<p><strong>Response:</strong></p>";
            echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
            echo htmlspecialchars($response);
            echo "</pre>";
            
            $responseData = json_decode($response, true);
            if ($responseData) {
                if ($responseData['success']) {
                    echo "<p style='color: green;'>✅ API extraction successful</p>";
                } else {
                    echo "<p style='color: red;'>❌ API extraction failed: " . htmlspecialchars($responseData['message']) . "</p>";
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
