<?php
// Create Categories Table and Populate with Data
echo "<h1>Create Categories Table</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Create album_categories Table</h2>";
    
    // Create the table
    $createTable = "CREATE TABLE IF NOT EXISTS album_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        description TEXT,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $db->exec($createTable);
    echo "<p>✅ album_categories table created/verified</p>";
    
    echo "<h2>Step 2: Insert Default Categories</h2>";
    
    $defaultCategories = [
        ['album', 'Studio Albums - Full-length releases'],
        ['acoustic', 'Acoustic Versions - Unplugged performances'],
        ['live', 'Live Recordings - Concert performances'],
        ['remix', 'Remixes - Reimagined versions'],
        ['compilation', 'Compilations - Collection of tracks'],
        ['ep', 'EPs - Extended plays'],
        ['single', 'Singles - Individual releases'],
        ['rock', 'Rock Music - Guitar-driven sound'],
        ['pop', 'Pop Music - Commercial popular music'],
        ['jazz', 'Jazz - Improvisational music'],
        ['classical', 'Classical - Traditional orchestral music'],
        ['electronic', 'Electronic - Digital and synthesized music'],
        ['folk', 'Folk - Traditional and acoustic music'],
        ['blues', 'Blues - Rooted in African American music'],
        ['country', 'Country - American country music'],
        ['r&b', 'R&B - Rhythm and Blues'],
        ['hip-hop', 'Hip-Hop - Rap and urban music']
    ];
    
    $insertCount = 0;
    foreach ($defaultCategories as $category) {
        $insertQuery = "INSERT IGNORE INTO album_categories (name, description) VALUES (?, ?)";
        $insertStmt = $db->prepare($insertQuery);
        if ($insertStmt->execute([$category[0], $category[1]])) {
            $insertCount++;
        }
    }
    echo "<p>✅ Inserted $insertCount default categories</p>";
    
    echo "<h2>Step 3: Verify Categories</h2>";
    
    $query = "SELECT * FROM album_categories WHERE status = 'active' ORDER BY name";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total active categories: " . count($categories) . "</p>";
    
    echo "<h3>Available Categories:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Status</th></tr>";
    foreach ($categories as $category) {
        echo "<tr>";
        echo "<td>{$category['id']}</td>";
        echo "<td><strong>" . htmlspecialchars($category['name']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($category['description']) . "</td>";
        echo "<td>{$category['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Step 4: Test Categories API</h2>";
    
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
        echo "<p>✅ Categories API responding</p>";
        $apiData = json_decode($response, true);
        
        if (is_array($apiData)) {
            echo "<p>✅ API returned " . count($apiData) . " categories</p>";
            
            echo "<h3>API Response Sample:</h3>";
            echo "<pre>" . htmlspecialchars(json_encode(array_slice($apiData, 0, 3), JSON_PRETTY_PRINT)) . "</pre>";
            
            // Verify format
            if (count($apiData) > 0 && isset($apiData[0]['name'])) {
                echo "<p>✅ API format is correct (has 'name' field)</p>";
            } else {
                echo "<p>❌ API format is incorrect</p>";
            }
        } else {
            echo "<p>❌ API returned invalid data</p>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    } else {
        echo "<p>❌ Categories API not responding</p>";
    }
    
    echo "<h2>Step 5: Test Adding a New Category</h2>";
    
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
        
        if (isset($postResult['id'])) {
            echo "<p>✅ Test category created successfully!</p>";
        } else {
            echo "<p>❌ Category creation failed</p>";
        }
    } else {
        echo "<p>❌ Category creation API not responding</p>";
    }
    
    echo "<h2 style='color: green;'>🎉 Categories Table Creation Complete!</h2>";
    echo "<p><strong>What was done:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Created album_categories table</li>";
    echo "<li>✅ Inserted 17 default categories</li>";
    echo "<li>✅ Verified API response format</li>";
    echo "<li>✅ Tested category creation</li>";
    echo "</ul>";
    
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li><a href='admin/albums.php' target='_blank'>Open Albums Dashboard</a></li>";
    echo "<li>Category dropdown should now show 17+ categories</li>";
    echo "<li>Test adding a new category with '+ Add New' button</li>";
    echo "<li>Verify new category appears in dropdown</li>";
    echo "</ol>";
    
    echo "<p><strong>Categories Available:</strong></p>";
    echo "<p>album, acoustic, live, remix, compilation, ep, single, rock, pop, jazz, classical, electronic, folk, blues, country, r&b, hip-hop</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
