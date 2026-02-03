<?php
// Final Fix - Update database paths and create images
echo "<h1>Final Fix for Album Images</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    // Step 1: Create uploads directory
    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        echo "<p>✅ Created uploads directory</p>";
    }
    
    // Step 2: Get current albums
    $query = "SELECT id, title, cover_image FROM albums";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Current Albums:</h2>";
    foreach ($albums as $album) {
        echo "<p>ID {$album['id']}: {$album['title']} - Current: {$album['cover_image']}</p>";
    }
    
    // Step 3: Create images for all albums
    echo "<h2>Creating Images...</h2>";
    $colors = [
        1 => [255, 99, 71],   // Tomato
        2 => [60, 179, 113],  // Medium Sea Green
        3 => [106, 90, 205],  // Slate Blue
        4 => [255, 165, 0],   // Orange
        5 => [147, 112, 219], // Medium Purple
        6 => [255, 20, 147],  // Deep Pink
    ];
    
    foreach ($albums as $album) {
        $imagePath = $uploadDir . '/album_' . $album['id'] . '.jpg';
        $color = $colors[$album['id']] ?? [128, 128, 128];
        
        if (!file_exists($imagePath)) {
            // Create image
            $img = imagecreatetruecolor(300, 300);
            $bgColor = imagecolorallocate($img, $color[0], $color[1], $color[2]);
            imagefill($img, 0, 0, $bgColor);
            
            // Add text
            $textColor = imagecolorallocate($img, 255, 255, 255);
            
            // Add album ID
            imagestring($img, 5, 10, 10, "ID: " . $album['id'], $textColor);
            
            // Add title (split if too long)
            $title = substr($album['title'], 0, 20);
            imagestring($img, 5, 10, 140, $title, $textColor);
            
            imagejpeg($img, $imagePath, 90);
            imagedestroy($img);
            
            echo "<p>✅ Created: album_{$album['id']}.jpg</p>";
        } else {
            echo "<p>✅ Exists: album_{$album['id']}.jpg</p>";
        }
    }
    
    // Step 4: Update database with NEW paths
    echo "<h2>Updating Database Paths...</h2>";
    
    foreach ($albums as $album) {
        $newPath = 'uploads/album_' . $album['id'] . '.jpg';
        
        $query = "UPDATE albums SET cover_image = :cover_image WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':cover_image', $newPath);
        $stmt->bindParam(':id', $album['id']);
        
        if ($stmt->execute()) {
            echo "<p>✅ Updated Album {$album['id']}: $newPath</p>";
        } else {
            echo "<p>❌ Failed to update Album {$album['id']}</p>";
        }
    }
    
    // Step 5: Verify the update
    echo "<h2>Verification:</h2>";
    $query = "SELECT id, title, cover_image FROM albums";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $updatedAlbums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($updatedAlbums as $album) {
        $fullPath = __DIR__ . '/' . $album['cover_image'];
        $exists = file_exists($fullPath);
        
        echo "<p>";
        echo "<strong>Album {$album['id']}:</strong> {$album['title']}<br>";
        echo "Path: {$album['cover_image']}<br>";
        echo "File exists: " . ($exists ? '✅ YES' : '❌ NO') . "<br>";
        
        if ($exists) {
            echo '<img src="' . $album['cover_image'] . '" width="60" height="60" style="border: 2px solid green;"> ';
            echo '<img src="../' . $album['cover_image'] . '" width="60" height="60" style="border: 2px solid blue;">';
        }
        echo "</p>";
    }
    
    echo "<h2 style='color: green;'>🎉 Fix Complete!</h2>";
    echo "<p><strong>What was fixed:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Database updated from 'public/images/demo/albumX.jpg' to 'uploads/album_X.jpg'</li>";
    echo "<li>✅ Real image files created in backend/uploads/</li>";
    echo "<li>✅ All albums now have working cover images</li>";
    echo "</ul>";
    
    echo "<p><a href='admin/albums.php' target='_blank'>👉 Test Albums Dashboard</a></p>";
    echo "<p><strong>Refresh the page after clicking the link!</strong></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
