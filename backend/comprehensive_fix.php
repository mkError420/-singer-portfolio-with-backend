<?php
// Comprehensive Fix for Album Images
echo "<h1>Comprehensive Album Image Fix</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Check Current Album Data</h2>";
    $query = "SELECT id, title, cover_image FROM albums";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($albums as $album) {
        echo "<p><strong>Album {$album['id']}:</strong> {$album['title']} - Cover: " . ($album['cover_image'] ?: 'NULL') . "</p>";
    }
    
    echo "<h2>Step 2: Check Demo Images</h2>";
    $demoImages = [
        1 => '../public/images/demo/album1.jpg',
        2 => '../public/images/demo/album2.jpg', 
        3 => '../public/images/demo/album3.jpg'
    ];
    
    foreach ($demoImages as $albumId => $imagePath) {
        // Check different possible paths
        $paths = [
            __DIR__ . '/public/images/demo/album' . $albumId . '.jpg',
            __DIR__ . '/../public/images/demo/album' . $albumId . '.jpg',
            __DIR__ . '/../../public/images/demo/album' . $albumId . '.jpg'
        ];
        
        $foundPath = null;
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $foundPath = $path;
                echo "<p>✅ Album $albumId: Found at " . htmlspecialchars($path) . "</p>";
                break;
            }
        }
        
        if (!$foundPath) {
            echo "<p>❌ Album $albumId: No demo image found</p>";
        }
    }
    
    echo "<h2>Step 3: Create Demo Images if Missing</h2>";
    $demoDir = __DIR__ . '/../public/images/demo/';
    if (!is_dir($demoDir)) {
        mkdir($demoDir, 0777, true);
        echo "<p>✅ Created demo directory</p>";
    }
    
    // Create simple placeholder images if they don't exist
    for ($i = 1; $i <= 3; $i++) {
        $imagePath = $demoDir . 'album' . $i . '.jpg';
        if (!file_exists($imagePath)) {
            // Create a simple colored placeholder
            $img = imagecreatetruecolor(300, 300);
            $colors = [
                imagecolorallocate($img, 255, 99, 71),   // Tomato
                imagecolorallocate($img, 60, 179, 113),  // Medium Sea Green
                imagecolorallocate($img, 106, 90, 205)   // Slate Blue
            ];
            $color = $colors[$i - 1];
            imagefill($img, 0, 0, $color);
            
            // Add text
            $textColor = imagecolorallocate($img, 255, 255, 255);
            $text = "Album " . $i;
            imagettftext($img, 20, 0, 80, 150, $textColor, __DIR__ . '/arial.ttf', $text);
            
            imagejpeg($img, $imagePath);
            imagedestroy($img);
            echo "<p>✅ Created demo image for Album $i</p>";
        } else {
            echo "<p>✅ Demo image for Album $i already exists</p>";
        }
    }
    
    echo "<h2>Step 4: Update Database with Correct Paths</h2>";
    $correctPaths = [
        1 => 'public/images/demo/album1.jpg',
        2 => 'public/images/demo/album2.jpg',
        3 => 'public/images/demo/album3.jpg'
    ];
    
    foreach ($correctPaths as $albumId => $imagePath) {
        $query = "UPDATE albums SET cover_image = :cover_image WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':cover_image', $imagePath);
        $stmt->bindParam(':id', $albumId);
        
        if ($stmt->execute()) {
            echo "<p>✅ Updated Album $albumId with path: $imagePath</p>";
        } else {
            echo "<p>❌ Failed to update Album $albumId</p>";
        }
    }
    
    echo "<h2>Step 5: Test Image Paths</h2>";
    foreach ($correctPaths as $albumId => $imagePath) {
        $fullPath = __DIR__ . '/../' . $imagePath;
        $exists = file_exists($fullPath);
        echo "<p>Album $albumId: $imagePath - " . ($exists ? '✅ File exists' : '❌ File missing') . "</p>";
        
        if ($exists) {
            echo "<img src='../$imagePath' width='60' height='60' style='border: 1px solid #ccc; margin: 5px;' onerror='this.style.border=\"2px solid red\"'>";
        }
    }
    
    echo "<h2 style='color: green;'>Fix Complete!</h2>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li><a href='admin/albums.php' target='_blank'>Open Albums Dashboard</a></li>";
    echo "<li>Refresh the page if images still don't show</li>";
    echo "<li>Check browser console for any image loading errors</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Fix Failed!</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
