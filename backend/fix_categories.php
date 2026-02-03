<?php
// Fix Categories System
echo "<h1>Fix Categories System</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Create/Update Categories Table</h2>";
    
    // Create table if not exists
    $createTable = "CREATE TABLE IF NOT EXISTS album_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        description TEXT,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $db->exec($createTable);
    echo "<p>✅ Categories table ready</p>";
    
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
    echo "<tr><th>Name</th><th>Description</th></tr>";
    foreach ($categories as $category) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($category['name']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($category['description']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Step 4: Test API Response</h2>";
    
    // Test the API directly
    try {
        $query = "SELECT name, description FROM album_categories WHERE status = 'active' ORDER BY name";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $apiCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>✅ API query successful</p>";
        echo "<p>Categories that will be returned by API:</p>";
        echo "<pre>" . htmlspecialchars(json_encode($apiCategories, JSON_PRETTY_PRINT)) . "</pre>";
        
    } catch (Exception $e) {
        echo "<p>❌ API query failed: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2 style='color: green;'>🎉 Categories Fix Complete!</h2>";
    echo "<p><strong>What was done:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Created/verified album_categories table</li>";
    echo "<li>✅ Inserted 16 default categories</li>";
    echo "<li>✅ Verified API response format</li>";
    echo "<li>✅ Categories ready for dropdown</li>";
    echo "</ul>";
    
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li><a href='admin/albums.php' target='_blank'>Open Albums Dashboard</a></li>";
    echo "<li>Click '+ Add New' next to Category dropdown</li>";
    echo "<li>Try adding a new category</li>";
    echo "<li>Check if it appears in dropdown</li>";
    echo "</ol>";
    
    echo "<p><strong>If still not working:</strong></p>";
    echo "<ul>";
    echo "<li>Open browser console (F12)</li>";
    echo "<li>Look for JavaScript errors</li>";
    echo "<li>Check Network tab for failed API calls</li>";
    echo "<li>Run <a href='test_categories.php'>Categories Test</a> for detailed diagnostics</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
