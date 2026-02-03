<?php
// Test Categories System
echo "<h1>Categories System Test</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Check Database Table</h2>";
    
    // Check if album_categories table exists
    $tableCheck = $db->query("SHOW TABLES LIKE 'album_categories'");
    if ($tableCheck->rowCount() > 0) {
        echo "<p>✅ album_categories table exists</p>";
        
        // Check current categories
        $query = "SELECT * FROM album_categories ORDER BY name";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Current Categories:</h3>";
        if (count($categories) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Status</th></tr>";
            foreach ($categories as $category) {
                echo "<tr>";
                echo "<td>{$category['id']}</td>";
                echo "<td>" . htmlspecialchars($category['name']) . "</td>";
                echo "<td>" . htmlspecialchars($category['description']) . "</td>";
                echo "<td>{$category['status']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No categories found in table</p>";
        }
    } else {
        echo "<p>❌ album_categories table does not exist</p>";
        echo "<p>Creating table...</p>";
        
        // Create the table
        $createTable = "CREATE TABLE album_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL,
            description TEXT,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $db->exec($createTable);
        echo "<p>✅ Table created</p>";
    }
    
    echo "<h2>Step 2: Insert Default Categories</h2>";
    
    $defaultCategories = [
        ['album', 'Studio Albums - Full-length releases'],
        ['acoustic', 'Acoustic Versions - Unplugged performances'],
        ['live', 'Live Recordings - Concert performances'],
        ['remix', 'Remixes - Reimagined versions'],
        ['compilation', 'Compilations - Collection of tracks'],
        ['ep', 'EPs - Extended plays'],
        ['single', 'Singles - Individual releases']
    ];
    
    foreach ($defaultCategories as $category) {
        $insertQuery = "INSERT IGNORE INTO album_categories (name, description) VALUES (?, ?)";
        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->execute([$category[0], $category[1]]);
    }
    echo "<p>✅ Default categories inserted</p>";
    
    echo "<h2>Step 3: Test Categories API</h2>";
    
    // Test the API
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/madam-portfolio/backend/api/categories.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = file_get_contents($apiUrl, false, $context);
    
    if ($response) {
        echo "<p>✅ API responding</p>";
        $apiData = json_decode($response, true);
        
        if (is_array($apiData)) {
            echo "<p>✅ API returned " . count($apiData) . " categories</p>";
            echo "<h3>API Response:</h3>";
            echo "<pre>" . htmlspecialchars(json_encode($apiData, JSON_PRETTY_PRINT)) . "</pre>";
        } else {
            echo "<p>❌ API returned invalid data</p>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    } else {
        echo "<p>❌ API not responding</p>";
    }
    
    echo "<h2>Step 4: Test Adding New Category</h2>";
    
    $testCategory = [
        'name' => 'Test Category ' . date('His'),
        'description' => 'This is a test category created at ' . date('Y-m-d H:i:s')
    ];
    
    $postData = json_encode($testCategory);
    $postContext = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $postData
        ]
    ]);
    
    $postResponse = file_get_contents($apiUrl, false, $postContext);
    
    if ($postResponse) {
        echo "<p>✅ Category creation API responding</p>";
        $postResult = json_decode($postResponse, true);
        echo "<pre>" . htmlspecialchars(json_encode($postResult, JSON_PRETTY_PRINT)) . "</pre>";
    } else {
        echo "<p>❌ Category creation API not responding</p>";
    }
    
    echo "<h2>Step 5: Verify New Category</h2>";
    
    // Check categories again
    $query = "SELECT * FROM album_categories ORDER BY name";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $finalCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total categories: " . count($finalCategories) . "</p>";
    
    echo "<h2>Next Steps:</h2>";
    echo "<ol>";
    echo "<li><a href='admin/albums.php' target='_blank'>Test Albums Dashboard</a></li>";
    echo "<li>Try adding a new category in the dashboard</li>";
    echo "<li>Check browser console (F12) for JavaScript errors</li>";
    echo "<li>Check Network tab for API calls</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
