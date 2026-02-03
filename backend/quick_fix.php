<?php
// Quick Fix - Create working images and update database
echo "<h1>Quick Fix for Album Images</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    // Step 1: Create uploads directory in backend
    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        echo "<p>✅ Created uploads directory</p>";
    }
    
    // Step 2: Create simple colored images
    $albums = [
        ['id' => 1, 'title' => 'The new 2026', 'color' => [255, 99, 71]],
        ['id' => 2, 'title' => 'Echoes of Emotion', 'color' => [60, 179, 113]],
        ['id' => 3, 'title' => 'Soulful Journey', 'color' => [106, 90, 205]],
        ['id' => 4, 'title' => 'Acoustic Sessions', 'color' => [255, 165, 0]]
    ];
    
    foreach ($albums as $album) {
        $imagePath = $uploadDir . '/album_' . $album['id'] . '.jpg';
        
        if (!file_exists($imagePath)) {
            // Create image
            $img = imagecreatetruecolor(300, 300);
            $bgColor = imagecolorallocate($img, $album['color'][0], $album['color'][1], $album['color'][2]);
            imagefill($img, 0, 0, $bgColor);
            
            // Add text
            $textColor = imagecolorallocate($img, 255, 255, 255);
            $fontSize = 16;
            $angle = 0;
            $x = 150;
            $y = 150;
            
            // Try to use built-in font
            imagestring($img, 5, $x - 80, $y - 10, $album['title'], $textColor);
            
            imagejpeg($img, $imagePath, 90);
            imagedestroy($img);
            
            echo "<p>✅ Created image: album_{$album['id']}.jpg</p>";
        } else {
            echo "<p>✅ Image exists: album_{$album['id']}.jpg</p>";
        }
    }
    
    // Step 3: Update database with correct paths
    echo "<h2>Updating Database...</h2>";
    
    foreach ($albums as $album) {
        $imagePath = 'uploads/album_' . $album['id'] . '.jpg';
        
        // Check if album exists and update
        $query = "UPDATE albums SET cover_image = :cover_image WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':cover_image', $imagePath);
        $stmt->bindParam(':id', $album['id']);
        
        if ($stmt->execute()) {
            echo "<p>✅ Updated Album {$album['id']} with: $imagePath</p>";
        } else {
            echo "<p>❌ Failed to update Album {$album['id']}</p>";
        }
    }
    
    // Step 4: Test the paths
    echo "<h2>Testing Image Paths:</h2>";
    
    foreach ($albums as $album) {
        $imagePath = 'uploads/album_' . $album['id'] . '.jpg';
        $fullPath = __DIR__ . '/' . $imagePath;
        
        echo "<p>";
        echo "<strong>Album {$album['id']}:</strong><br>";
        echo "Path: $imagePath<br>";
        echo "Full Path: $fullPath<br>";
        echo "Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "<br>";
        
        if (file_exists($fullPath)) {
            echo '<img src="' . $imagePath . '" width="60" height="60" style="border: 1px solid #ccc;"> ';
            echo '<img src="../' . $imagePath . '" width="60" height="60" style="border: 1px solid #ccc;">';
        }
        echo "</p>";
    }
    
    echo "<h2 style='color: green;'>Fix Complete!</h2>";
    echo "<p><strong>What was done:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Created backend/uploads/ directory</li>";
    echo "<li>✅ Generated 4 album cover images</li>";
    echo "<li>✅ Updated database with correct paths</li>";
    echo "<li>✅ Tested image accessibility</li>";
    echo "</ul>";
    
    echo "<p><a href='admin/albums.php' target='_blank'>Test Albums Dashboard →</a></p>";
    echo "<p><strong>Expected path structure:</strong></p>";
    echo "<code>backend/uploads/album_1.jpg</code><br>";
    echo "<code>backend/uploads/album_2.jpg</code><br>";
    echo "<code>backend/uploads/album_3.jpg</code><br>";
    echo "<code>backend/uploads/album_4.jpg</code>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
