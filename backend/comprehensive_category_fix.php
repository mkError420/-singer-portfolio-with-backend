<?php
// Comprehensive Category Fix - Complete Diagnosis and Repair
echo "<h1>Comprehensive Category Fix</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Step 1: Verify Albums Table Structure</h2>";
    
    $structureQuery = "DESCRIBE albums";
    $structureStmt = $db->query($structureQuery);
    $columns = $structureStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $categoryColumn = null;
    foreach ($columns as $column) {
        if ($column['Field'] === 'category') {
            $categoryColumn = $column;
            break;
        }
    }
    
    if ($categoryColumn) {
        echo "<p>✅ Category column found:</p>";
        echo "<pre>" . htmlspecialchars(json_encode($categoryColumn, JSON_PRETTY_PRINT)) . "</pre>";
        
        if ($categoryColumn['Type'] !== 'varchar(100)') {
            echo "<p style='color: orange;'>⚠️ Category column type is not varchar(100), fixing...</p>";
            $db->exec("ALTER TABLE albums MODIFY COLUMN category VARCHAR(100) DEFAULT 'album'");
            echo "<p>✅ Category column type fixed</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Category column missing, adding it...</p>";
        $db->exec("ALTER TABLE albums ADD COLUMN category VARCHAR(100) DEFAULT 'album' AFTER year");
        echo "<p>✅ Category column added</p>";
    }
    
    echo "<h2>Step 2: Test Direct Database Operations</h2>";
    
    // Test 1: Direct INSERT with category
    $testTitle = 'Direct Test ' . date('His');
    $testYear = date('Y');
    $testCategory = 'rock';
    $testDescription = 'Direct database test';
    
    $insertQuery = "INSERT INTO albums (title, year, category, description) VALUES (?, ?, ?, ?)";
    $insertStmt = $db->prepare($insertQuery);
    
    echo "<p>Testing direct INSERT with category '$testCategory'...</p>";
    
    if ($insertStmt->execute([$testTitle, $testYear, $testCategory, $testDescription])) {
        $insertId = $db->lastInsertId();
        echo "<p>✅ Direct INSERT successful, ID: $insertId</p>";
        
        // Verify the insert
        $verifyQuery = "SELECT title, category FROM albums WHERE id = ?";
        $verifyStmt = $db->prepare($verifyQuery);
        $verifyStmt->execute([$insertId]);
        $result = $verifyStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['category'] === $testCategory) {
            echo "<p style='color: green;'>✅ Direct INSERT category saved correctly: '{$result['category']}'</p>";
        } else {
            echo "<p style='color: red;'>❌ Direct INSERT failed. Expected: '$testCategory', Got: '" . ($result['category'] ?? 'NULL') . "'</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Direct INSERT failed</p>";
    }
    
    // Test 2: Direct UPDATE with category
    echo "<p>Testing direct UPDATE with category...</p>";
    $updateQuery = "UPDATE albums SET category = ? WHERE id = ?";
    $updateStmt = $db->prepare($updateQuery);
    
    if ($updateStmt->execute(['jazz', $insertId])) {
        echo "<p>✅ Direct UPDATE successful</p>";
        
        // Verify the update
        $verifyStmt->execute([$insertId]);
        $updatedResult = $verifyStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($updatedResult && $updatedResult['category'] === 'jazz') {
            echo "<p style='color: green;'>✅ Direct UPDATE category saved correctly: '{$updatedResult['category']}'</p>";
        } else {
            echo "<p style='color: red;'>❌ Direct UPDATE failed</p>";
        }
    }
    
    echo "<h2>Step 3: Test API with Detailed Logging</h2>";
    
    // Enable error logging
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/category_debug.log');
    
    $apiTestAlbum = [
        'title' => 'API Test Album ' . date('His'),
        'year' => date('Y'),
        'category' => 'pop',
        'description' => 'API test for category',
        'cover_image' => 'uploads/api_test.jpg',
        'tracks' => []
    ];
    
    echo "<p>Testing API with category '{$apiTestAlbum['category']}'...</p>";
    echo "<p>Data being sent to API:</p>";
    echo "<pre>" . htmlspecialchars(json_encode($apiTestAlbum, JSON_PRETTY_PRINT)) . "</pre>";
    
    $postData = json_encode($apiTestAlbum);
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/madam-portfolio/backend/api/albums.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $postData
        ]
    ]);
    
    $response = file_get_contents($apiUrl, false, $context);
    
    if ($response) {
        $apiResult = json_decode($response, true);
        echo "<p>API Response:</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
        
        if (isset($apiResult['id'])) {
            $apiId = $apiResult['id'];
            echo "<p>✅ API created album with ID: $apiId</p>";
            
            // Verify API result
            $apiVerifyQuery = "SELECT title, category FROM albums WHERE id = ?";
            $apiVerifyStmt = $db->prepare($apiVerifyQuery);
            $apiVerifyStmt->execute([$apiId]);
            $apiResult = $apiVerifyStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($apiResult) {
                echo "<p>API Result in database:</p>";
                echo "<pre>" . htmlspecialchars(json_encode($apiResult, JSON_PRETTY_PRINT)) . "</pre>";
                
                if ($apiResult['category'] === $apiTestAlbum['category']) {
                    echo "<p style='color: green;'>✅ API category saved correctly: '{$apiResult['category']}'</p>";
                } else {
                    echo "<p style='color: red;'>❌ API category failed. Expected: '{$apiTestAlbum['category']}', Got: '{$apiResult['category']}'</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>❌ API creation failed</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ API not responding</p>";
    }
    
    echo "<h2>Step 4: Check Debug Logs</h2>";
    
    $logFile = __DIR__ . '/category_debug.log';
    if (file_exists($logFile)) {
        echo "<p>Debug log contents:</p>";
        echo "<pre>" . htmlspecialchars(file_get_contents($logFile)) . "</pre>";
    } else {
        echo "<p>No debug log found</p>";
    }
    
    echo "<h2>Step 5: Fix Frontend Form Issues</h2>";
    
    echo "<p>Creating a test form to verify frontend submission...</p>";
    
    // Create a simple test form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Category Test Form</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .form-group { margin: 15px 0; }
            label { display: block; margin-bottom: 5px; }
            input, select, textarea { width: 300px; padding: 8px; }
            button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
            button:hover { background: #0056b3; }
            .result { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
            .success { background: #d4edda; border-color: #c3e6cb; }
            .error { background: #f8d7da; border-color: #f5c6cb; }
        </style>
    </head>
    <body>
        <h2>Category Test Form</h2>
        <form id="testForm">
            <div class="form-group">
                <label for="testTitle">Title:</label>
                <input type="text" id="testTitle" value="Test Album <?php echo date('His'); ?>" required>
            </div>
            <div class="form-group">
                <label for="testYear">Year:</label>
                <input type="text" id="testYear" value="<?php echo date('Y'); ?>" required>
            </div>
            <div class="form-group">
                <label for="testCategory">Category:</label>
                <select id="testCategory" required>
                    <option value="">Select Category</option>
                    <option value="rock">Rock</option>
                    <option value="pop">Pop</option>
                    <option value="jazz">Jazz</option>
                    <option value="classical">Classical</option>
                    <option value="electronic">Electronic</option>
                </select>
            </div>
            <div class="form-group">
                <label for="testDescription">Description:</label>
                <textarea id="testDescription">Test description</textarea>
            </div>
            <button type="submit">Test Category Submission</button>
        </form>
        
        <div id="result" class="result" style="display: none;"></div>
        
        <script>
            document.getElementById('testForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const resultDiv = document.getElementById('result');
                resultDiv.style.display = 'block';
                resultDiv.className = 'result';
                resultDiv.innerHTML = '<p>Testing...</p>';
                
                const categoryValue = document.getElementById('testCategory').value;
                console.log('Selected category:', categoryValue);
                
                const albumData = {
                    title: document.getElementById('testTitle').value,
                    year: document.getElementById('testYear').value,
                    category: categoryValue,
                    description: document.getElementById('testDescription').value,
                    cover_image: 'uploads/test.jpg',
                    tracks: []
                };
                
                console.log('Album data being sent:', albumData);
                
                try {
                    const response = await fetch('api/albums.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(albumData)
                    });
                    
                    const result = await response.json();
                    console.log('API Response:', result);
                    
                    if (result.id) {
                        resultDiv.className = 'result success';
                        resultDiv.innerHTML = `
                            <h3>✅ Success!</h3>
                            <p>Album created with ID: ${result.id}</p>
                            <p>Category sent: "${categoryValue}"</p>
                            <p><a href="comprehensive_category_fix.php" target="_blank">Refresh this page to verify in database</a></p>
                        `;
                    } else {
                        resultDiv.className = 'result error';
                        resultDiv.innerHTML = `
                            <h3>❌ Failed</h3>
                            <p>Error: ${result.message || 'Unknown error'}</p>
                        `;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    resultDiv.className = 'result error';
                    resultDiv.innerHTML = `
                        <h3>❌ Error</h3>
                        <p>${error.message}</p>
                    `;
                }
            });
        </script>
    </body>
    </html>
    <?php
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
