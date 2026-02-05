<?php
// Check videos in database
try {
    $db = new PDO("mysql:host=localhost;dbname=madam_portfolio", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DATABASE VIDEOS CHECK ===\n\n";
    
    // Check total videos in database
    $stmt = $db->query("SELECT COUNT(*) as count FROM videos");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total videos in database: " . $count['count'] . "\n\n";
    
    // Get all videos
    $stmt = $db->query("SELECT * FROM videos ORDER BY created_at DESC");
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($videos)) {
        echo "No videos found in database.\n";
    } else {
        echo "Videos in database:\n";
        echo "==================\n";
        foreach ($videos as $video) {
            echo "ID: " . $video['id'] . "\n";
            echo "Title: " . $video['title'] . "\n";
            echo "Description: " . $video['description'] . "\n";
            echo "Video URL: " . $video['video_url'] . "\n";
            echo "Thumbnail: " . $video['thumbnail'] . "\n";
            echo "Category: " . $video['category'] . "\n";
            echo "Duration: " . $video['duration'] . "\n";
            echo "Status: " . $video['status'] . "\n";
            echo "Created: " . $video['created_at'] . "\n";
            echo "---\n";
        }
    }
    
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
