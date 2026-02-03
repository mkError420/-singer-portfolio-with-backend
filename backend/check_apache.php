<?php
// Check Apache Configuration
echo "<h1>🖥️ Apache Configuration Check</h1>";

echo "<h2>Server Information</h2>";
echo "<p><strong>Server Software:</strong> " . htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p><strong>Server Port:</strong> " . htmlspecialchars($_SERVER['SERVER_PORT'] ?? 'Unknown') . "</p>";
echo "<p><strong>Request URI:</strong> " . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'Unknown') . "</p>";
echo "<p><strong>Script Name:</strong> " . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "</p>";

echo "<h2>Directory Structure</h2>";
$currentDir = getcwd();
echo "<p><strong>Current Directory:</strong> " . htmlspecialchars($currentDir) . "</p>";

$apiDir = $currentDir . '/api';
echo "<p><strong>API Directory:</strong> " . htmlspecialchars($apiDir) . "</p>";
echo "<p><strong>API Directory Exists:</strong> " . (is_dir($apiDir) ? '✅ Yes' : '❌ No') . "</p>";

if (is_dir($apiDir)) {
    $files = scandir($apiDir);
    echo "<p><strong>API Files:</strong></p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<li>" . htmlspecialchars($file) . "</li>";
        }
    }
    echo "</ul>";
}

echo "<h2>API Test</h2>";

// Test albums API
try {
    ob_start();
    include __DIR__ . '/api/albums.php';
    $apiOutput = ob_get_clean();
    
    echo "<p><strong>Albums API Output:</strong></p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars($apiOutput) . "</pre>";
    
    $data = json_decode($apiOutput, true);
    if ($data !== null) {
        echo "<p style='color: green;'>✅ Albums API returns valid JSON</p>";
        echo "<p>Found " . count($data) . " albums</p>";
    } else {
        echo "<p style='color: red;'>❌ Albums API does not return valid JSON</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Albums API Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>URL Tests</h2>";
echo "<p>Test these URLs directly:</p>";
echo "<ul>";
echo "<li><a href='" . htmlspecialchars($_SERVER['PHP_SELF']) . "?test=albums' target='_blank'>Test Albums API</a></li>";
echo "<li><a href='../api/albums.php' target='_blank'>Direct Albums API</a></li>";
echo "<li><a href='api/albums.php' target='_blank'>Relative Albums API</a></li>";
echo "</ul>";

if (isset($_GET['test']) && $_GET['test'] === 'albums') {
    echo "<h3>Albums API Test Result:</h3>";
    try {
        require_once __DIR__ . '/config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT COUNT(*) as count FROM albums WHERE status = 'active'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<p style='color: green;'>✅ Database connection successful</p>";
        echo "<p>Found {$result['count']} active albums</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<h2>Troubleshooting</h2>";
echo "<p><strong>If API URLs don't work:</strong></p>";
echo "<ul>";
echo "<li>Check if Apache is running on port 80443</li>";
echo "<li>Verify files exist in correct location</li>";
echo "<li>Check Apache error logs</li>";
echo "<li>Test with: http://localhost:80443/madam-portfolio/backend/api/albums.php</li>";
echo "</ul>";
?>
