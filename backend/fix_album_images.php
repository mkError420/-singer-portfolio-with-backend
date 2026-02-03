<?php
// Fix Album Images with Demo Images
echo "<h1>Fix Album Images</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    // Demo images from public folder
    $demoImages = [
        1 => 'public/images/demo/album1.jpg',
        2 => 'public/images/demo/album2.jpg', 
        3 => 'public/images/demo/album3.jpg'
    ];
    
    echo "<h3>Updating Albums with Demo Images:</h3>";
    
    foreach ($demoImages as $albumId => $imagePath) {
        // Check if file exists
        $fullPath = __DIR__ . '/../' . $imagePath;
        $fileExists = file_exists($fullPath);
        
        echo "<p><strong>Album $albumId:</strong> $imagePath - " . ($fileExists ? '✅ File exists' : '❌ File missing') . "</p>";
        
        if ($fileExists) {
            // Update album with demo image
            $query = "UPDATE albums SET cover_image = :cover_image WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':cover_image', $imagePath);
            $stmt->bindParam(':id', $albumId);
            
            if ($stmt->execute()) {
                echo "<p>✅ Updated Album $albumId with $imagePath</p>";
            } else {
                echo "<p>❌ Failed to update Album $albumId</p>";
            }
        }
    }
    
    // Also update singles
    echo "<h3>Updating Singles with Demo Images:</h3>";
    
    $singleDemoImages = [
        1 => 'public/images/demo/single1.jpg',
        2 => 'public/images/demo/single2.jpg',
        3 => 'public/images/demo/single3.jpg'
    ];
    
    foreach ($singleDemoImages as $singleId => $imagePath) {
        $fullPath = __DIR__ . '/../' . $imagePath;
        $fileExists = file_exists($fullPath);
        
        echo "<p><strong>Single $singleId:</strong> $imagePath - " . ($fileExists ? '✅ File exists' : '❌ File missing') . "</p>";
        
        if ($fileExists) {
            $query = "UPDATE singles SET cover_image = :cover_image WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':cover_image', $imagePath);
            $stmt->bindParam(':id', $singleId);
            
            if ($stmt->execute()) {
                echo "<p>✅ Updated Single $singleId with $imagePath</p>";
            }
        }
    }
    
    echo "<h2 style='color: green;'>Fix Complete!</h2>";
    echo "<p><a href='admin/albums.php'>Check Albums Dashboard</a></p>";
    echo "<p><a href='admin/singles.php'>Check Singles Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Fix Failed!</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
