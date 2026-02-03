<?php
// Debug Image Paths
echo "<h1>Debug Image Paths</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }
    
    echo "<h3>Albums Table:</h3>";
    $query = "SELECT id, title, cover_image FROM albums";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Cover Image Path</th><th>File Exists?</th><th>Test Image</th></tr>";
    
    foreach ($albums as $album) {
        $imagePath = $album['cover_image'];
        $fullPath = __DIR__ . '/' . $imagePath;
        $webPath = $imagePath;
        
        $fileExists = file_exists($fullPath);
        $existsText = $fileExists ? '✅ Yes' : '❌ No';
        
        echo "<tr>";
        echo "<td>{$album['id']}</td>";
        echo "<td>{$album['title']}</td>";
        echo "<td>" . htmlspecialchars($imagePath) . "</td>";
        echo "<td>$existsText</td>";
        echo "<td>";
        if ($imagePath) {
            echo "<img src='$webPath' width='60' height='60' style='object-fit: cover;' onerror='this.style.border=\"2px solid red\"'>";
        } else {
            echo "No image";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Singles Table:</h3>";
    $query = "SELECT id, title, cover_image FROM singles";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $singles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Cover Image Path</th><th>File Exists?</th><th>Test Image</th></tr>";
    
    foreach ($singles as $single) {
        $imagePath = $single['cover_image'];
        $fullPath = __DIR__ . '/' . $imagePath;
        $webPath = $imagePath;
        
        $fileExists = file_exists($fullPath);
        $existsText = $fileExists ? '✅ Yes' : '❌ No';
        
        echo "<tr>";
        echo "<td>{$single['id']}</td>";
        echo "<td>{$single['title']}</td>";
        echo "<td>" . htmlspecialchars($imagePath) . "</td>";
        echo "<td>$existsText</td>";
        echo "<td>";
        if ($imagePath) {
            echo "<img src='$webPath' width='60' height='60' style='object-fit: cover;' onerror='this.style.border=\"2px solid red\"'>";
        } else {
            echo "No image";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Upload Directory Check:</h3>";
    $uploadDir = __DIR__ . '/uploads';
    if (is_dir($uploadDir)) {
        echo "<p>✅ Upload directory exists: $uploadDir</p>";
        $files = scandir($uploadDir);
        echo "<p>Files in upload directory:</p>";
        echo "<ul>";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>❌ Upload directory does not exist: $uploadDir</p>";
    }
    
    echo "<h3>Test Different Path Formats:</h3>";
    if (!empty($albums)) {
        $firstAlbum = $albums[0];
        if ($firstAlbum['cover_image']) {
            $path = $firstAlbum['cover_image'];
            echo "<p>Original path: " . htmlspecialchars($path) . "</p>";
            echo "<p>With leading slash: /" . htmlspecialchars($path) . "</p>";
            echo "<p>With ../: ../" . htmlspecialchars($path) . "</p>";
            
            echo "<h4>Test Images:</h4>";
            echo "<p>Original: <img src='$path' width='60' height='60' style='object-fit: cover; border: 1px solid #ccc;' onerror='this.style.border=\"2px solid red\"'></p>";
            echo "<p>With slash: <img src='/$path' width='60' height='60' style='object-fit: cover; border: 1px solid #ccc;' onerror='this.style.border=\"2px solid red\"'></p>";
            echo "<p>With ../: <img src='../$path' width='60' height='60' style='object-fit: cover; border: 1px solid #ccc;' onerror='this.style.border=\"2px solid red\"'></p>";
        }
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Debug Failed!</h2>";
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
