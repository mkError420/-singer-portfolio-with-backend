<!DOCTYPE html>
<html>
<head>
    <title>Server Status Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🖥️ Server Status Check</h1>
    
    <div class="status info">
        <strong>Current Directory:</strong> <?php echo htmlspecialchars(getcwd()); ?>
    </div>
    
    <div class="status info">
        <strong>Document Root:</strong> <?php echo htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'Not set'); ?>
    </div>
    
    <div class="status info">
        <strong>Server Software:</strong> <?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Not set'); ?>
    </div>
    
    <div class="status info">
        <strong>PHP Version:</strong> <?php echo htmlspecialchars(phpversion()); ?>
    </div>
    
    <div class="status info">
        <strong>Request URI:</strong> <?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'Not set'); ?>
    </div>
    
    <h2>📁 File Structure Check</h2>
    
    <?php
    $backendDir = __DIR__;
    $apiDir = $backendDir . '/api';
    
    echo "<div class='status " . (is_dir($backendDir) ? 'success' : 'error') . "'>";
    echo "<strong>Backend Directory:</strong> " . htmlspecialchars($backendDir) . " - " . (is_dir($backendDir) ? "✅ Exists" : "❌ Missing");
    echo "</div>";
    
    echo "<div class='status " . (is_dir($apiDir) ? 'success' : 'error') . "'>";
    echo "<strong>API Directory:</strong> " . htmlspecialchars($apiDir) . " - " . (is_dir($apiDir) ? "✅ Exists" : "❌ Missing");
    echo "</div>";
    
    if (is_dir($apiDir)) {
        echo "<h3>API Files:</h3>";
        $files = scandir($apiDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $apiDir . '/' . $file;
                $exists = file_exists($filePath);
                echo "<div class='status " . ($exists ? 'success' : 'error') . "'>";
                echo "<strong>$file</strong> - " . ($exists ? "✅ Exists" : "❌ Missing");
                echo "</div>";
            }
        }
    }
    ?>
    
    <h2>🔗 URL Tests</h2>
    
    <div class="status info">
        <p>Test these URLs directly:</p>
        <ul>
            <li><a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?test=albums" target="_blank">Test Albums API</a></li>
            <li><a href="../api/albums.php" target="_blank">Direct Albums API</a></li>
            <li><a href="api/albums.php" target="_blank">Relative Albums API</a></li>
            <li><a href="api/cors_test.php" target="_blank">CORS Test</a></li>
        </ul>
    </div>
    
    <?php
    if (isset($_GET['test']) && $_GET['test'] === 'albums') {
        echo "<h2>🎵 Albums API Test</h2>";
        
        try {
            require_once __DIR__ . '/config/database.php';
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT COUNT(*) as count FROM albums WHERE status = 'active'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<div class='status success'>";
            echo "<strong>Database Connection:</strong> ✅ Success<br>";
            echo "<strong>Albums Found:</strong> " . $result['count'];
            echo "</div>";
            
            // Test API file
            ob_start();
            include __DIR__ . '/api/albums.php';
            $apiOutput = ob_get_clean();
            
            echo "<h3>API Output:</h3>";
            echo "<pre>" . htmlspecialchars($apiOutput) . "</pre>";
            
            $data = json_decode($apiOutput, true);
            if ($data !== null) {
                echo "<div class='status success'>";
                echo "<strong>API Status:</strong> ✅ Returns valid JSON<br>";
                echo "<strong>Albums Returned:</strong> " . count($data);
                echo "</div>";
            } else {
                echo "<div class='status error'>";
                echo "<strong>API Status:</strong> ❌ Invalid JSON";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='status error'>";
            echo "<strong>Error:</strong> " . htmlspecialchars($e->getMessage());
            echo "</div>";
        }
    }
    ?>
    
    <h2>🔧 Troubleshooting</h2>
    
    <div class="status info">
        <strong>If URLs don't work:</strong>
        <ol>
            <li>Make sure XAMPP Apache is running (green in XAMPP Control Panel)</li>
            <li>Check that files are in: C:/xampp/htdocs/madam-portfolio/backend/api/</li>
            <li>Try accessing: http://localhost/dashboard/ to verify XAMPP works</li>
            <li>Check XAMPP Apache error logs</li>
            <li>Make sure .htaccess files aren't blocking access</li>
        </ol>
    </div>
    
    <div class="status info">
        <strong>XAMPP Paths:</strong><br>
        Expected: C:/xampp/htdocs/madam-portfolio/backend/api/<br>
        Current: <?php echo htmlspecialchars(__DIR__); ?>/api/
    </div>
    
    <script>
        console.log('Server status check loaded');
        console.log('Current URL:', window.location.href);
        console.log('Document root:', '<?php echo htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? ''); ?>');
    </script>
</body>
</html>
