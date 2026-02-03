<?php
// Test Singles API
echo "<h1>🎵 Singles API Test</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Check Singles Table</h2>";
    
    // Check if singles table exists
    $tableCheck = $db->query("SHOW TABLES LIKE 'singles'");
    $tableExists = $tableCheck->rowCount() > 0;
    
    echo "<p>Singles table exists: " . ($tableExists ? '✅ Yes' : '❌ No') . "</p>";
    
    if (!$tableExists) {
        echo "<h2>Creating Singles Table</h2>";
        
        $createTableSQL = "CREATE TABLE IF NOT EXISTS singles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            duration VARCHAR(50) DEFAULT NULL,
            artist VARCHAR(255) DEFAULT NULL,
            cover_image VARCHAR(500) DEFAULT NULL,
            release_date DATE DEFAULT NULL,
            audio_file VARCHAR(500) DEFAULT NULL,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $db->exec($createTableSQL);
        echo "<p>✅ Singles table created</p>";
        
        // Insert some demo singles
        $insertSQL = "INSERT INTO singles (title, duration, artist, cover_image, release_date, status) VALUES 
                      ('Demo Single 1', '3:45', 'Demo Artist', 'uploads/demo_single1.jpg', '2026-01-15', 'active'),
                      ('Demo Single 2', '4:20', 'Demo Artist', 'uploads/demo_single2.jpg', '2026-02-01', 'active')";
        
        $db->exec($insertSQL);
        echo "<p>✅ Demo singles inserted</p>";
    }
    
    echo "<h2>Step 2: Test Singles API Directly</h2>";
    
    // Test the singles API directly
    ob_start();
    include __DIR__ . '/api/singles.php';
    $apiOutput = ob_get_clean();
    
    echo "<h3>Singles API Output:</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars($apiOutput) . "</pre>";
    
    $singlesData = json_decode($apiOutput, true);
    
    if ($singlesData !== null) {
        echo "<p style='color: green;'>✅ Singles API returns valid JSON</p>";
        echo "<p>Found " . count($singlesData) . " singles</p>";
        
        if (count($singlesData) > 0) {
            echo "<h3>Sample Singles:</h3>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Title</th><th>Artist</th><th>Duration</th><th>Status</th></tr>";
            
            foreach ($singlesData as $single) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($single['title']) . "</td>";
                echo "<td>" . htmlspecialchars($single['artist'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($single['duration'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($single['status']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>❌ Singles API does not return valid JSON</p>";
        echo "<p>Raw output: " . htmlspecialchars(substr($apiOutput, 0, 200)) . "...</p>";
    }
    
    echo "<h2>Step 3: Test Singles API via Browser</h2>";
    echo "<p><a href='api/singles.php' target='_blank'>Test Singles API in Browser</a></p>";
    
    echo "<h2>Step 4: React Integration</h2>";
    echo "<p><strong>For React:</strong></p>";
    echo "<ul>";
    echo "<li>If singles table exists and API works, React should load singles</li>";
    echo "<li>If no singles exist, React will get empty array</li>";
    echo "<li>Empty singles array is normal for new installations</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
