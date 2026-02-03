<?php
// Direct PHP Test (doesn't require Apache web server)
echo "<h1>🔍 Direct PHP Test</h1>";

echo "<h2>PHP Environment</h2>";
echo "<p><strong>PHP Version:</strong> " . htmlspecialchars(phpversion()) . "</p>";
echo "<p><strong>Current Directory:</strong> " . htmlspecialchars(getcwd()) . "</p>";
echo "<p><strong>File Path:</strong> " . htmlspecialchars(__FILE__) . "</p>";

echo "<h2>File System Check</h2>";

$backendDir = __DIR__;
echo "<p><strong>Backend Directory:</strong> " . htmlspecialchars($backendDir) . "</p>";

$apiDir = $backendDir . '/api';
echo "<p><strong>API Directory:</strong> " . htmlspecialchars($apiDir) . "</p>";
echo "<p><strong>API Directory Exists:</strong> " . (is_dir($apiDir) ? '✅ Yes' : '❌ No') . "</p>";

if (is_dir($apiDir)) {
    $files = scandir($apiDir);
    echo "<p><strong>Files in API Directory:</strong></p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $apiDir . '/' . $file;
            $exists = file_exists($filePath);
            echo "<li>" . htmlspecialchars($file) . " - " . ($exists ? '✅' : '❌') . "</li>";
        }
    }
    echo "</ul>";
}

echo "<h2>Database Test</h2>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    $query = "SELECT COUNT(*) as count FROM albums WHERE status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<p>Found {$result['count']} active albums in database</p>";
    
    if ($result['count'] > 0) {
        $query = "SELECT id, title, year, category FROM albums WHERE status = 'active' LIMIT 3";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Sample Albums:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Year</th><th>Category</th></tr>";
        foreach ($albums as $album) {
            echo "<tr>";
            echo "<td>{$album['id']}</td>";
            echo "<td>" . htmlspecialchars($album['title']) . "</td>";
            echo "<td>{$album['year']}</td>";
            echo "<td>" . htmlspecialchars($album['category']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>API File Test</h2>";

$albumsFile = $apiDir . '/albums.php';
if (file_exists($albumsFile)) {
    echo "<p>✅ albums.php file exists</p>";
    
    // Test if the PHP file has syntax errors
    $output = [];
    $returnCode = 0;
    exec("php -l " . escapeshellarg($albumsFile), $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "<p style='color: green;'>✅ albums.php has no syntax errors</p>";
    } else {
        echo "<p style='color: red;'>❌ albums.php has syntax errors:</p>";
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    }
    
    // Try to include the file (this will test if it runs without errors)
    echo "<h3>Testing albums.php execution:</h3>";
    
    // Backup server variables
    $originalMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $originalGet = $_GET;
    
    // Set up test request
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET = [];
    
    try {
        ob_start();
        include $albumsFile;
        $apiOutput = ob_get_clean();
        
        echo "<p style='color: green;'>✅ albums.php executes successfully</p>";
        echo "<h4>API Output:</h4>";
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars($apiOutput) . "</pre>";
        
        $data = json_decode($apiOutput, true);
        if ($data !== null) {
            echo "<p style='color: green;'>✅ API returns valid JSON</p>";
            echo "<p>API returned " . count($data) . " albums</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ API does not return valid JSON</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ albums.php execution error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Restore original variables
    $_SERVER['REQUEST_METHOD'] = $originalMethod;
    $_GET = $originalGet;
    
} else {
    echo "<p style='color: red;'>❌ albums.php file not found</p>";
}

echo "<h2>Apache/XAMPP Status</h2>";
echo "<p><strong>To check if Apache is running:</strong></p>";
echo "<ol>";
echo "<li>Open XAMPP Control Panel</li>";
echo "<li>Check if Apache is green (running)</li>";
echo "<li>Note the port number next to Apache</li>";
echo "<li>If Apache is red, click 'Start'</li>";
echo "</ol>";

echo "<h2>Troubleshooting Steps</h2>";
echo "<p><strong>If this PHP file works but Apache doesn't:</strong></p>";
echo "<ol>";
echo "<li>Apache is not running or misconfigured</li>";
echo "<li>Check XAMPP Control Panel</li>";
echo "<li>Try starting Apache on a different port</li>";
echo "<li>Check for port conflicts (Skype, etc.)</li>";
echo "</ol>";

echo "<p><strong>If this PHP file doesn't work:</strong></p>";
echo "<ol>";
echo "<li>PHP is not installed or not in PATH</li>";
echo "<li>Run this with: php " . htmlspecialchars(__FILE__) . "</li>";
echo "<li>Install/reinstall XAMPP</li>";
echo "</ol>";
?>
