<!DOCTYPE html>
<html>
<head>
    <title>Check Uploads Folder</title>
</head>
<body>
    <h1>📁 Uploads Folder Check</h1>
    
    <?php
    $uploadsDir = __DIR__ . '/uploads';
    
    echo "<h2>Uploads Directory Check</h2>";
    echo "<p><strong>Path:</strong> " . htmlspecialchars($uploadsDir) . "</p>";
    
    if (file_exists($uploadsDir)) {
        echo "<p style='color: green;'>✅ Uploads folder exists</p>";
        
        if (is_dir($uploadsDir)) {
            echo "<p style='color: green;'>✅ It's a directory</p>";
            
            // Check if it's readable
            if (is_readable($uploadsDir)) {
                echo "<p style='color: green;'>✅ Directory is readable</p>";
                
                // List files
                $files = scandir($uploadsDir);
                $imageFiles = array_filter($files, function($file) use ($uploadsDir) {
                    $filePath = $uploadsDir . '/' . $file;
                    return is_file($filePath) && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                });
                
                echo "<h3>Image Files Found (" . count($imageFiles) . "):</h3>";
                
                if (empty($imageFiles)) {
                    echo "<p style='color: orange;'>⚠️ No image files found in uploads folder</p>";
                } else {
                    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
                    echo "<tr><th>File Name</th><th>File Size</th><th>Test URL</th><th>Preview</th></tr>";
                    
                    foreach ($imageFiles as $file) {
                        $filePath = $uploadsDir . '/' . $file;
                        $fileSize = filesize($filePath);
                        $testUrl = '/madam-portfolio/backend/uploads/' . $file;
                        
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($file) . "</td>";
                        echo "<td>" . number_format($fileSize / 1024, 2) . " KB</td>";
                        echo "<td><a href='" . $testUrl . "' target='_blank'>" . htmlspecialchars($testUrl) . "</a></td>";
                        echo "<td><img src='" . $testUrl . "' style='width: 50px; height: 50px; object-fit: cover;' onerror=\"this.style.display='none'; this.nextElementSibling.style.display='inline';\" />";
                        echo "<span style='display: none; color: red;'>❌ Failed to load</span></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            } else {
                echo "<p style='color: red;'>❌ Directory is not readable</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ It's not a directory</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Uploads folder does not exist</p>";
        
        // Try to create it
        if (mkdir($uploadsDir, 0755, true)) {
            echo "<p style='color: green;'>✅ Created uploads folder</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create uploads folder</p>";
        }
    }
    
    echo "<h2>Apache Configuration Check</h2>";
    echo "<p><strong>Current working directory:</strong> " . htmlspecialchars(getcwd()) . "</p>";
    echo "<p><strong>Document Root:</strong> " . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "</p>";
    echo "<p><strong>Script Name:</strong> " . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "</p>";
    
    echo "<h2>Solutions</h2>";
    echo "<ol>";
    echo "<li><strong>If folder doesn't exist:</strong> Create the uploads folder</li>";
    echo "<li><strong>If folder exists but no images:</strong> Upload some images through the admin panel</li>";
    echo "<li><strong>If images exist but don't load:</strong> Check Apache configuration for uploads folder access</li>";
    echo "<li><strong>If URLs work directly:</strong> The React image URLs should work now</li>";
    echo "</ol>";
    ?>
    
</body>
</html>
