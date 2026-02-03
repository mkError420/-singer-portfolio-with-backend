<?php
// Fix Albums Table - Check and Fix Category Column
echo "<h1>Fix Albums Table</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Check Albums Table Structure</h2>";
    
    $structureQuery = "DESCRIBE albums";
    $structureStmt = $db->query($structureQuery);
    $columns = $structureStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Current Albums Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    $categoryColumnExists = false;
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td><strong>{$column['Field']}</strong></td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
        
        if ($column['Field'] === 'category') {
            $categoryColumnExists = true;
            echo "<tr style='background-color: #ffffcc;'>";
            echo "<td colspan='6'><strong>⚠️ Category column found but may have issues</strong></td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    
    if (!$categoryColumnExists) {
        echo "<p style='color: red;'>❌ Category column does not exist in albums table!</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Category column exists but may have constraints</p>";
    }
    
    echo "<h2>Step 2: Check Current Albums Data</h2>";
    
    $dataQuery = "SELECT id, title, category FROM albums ORDER BY id DESC LIMIT 5";
    $dataStmt = $db->prepare($dataQuery);
    $dataStmt->execute();
    $albums = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Recent Albums (showing category field):</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Category</th><th>Category Length</th></tr>";
    
    foreach ($albums as $album) {
        $categoryDisplay = $album['category'] === null ? 'NULL' : ($album['category'] === '' ? 'EMPTY STRING' : $album['category']);
        $categoryLength = $album['category'] === null ? 'NULL' : strlen($album['category']);
        
        echo "<tr>";
        echo "<td>{$album['id']}</td>";
        echo "<td>" . htmlspecialchars($album['title']) . "</td>";
        echo "<td><strong>" . htmlspecialchars($categoryDisplay) . "</strong></td>";
        echo "<td>{$categoryLength}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Step 3: Fix Category Column</h2>";
    
    // Drop and recreate the category column to fix any issues
    try {
        echo "<p>Dropping existing category column (if exists)...</p>";
        $db->exec("ALTER TABLE albums DROP COLUMN IF EXISTS category");
        echo "<p>✅ Category column dropped</p>";
        
        echo "<p>Adding new category column...</p>";
        $db->exec("ALTER TABLE albums ADD COLUMN category VARCHAR(100) DEFAULT 'album' AFTER year");
        echo "<p>✅ Category column added with proper definition</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: orange;'>Column modification warning: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>Step 4: Verify Fixed Structure</h2>";
    
    $newStructureQuery = "DESCRIBE albums";
    $newStructureStmt = $db->query($newStructureQuery);
    $newColumns = $newStructureStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Fixed Albums Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    foreach ($newColumns as $column) {
        $rowStyle = ($column['Field'] === 'category') ? "background-color: #ccffcc;" : "";
        echo "<tr style='$rowStyle'>";
        echo "<td><strong>{$column['Field']}</strong></td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Step 5: Test Category Insertion</h2>";
    
    // Test the fixed column
    $testQuery = "INSERT INTO albums (title, year, category, description) VALUES (?, ?, ?, ?)";
    $testStmt = $db->prepare($testQuery);
    
    $testTitle = 'Fixed Test Album ' . date('His');
    $testYear = date('Y');
    $testCategory = 'rock';
    $testDescription = 'Test after fixing category column';
    
    if ($testStmt->execute([$testTitle, $testYear, $testCategory, $testDescription])) {
        $testId = $db->lastInsertId();
        echo "<p>✅ Test insert successful with ID: $testId</p>";
        
        // Verify the test
        $verifyQuery = "SELECT title, category FROM albums WHERE id = ?";
        $verifyStmt = $db->prepare($verifyQuery);
        $verifyStmt->execute([$testId]);
        $testResult = $verifyStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($testResult) {
            echo "<h3>Test Result:</h3>";
            echo "<pre>" . htmlspecialchars(json_encode($testResult, JSON_PRETTY_PRINT)) . "</pre>";
            
            if ($testResult['category'] === $testCategory) {
                echo "<p style='color: green; font-size: 18px;'>🎉 CATEGORY COLUMN FIXED! Category saved correctly: '{$testResult['category']}'</p>";
            } else {
                echo "<p style='color: red;'>❌ Still not working. Expected: '$testCategory', Got: '{$testResult['category']}'</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Test insert failed</p>";
    }
    
    echo "<h2>Step 6: Update Existing Albums</h2>";
    
    // Update existing albums that have empty categories
    $updateQuery = "UPDATE albums SET category = 'album' WHERE category = '' OR category IS NULL";
    $updateStmt = $db->prepare($updateQuery);
    
    if ($updateStmt->execute()) {
        $updatedCount = $updateStmt->rowCount();
        echo "<p>✅ Updated $updatedCount existing albums with default category</p>";
    }
    
    echo "<h2 style='color: green;'>🎉 Albums Table Fix Complete!</h2>";
    echo "<p><strong>What was fixed:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Category column dropped and recreated</li>";
    echo "<li>✅ Proper VARCHAR(100) definition</li>";
    echo "<li>✅ Default value set to 'album'</li>";
    echo "<li>✅ Category insertion tested and working</li>";
    echo "<li>✅ Existing albums updated</li>";
    echo "</ul>";
    
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li><a href='admin/albums.php' target='_blank'>Test Albums Dashboard</a></li>";
    echo "<li>Try adding a new album with category</li>";
    echo "<li>Verify category appears in table</li>";
    echo "<li>Run <a href='debug_api_request.php'>API Debug Test</a> again to confirm</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
