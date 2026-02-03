<?php
// Debug API Request - Check what's being sent to albums API
echo "<h1>Debug API Request</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Simulate Exact Frontend Request</h2>";
    
    // Simulate exactly what the frontend sends
    $testAlbum = [
        'title' => 'Debug Album ' . date('His'),
        'year' => date('Y'),
        'category' => 'rock', // This should be preserved
        'description' => 'Debug album to test category preservation',
        'cover_image' => 'uploads/debug_album.jpg',
        'tracks' => []
    ];
    
    echo "<h3>Data being sent to API:</h3>";
    echo "<pre>" . htmlspecialchars(json_encode($testAlbum, JSON_PRETTY_PRINT)) . "</pre>";
    
    // Make the exact same request as frontend
    $postData = json_encode($testAlbum);
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/madam-portfolio/backend/api/albums.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $postData
        ]
    ]);
    
    echo "<h3>Sending request to: $apiUrl</h3>";
    echo "<h3>Raw POST data:</h3>";
    echo "<pre>" . htmlspecialchars($postData) . "</pre>";
    
    $response = file_get_contents($apiUrl, false, $context);
    
    if ($response) {
        echo "<h3>API Response:</h3>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
        
        $result = json_decode($response, true);
        
        if (isset($result['id'])) {
            echo "<p>✅ Album created with ID: {$result['id']}</p>";
            
            // Verify what was actually saved
            $verifyQuery = "SELECT id, title, category FROM albums WHERE id = ?";
            $verifyStmt = $db->prepare($verifyQuery);
            $verifyStmt->execute([$result['id']]);
            $savedAlbum = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($savedAlbum) {
                echo "<h3>What was actually saved in database:</h3>";
                echo "<pre>" . htmlspecialchars(json_encode($savedAlbum, JSON_PRETTY_PRINT)) . "</pre>";
                
                $expectedCategory = $testAlbum['category'];
                $actualCategory = $savedAlbum['category'];
                
                echo "<h3>Category Comparison:</h3>";
                echo "<p>Expected: '$expectedCategory'</p>";
                echo "<p>Actual: '$actualCategory'</p>";
                
                if ($expectedCategory === $actualCategory) {
                    echo "<p style='color: green;'>✅ Category saved correctly!</p>";
                } else {
                    echo "<p style='color: red;'>❌ Category NOT saved correctly!</p>";
                    
                    // Check if it's a string length issue
                    echo "<h3>Debug Info:</h3>";
                    echo "<p>Expected length: " . strlen($expectedCategory) . "</p>";
                    echo "<p>Actual length: " . strlen($actualCategory) . "</p>";
                    echo "<p>Expected bytes: " . bin2hex($expectedCategory) . "</p>";
                    echo "<p>Actual bytes: " . bin2hex($actualCategory) . "</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>❌ Album creation failed</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ API not responding</p>";
    }
    
    echo "<h2>Step 2: Check Error Logs</h2>";
    echo "<p>Check the PHP error logs for debug messages:</p>";
    echo "<ul>";
    echo "<li>Look for 'Album creation data received:' messages</li>";
    echo "<li>Look for 'Category value:' messages</li>";
    echo "<li>Look for 'Prepared values:' messages</li>";
    echo "</ul>";
    
    echo "<h2>Step 3: Manual Database Insert Test</h2>";
    
    // Test direct database insert to rule out database issues
    $directInsert = "INSERT INTO albums (title, year, category, description) VALUES (?, ?, ?, ?)";
    $directStmt = $db->prepare($directInsert);
    
    $directTitle = 'Direct Insert Test ' . date('His');
    $directYear = date('Y');
    $directCategory = 'direct-test';
    $directDescription = 'Direct database insert test';
    
    if ($directStmt->execute([$directTitle, $directYear, $directCategory, $directDescription])) {
        $directId = $db->lastInsertId();
        echo "<p>✅ Direct insert successful with ID: $directId</p>";
        
        // Verify direct insert
        $directVerify = "SELECT title, category FROM albums WHERE id = ?";
        $directVerifyStmt = $db->prepare($directVerify);
        $directVerifyStmt->execute([$directId]);
        $directResult = $directVerifyStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($directResult) {
            echo "<h3>Direct insert result:</h3>";
            echo "<pre>" . htmlspecialchars(json_encode($directResult, JSON_PRETTY_PRINT)) . "</pre>";
            
            if ($directResult['category'] === $directCategory) {
                echo "<p style='color: green;'>✅ Direct insert category saved correctly!</p>";
            } else {
                echo "<p style='color: red;'>❌ Even direct insert failed - database issue!</p>";
            }
        }
    }
    
    echo "<h2>Step 4: Check Albums Table Structure</h2>";
    
    $structureQuery = "DESCRIBE albums";
    $structureStmt = $db->query($structureQuery);
    $columns = $structureStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Albums table structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
