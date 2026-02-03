<?php
// Check .htaccess files
echo "<h1>🔍 .htaccess Check</h1>";

echo "<h2>Looking for .htaccess files...</h2>";

function findHtaccessFiles($dir) {
    $htaccessFiles = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getFilename() === '.htaccess') {
            $htaccessFiles[] = $file->getPathname();
        }
    }
    
    return $htaccessFiles;
}

$currentDir = __DIR__;
$htaccessFiles = findHtaccessFiles($currentDir);

if (empty($htaccessFiles)) {
    echo "<p style='color: green;'>✅ No .htaccess files found</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Found " . count($htaccessFiles) . " .htaccess files:</p>";
    echo "<ul>";
    foreach ($htaccessFiles as $file) {
        echo "<li><strong>" . htmlspecialchars($file) . "</strong></li>";
        
        // Show content of each .htaccess
        if (file_exists($file)) {
            $content = file_get_contents($file);
            echo "<pre style='background: #f5f5f5; padding: 10px; margin: 5px 0; font-size: 12px;'>";
            echo htmlspecialchars($content);
            echo "</pre>";
        }
    }
    echo "</ul>";
}

echo "<h2>Test Direct PHP Execution</h2>";

// Test if we can execute albums.php directly
$albumsFile = __DIR__ . '/api/albums.php';
if (file_exists($albumsFile)) {
    echo "<p>✅ albums.php file exists</p>";
    
    // Check if PHP can parse it
    $output = [];
    $returnCode = 0;
    exec("php -l " . escapeshellarg($albumsFile), $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "<p style='color: green;'>✅ albums.php has no syntax errors</p>";
    } else {
        echo "<p style='color: red;'>❌ albums.php has syntax errors:</p>";
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    }
    
    // Try to include it directly
    echo "<h3>Testing direct execution:</h3>";
    
    // Backup and set up environment
    $originalMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $originalGet = $_GET;
    
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET = [];
    
    try {
        ob_start();
        include $albumsFile;
        $apiOutput = ob_get_clean();
        
        echo "<p style='color: green;'>✅ albums.php executes successfully</p>";
        echo "<h4>Output:</h4>";
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars($apiOutput) . "</pre>";
        
        $data = json_decode($apiOutput, true);
        if ($data !== null) {
            echo "<p style='color: green;'>✅ Returns valid JSON (" . count($data) . " albums)</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Does not return valid JSON</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Execution error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Restore environment
    $_SERVER['REQUEST_METHOD'] = $originalMethod;
    $_GET = $originalGet;
    
} else {
    echo "<p style='color: red;'>❌ albums.php file not found</p>";
}

echo "<h2>Apache Configuration Check</h2>";
echo "<p><strong>Possible issues:</strong></p>";
echo "<ol>";
echo "<li>.htaccess file blocking access</li>";
echo "<li>Apache mod_rewrite not enabled</li>";
echo "<li>File permissions issue</li>";
echo "<li>Apache configuration blocking PHP execution</li>";
echo "</ol>";

echo "<h2>Solutions</h2>";
echo "<p><strong>If .htaccess is blocking:</strong></p>";
echo "<ul>";
echo "<li>Rename or remove problematic .htaccess files</li>";
echo "<li>Add 'Options +Indexes' to allow directory access</li>";
echo "<li>Add 'AddType application/x-httpd-php .php' to allow PHP execution</li>";
echo "</ul>";

echo "<p><strong>If Apache configuration is the issue:</strong></p>";
echo "<ul>";
echo "<li>Check XAMPP Apache error logs</li>";
echo "<li>Restart Apache service</li>";
echo "<li>Check httpd.conf for PHP handler configuration</li>";
echo "</ul>";

echo "<h2>Quick Test - Try accessing via different methods</h2>";
echo "<p>Test these URLs:</p>";
echo "<ul>";
echo "<li><a href='http://localhost/madam-portfolio/backend/api/albums.php' target='_blank'>Direct URL</a></li>";
echo "<li><a href='http://localhost/madam-portfolio/backend/test_port80.php' target='_blank'>Port 80 Test</a></li>";
echo "<li><a href='http://localhost/madam-portfolio/backend/direct_php_test.php' target='_blank'>Direct PHP Test</a></li>";
echo "</ul>";
?>
