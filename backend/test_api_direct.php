<?php
// Test API Direct Access
echo "<h1>Direct API Test</h1>";

echo "<h2>Step 1: Test Direct File Access</h2>";

// Test if albums.php file exists and is accessible
$albumsFile = __DIR__ . '/api/albums.php';
if (file_exists($albumsFile)) {
    echo "<p>✅ albums.php file exists at: " . htmlspecialchars($albumsFile) . "</p>";
    
    // Try to include it directly to test for PHP errors
    try {
        echo "<h3>Testing direct PHP execution:</h3>";
        
        // Simulate a GET request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
        
        // Capture output
        ob_start();
        include $albumsFile;
        $output = ob_get_clean();
        
        echo "<p>✅ PHP file executes without errors</p>";
        echo "<h4>Output:</h4>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ PHP execution error: " . $e->getMessage() . "</p>";
    } catch (Error $e) {
        echo "<p style='color: red;'>❌ PHP fatal error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ albums.php file not found!</p>";
}

echo "<h2>Step 2: Test Database Connection</h2>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    echo "<p>✅ Database connection successful</p>";
    
    // Test a simple query
    $query = "SELECT COUNT(*) as count FROM albums";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>✅ Database query successful - Found {$result['count']} albums</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<h2>Step 3: Test Web Server Access</h2>";

// Test if the web server can access the API
$apiUrl = 'http://localhost/madam-portfolio/backend/api/albums.php';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json',
        'timeout' => 10
    ]
]);

echo "<p>Testing URL: " . htmlspecialchars($apiUrl) . "</p>";

$response = @file_get_contents($apiUrl, false, $context);

if ($response === false) {
    echo "<p style='color: red;'>❌ Web server cannot access API</p>";
    
    // Check if XAMPP is running
    $xamppTest = @file_get_contents('http://localhost/dashboard/');
    if ($xamppTest === false) {
        echo "<p style='color: red;'>❌ XAMPP may not be running - Cannot access localhost</p>";
        echo "<p><strong>Solution:</strong> Start XAMPP Apache server</p>";
    } else {
        echo "<p>✅ XAMPP is running but API not accessible</p>";
    }
    
    // Show HTTP error if available
    if (isset($http_response_header)) {
        echo "<h4>HTTP Response Headers:</h4>";
        echo "<pre>";
        foreach ($http_response_header as $header) {
            echo htmlspecialchars($header) . "\n";
        }
        echo "</pre>";
    }
} else {
    echo "<p>✅ Web server can access API</p>";
    echo "<h4>Response:</h4>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    // Check if it's valid JSON
    $data = json_decode($response, true);
    if ($data !== null) {
        echo "<p>✅ API returns valid JSON</p>";
        if (is_array($data)) {
            echo "<p>✅ API returned " . count($data) . " items</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ API did not return valid JSON</p>";
    }
}

echo "<h2>Step 4: Check .htaccess Issues</h2>";

$htaccessFile = __DIR__ . '/.htaccess';
if (file_exists($htaccessFile)) {
    echo "<p>⚠️ .htaccess file found - checking contents:</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents($htaccessFile)) . "</pre>";
} else {
    echo "<p>✅ No .htaccess file in backend directory</p>";
}

$apiHtaccess = __DIR__ . '/api/.htaccess';
if (file_exists($apiHtaccess)) {
    echo "<p>⚠️ .htaccess file found in api directory:</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents($apiHtaccess)) . "</pre>";
} else {
    echo "<p>✅ No .htaccess file in api directory</p>";
}

echo "<h2>Step 5: Manual CORS Test</h2>";

// Create a simple test file with CORS headers
$testCorsContent = '<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

echo json_encode(["message" => "CORS test successful", "timestamp" => date("Y-m-d H:i:s")]);
?>';

file_put_contents(__DIR__ . '/api/cors_test.php', $testCorsContent);

echo "<p>Created cors_test.php - testing now...</p>";

$corsTestUrl = 'http://localhost/madam-portfolio/backend/api/cors_test.php';
$corsResponse = @file_get_contents($corsTestUrl, false, $context);

if ($corsResponse) {
    echo "<p>✅ CORS test successful:</p>";
    echo "<pre>" . htmlspecialchars($corsResponse) . "</pre>";
    
    if (isset($http_response_header)) {
        echo "<h4>CORS Headers:</h4>";
        echo "<pre>";
        foreach ($http_response_header as $header) {
            if (strpos($header, 'Access-Control') !== false) {
                echo htmlspecialchars($header) . "\n";
            }
        }
        echo "</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ CORS test failed</p>";
}

echo "<h2>Solutions</h2>";
echo "<p><strong>If API is not accessible:</strong></p>";
echo "<ol>";
echo "<li>Make sure XAMPP Apache is running</li>";
echo "<li>Check that files are in the correct folder: C:/xampp/htdocs/madam-portfolio/backend/api/</li>";
echo "<li>Try accessing directly: http://localhost/madam-portfolio/backend/api/albums.php</li>";
echo "<li>Check for PHP errors in XAMPP logs</li>";
echo "</ol>";

echo "<p><strong>If CORS still doesn't work:</strong></p>";
echo "<ol>";
echo "<li>Test the cors_test.php endpoint from browser</li>";
echo "<li>Check browser Network tab for actual headers</li>";
echo "<li>Try accessing from different browser</li>";
echo "</ol>";
?>
