<?php
// Test script to check videos in database
require_once 'backend/config/database.php';

try {
    $db = new PDO("mysql:host=localhost;dbname=madam_portfolio", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Database connected successfully!\n\n";
    
    // Check if videos table exists
    $stmt = $db->query("SHOW TABLES LIKE 'videos'");
    if ($stmt->rowCount() == 0) {
        echo "Videos table does not exist!\n";
        exit;
    }
    
    echo "Videos table exists.\n\n";
    
    // Check videos in database
    $stmt = $db->query("SELECT * FROM videos");
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($videos) . " videos in database:\n\n";
    
    if (empty($videos)) {
        echo "No videos found. Let's add a sample video...\n";
        
        // Insert a sample video
        $insert = $db->prepare("INSERT INTO videos (title, description, video_url, thumbnail, category, duration) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->execute([
            'Sample Video',
            'This is a sample video uploaded from dashboard',
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://via.placeholder.com/300x200/333/fff?text=Sample+Video',
            'music',
            '3:45'
        ]);
        
        echo "Sample video added!\n";
        
        // Get videos again
        $stmt = $db->query("SELECT * FROM videos");
        $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    foreach ($videos as $video) {
        echo "ID: " . $video['id'] . "\n";
        echo "Title: " . $video['title'] . "\n";
        echo "URL: " . $video['video_url'] . "\n";
        echo "Category: " . $video['category'] . "\n";
        echo "Created: " . $video['created_at'] . "\n";
        echo "---\n";
    }
    
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
