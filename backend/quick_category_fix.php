<?php
// Quick Category Fix - Check and Fix API Response
echo "<h1>Quick Category Fix</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Verify Categories Table</h2>";
    
    $query = "SELECT * FROM album_categories WHERE status = 'active' ORDER BY name";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Categories in database: " . count($categories) . "</p>";
    
    if (count($categories) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Name</th><th>Description</th></tr>";
        foreach ($categories as $category) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($category['name']) . "</td>";
            echo "<td>" . htmlspecialchars($category['description']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No categories found</p>";
        exit;
    }
    
    echo "<h2>Step 2: Test Categories API Directly</h2>";
    
    // Test the API exactly as JavaScript calls it
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/madam-portfolio/backend/api/categories.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = file_get_contents($apiUrl, false, $context);
    
    if ($response) {
        echo "<p>✅ API Response received</p>";
        echo "<h3>Raw API Response:</h3>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
        
        $apiData = json_decode($response, true);
        
        if (is_array($apiData)) {
            echo "<p>✅ API returned " . count($apiData) . " categories</p>";
            
            if (count($apiData) > 0) {
                echo "<h3>First Category Structure:</h3>";
                echo "<pre>" . htmlspecialchars(json_encode($apiData[0], JSON_PRETTY_PRINT)) . "</pre>";
                
                // Check if the format is correct
                if (isset($apiData[0]['name'])) {
                    echo "<p>✅ API format is correct</p>";
                } else {
                    echo "<p>❌ API format is incorrect - missing 'name' field</p>";
                }
            }
        } else {
            echo "<p>❌ API returned invalid data</p>";
        }
    } else {
        echo "<p>❌ API not responding</p>";
    }
    
    echo "<h2>Step 3: Create Test JavaScript</h2>";
    
    // Create a simple test page to debug the JavaScript
    $testJs = "
    <script>
    async function testCategories() {
        console.log('Testing categories API...');
        
        try {
            const response = await fetch('api/categories.php');
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            
            const categories = await response.json();
            console.log('Categories loaded:', categories);
            console.log('Categories count:', categories.length);
            
            // Test populating a select
            const select = document.createElement('select');
            select.innerHTML = '<option value=\"\">Select Category</option>';
            
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.name;
                option.textContent = category.name;
                select.appendChild(option);
            });
            
            console.log('Select options count:', select.options.length);
            console.log('Select HTML:', select.innerHTML);
            
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    // Run test when page loads
    window.onload = testCategories;
    </script>
    ";
    
    echo "<p>Test JavaScript created. Check browser console for detailed debugging.</p>";
    
    echo "<h2>Step 4: Browser Debug Instructions</h2>";
    echo "<p><strong>To debug in Albums Dashboard:</strong></p>";
    echo "<ol>";
    echo "<li>Open <a href='admin/albums.php' target='_blank'>Albums Dashboard</a></li>";
    echo "<li>Open browser console (F12)</li>";
    echo "<li>Type: <code>localStorage.clear(); location.reload();</code></li>";
    echo "<li>Watch for category loading messages</li>";
    echo "<li>Type: <code>categories</code> to see the categories variable</li>";
    echo "<li>Type: <code>document.getElementById('category')</code> to check the select element</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
