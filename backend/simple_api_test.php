<?php
// Simple API Test - Access via browser
echo "<h1>API Status Check</h1>";

echo "<h2>1. XAMPP Status</h2>";
if (isset($_SERVER['SERVER_SOFTWARE'])) {
    echo "<p>✅ Web server: " . htmlspecialchars($_SERVER['SERVER_SOFTWARE']) . "</p>";
    echo "<p>✅ PHP version: " . htmlspecialchars(phpversion()) . "</p>";
} else {
    echo "<p>❌ Not running through web server</p>";
}

echo "<h2>2. File Structure</h2>";
$apiDir = __DIR__ . '/api';
if (is_dir($apiDir)) {
    echo "<p>✅ API directory exists: " . htmlspecialchars($apiDir) . "</p>";
    
    $files = scandir($apiDir);
    echo "<p>API files found:</p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<li>" . htmlspecialchars($file) . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>❌ API directory not found</p>";
}

echo "<h2>3. Albums API Test</h2>";
$albumsFile = $apiDir . '/albums.php';
if (file_exists($albumsFile)) {
    echo "<p>✅ albums.php exists</p>";
    
    // Try to execute albums API
    try {
        // Backup original server vars
        $originalMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $originalGet = $_GET;
        
        // Set up test request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
        
        // Capture output
        ob_start();
        include $albumsFile;
        $output = ob_get_clean();
        
        echo "<p>✅ Albums API executed successfully</p>";
        echo "<h4>Output:</h4>";
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars($output) . "</pre>";
        
        // Check if output is valid JSON
        $data = json_decode($output, true);
        if ($data !== null) {
            echo "<p>✅ Returns valid JSON</p>";
            if (is_array($data)) {
                echo "<p>✅ Returns " . count($data) . " albums</p>";
            }
        } else {
            echo "<p>⚠️ Output is not valid JSON</p>";
        }
        
        // Restore original vars
        $_SERVER['REQUEST_METHOD'] = $originalMethod;
        $_GET = $originalGet;
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error executing albums API: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ albums.php not found</p>";
}

echo "<h2>4. CORS Headers Test</h2>";
$corsTestFile = $apiDir . '/cors_test.php';
if (file_exists($corsTestFile)) {
    echo "<p>✅ CORS test file exists</p>";
    
    try {
        ob_start();
        include $corsTestFile;
        $corsOutput = ob_get_clean();
        
        echo "<p>✅ CORS test executed</p>";
        echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($corsOutput) . "</pre>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ CORS test error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ CORS test file not found</p>";
}

echo "<h2>5. Database Test</h2>";
try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    echo "<p>✅ Database connected</p>";
    
    $query = "SELECT COUNT(*) as count FROM albums";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>✅ Found {$result['count']} albums in database</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<h2>6. Frontend Connection Test</h2>";
echo "<p>Test this URL in your browser:</p>";
echo "<code>http://localhost/madam-portfolio/backend/api/albums.php</code>";
echo "<br><br>";
echo "<p>If you see JSON output above, the API is working.</p>";
echo "<p>If you get errors, there's a server configuration issue.</p>";

echo "<h2>7. Manual Frontend Test</h2>";
echo "<p>Open browser console (F12) and run:</p>";
echo "<pre style='background: #f5f5f5; padding: 10px;'>";
echo "fetch('http://localhost/madam-portfolio/backend/api/albums.php')";
echo "  .then(response => response.json())";
echo "  .then(data => console.log('Success:', data))";
echo "  .catch(error => console.error('Error:', error));";
echo "</pre>";

echo "<h2>Troubleshooting Steps:</h2>";
echo "<ol>";
echo "<li>Check if XAMPP Apache is running (green indicator in XAMPP control panel)</li>";
echo "<li>Try accessing: http://localhost/dashboard/</li>";
echo "<li>Try accessing: http://localhost/madam-portfolio/backend/api/albums.php</li>";
echo "<li>If step 3 works, the issue is CORS - if not, it's server configuration</li>";
echo "<li>Check XAMPP Apache logs for errors</li>";
echo "</ol>";

echo "<h2>Current Working Directory:</h2>";
echo "<p>" . htmlspecialchars(getcwd()) . "</p>";
echo "<h2>Document Root:</h2>";
echo "<p>" . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "</p>";
?>
