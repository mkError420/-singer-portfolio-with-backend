<?php
// Test Albums Page Loading
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo "<h1>Access Denied</h1>";
    echo "<p>Please <a href='admin/login.php'>login first</a></p>";
    exit;
}

require_once __DIR__ . '/config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get albums
$query = "SELECT * FROM albums WHERE status = 'active' ORDER BY id";
$stmt = $db->prepare($query);
$stmt->execute();
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Albums Test Page</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .album-cover { width: 60px; height: 60px; object-fit: cover; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Albums Dashboard Test</h1>
    
    <div class="success">
        <p>✅ Admin logged in: <?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
        <p>✅ Database connected</p>
        <p>✅ Found <?php echo count($albums); ?> albums</p>
    </div>
    
    <h2>Albums Data:</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Year</th>
            <th>Category</th>
            <th>Cover Image</th>
            <th>Image Test</th>
        </tr>
        <?php if (count($albums) > 0): ?>
            <?php foreach ($albums as $album): ?>
                <tr>
                    <td><?php echo $album['id']; ?></td>
                    <td><?php echo htmlspecialchars($album['title']); ?></td>
                    <td><?php echo $album['year']; ?></td>
                    <td><?php echo $album['category']; ?></td>
                    <td><?php echo htmlspecialchars($album['cover_image'] ?: 'NULL'); ?></td>
                    <td>
                        <?php if ($album['cover_image']): ?>
                            <img src="<?php echo $album['cover_image']; ?>" class="album-cover" 
                                 onerror="this.style.border='2px solid red'; this.title='Failed: <?php echo $album['cover_image']; ?>'">
                        <?php else: ?>
                            <span class="error">No image</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="error">No albums found in database</td>
            </tr>
        <?php endif; ?>
    </table>
    
    <h2>API Test:</h2>
    <div id="api-test">
        <p>Testing API...</p>
    </div>
    
    <h2>Actions:</h2>
    <p>
        <a href="admin/albums.php" target="_blank">👉 Go to Albums Dashboard</a> |
        <a href="check_dashboard.php">👉 Run Dashboard Check</a> |
        <a href="simple_fix.php">👉 Run Simple Fix</a>
    </p>
    
    <script>
        // Test API from JavaScript
        fetch('api/albums.php')
            .then(response => response.json())
            .then(data => {
                const apiTest = document.getElementById('api-test');
                if (Array.isArray(data)) {
                    apiTest.innerHTML = `<p class="success">✅ API returned ${data.length} albums</p>`;
                } else {
                    apiTest.innerHTML = `<p class="error">❌ API returned invalid data</p>`;
                }
            })
            .catch(error => {
                document.getElementById('api-test').innerHTML = `<p class="error">❌ API Error: ${error.message}</p>`;
            });
    </script>
</body>
</html>
