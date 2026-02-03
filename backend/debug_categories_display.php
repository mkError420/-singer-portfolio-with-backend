<?php
// Debug Categories Display Issue
echo "<h1>Categories Display Debug</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Check Categories in Database</h2>";
    
    // Check current categories
    $query = "SELECT * FROM album_categories WHERE status = 'active' ORDER BY name";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total categories: " . count($categories) . "</p>";
    
    if (count($categories) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Status</th><th>Created</th></tr>";
        foreach ($categories as $category) {
            echo "<tr>";
            echo "<td>{$category['id']}</td>";
            echo "<td><strong>" . htmlspecialchars($category['name']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($category['description']) . "</td>";
            echo "<td>{$category['status']}</td>";
            echo "<td>{$category['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No categories found</p>";
    }
    
    echo "<h2>Step 2: Test Categories API Response</h2>";
    
    // Test the API exactly as the frontend would call it
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
            
            echo "<h3>API Response Format:</h3>";
            echo "<pre>" . htmlspecialchars(json_encode($apiData, JSON_PRETTY_PRINT)) . "</pre>";
            
            // Check if the format matches what JavaScript expects
            if (count($apiData) > 0) {
                $firstCategory = $apiData[0];
                if (isset($firstCategory['name'])) {
                    echo "<p>✅ API format is correct (has 'name' field)</p>";
                } else {
                    echo "<p>❌ API format is incorrect (missing 'name' field)</p>";
                }
            }
        } else {
            echo "<p>❌ API returned invalid data</p>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    } else {
        echo "<p>❌ Categories API not responding</p>";
    }
    
    echo "<h2>Step 3: Test Adding a New Category</h2>";
    
    // Test adding a new category
    $testCategory = [
        'name' => 'Debug Category ' . date('His'),
        'description' => 'This is a debug category created at ' . date('Y-m-d H:i:s')
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
            echo "<p>✅ Test category created with ID: {$postResult['id']}</p>";
            
            // Verify it was created
            echo "<h2>Step 4: Verify New Category in Database</h2>";
            $verifyQuery = "SELECT * FROM album_categories WHERE id = ?";
            $verifyStmt = $db->prepare($verifyQuery);
            $verifyStmt->execute([$postResult['id']]);
            $newCategory = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($newCategory) {
                echo "<p>✅ New category found in database</p>";
                echo "<pre>" . htmlspecialchars(json_encode($newCategory, JSON_PRETTY_PRINT)) . "</pre>";
            } else {
                echo "<p>❌ New category NOT found in database</p>";
            }
            
            // Test API again to see if new category appears
            echo "<h2>Step 5: Test API Again After Creation</h2>";
            $newResponse = file_get_contents($apiUrl, false, $context);
            
            if ($newResponse) {
                $newApiData = json_decode($newResponse, true);
                if (is_array($newApiData)) {
                    echo "<p>✅ API now returns " . count($newApiData) . " categories</p>";
                    
                    // Check if our new category is there
                    $foundNewCategory = false;
                    foreach ($newApiData as $cat) {
                        if (isset($cat['name']) && strpos($cat['name'], 'Debug Category') !== false) {
                            $foundNewCategory = true;
                            break;
                        }
                    }
                    
                    if ($foundNewCategory) {
                        echo "<p>✅ New category appears in API response</p>";
                    } else {
                        echo "<p>❌ New category NOT found in API response</p>";
                    }
                }
            }
        } else {
            echo "<p>❌ Category creation failed</p>";
        }
    } else {
        echo "<p>❌ Category creation API not responding</p>";
    }
    
    echo "<h2>Step 6: JavaScript Debugging Instructions</h2>";
    echo "<p><strong>To debug in browser:</strong></p>";
    echo "<ol>";
    echo "<li>Open <a href='admin/albums.php' target='_blank'>Albums Dashboard</a></li>";
    echo "<li>Open browser console (F12)</li>";
    echo "<li>Type: <code>console.log('Current categories:', categories)</code></li>";
    echo "<li>Type: <code>console.log('Category select:', document.getElementById('category'))</code></li>";
    echo "<li>Type: <code>console.log('Category options:', document.getElementById('category').options)</code></li>";
    echo "<li>Try adding a new category and watch console logs</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
