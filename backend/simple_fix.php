<?php
// Simple Fix - Use existing demo images and update paths
echo "<h1>Simple Fix for Album Images</h1>";

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
    
    // Step 2: Copy existing demo images to uploads
    echo "<h2>Copying Demo Images...</h2>";
    
    $demoImages = [
        1 => 'public/images/demo/album1.jpg',
        2 => 'public/images/demo/album2.jpg',
        3 => 'public/images/demo/album3.jpg'
    ];
    
    foreach ($demoImages as $albumId => $demoPath) {
        $sourcePath = __DIR__ . '/../' . $demoPath;
        $destPath = $uploadDir . '/album_' . $albumId . '.jpg';
        
        if (file_exists($sourcePath)) {
            if (!file_exists($destPath)) {
                if (copy($sourcePath, $destPath)) {
                    echo "<p>✅ Copied: $demoPath → uploads/album_$albumId.jpg</p>";
                } else {
                    echo "<p>❌ Failed to copy: $demoPath</p>";
                }
            } else {
                echo "<p>✅ Already exists: uploads/album_$albumId.jpg</p>";
            }
        } else {
            echo "<p>⚠ Demo image not found: $demoPath</p>";
            // Create a simple text-based placeholder
            $placeholderContent = "Album $albumId";
            file_put_contents($destPath . '.txt', $placeholderContent);
            echo "<p>✅ Created placeholder: uploads/album_$albumId.jpg.txt</p>";
        }
    }
    
    // Step 3: Get current albums and update paths
    echo "<h2>Updating Database Paths...</h2>";
    
    $query = "SELECT id, title, cover_image FROM albums";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($albums as $album) {
        $newPath = 'uploads/album_' . $album['id'] . '.jpg';
        
        $updateQuery = "UPDATE albums SET cover_image = :cover_image WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':cover_image', $newPath);
        $updateStmt->bindParam(':id', $album['id']);
        
        if ($updateStmt->execute()) {
            echo "<p>✅ Updated Album {$album['id']}: $newPath</p>";
        } else {
            echo "<p>❌ Failed to update Album {$album['id']}</p>";
        }
    }
    
    // Step 4: Create simple placeholder images for missing ones
    echo "<h2>Creating Placeholders for Missing Images...</h2>";
    
    foreach ($albums as $album) {
        $imagePath = $uploadDir . '/album_' . $album['id'] . '.jpg';
        
        if (!file_exists($imagePath)) {
            // Create a simple SVG image as placeholder
            $svgContent = '<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg">
                <rect width="300" height="300" fill="#' . sprintf('%02x%02x%02x', rand(100, 255), rand(100, 255), rand(100, 255)) . '"/>
                <text x="150" y="140" text-anchor="middle" fill="white" font-size="20" font-family="Arial">Album ' . $album['id'] . '</text>
                <text x="150" y="170" text-anchor="middle" fill="white" font-size="16" font-family="Arial">' . substr($album['title'], 0, 20) . '</text>
            </svg>';
            
            file_put_contents($imagePath . '.svg', $svgContent);
            
            // Also create a simple 1x1 pixel as fallback
            $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
            file_put_contents($imagePath, $pngContent);
            
            echo "<p>✅ Created placeholder for Album {$album['id']}</p>";
        }
    }
    
    // Step 5: Verification
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
        echo "</p>";
    }
    
    echo "<h2 style='color: green;'>🎉 Simple Fix Complete!</h2>";
    echo "<p><strong>What was done:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Database paths updated to 'uploads/album_X.jpg'</li>";
    echo "<li>✅ Demo images copied to uploads folder</li>";
    echo "<li>✅ Placeholders created for missing images</li>";
    echo "</ul>";
    
    echo "<p><a href='admin/albums.php' target='_blank'>👉 Test Albums Dashboard</a></p>";
    echo "<p><strong>Important: Refresh the page after clicking!</strong></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
