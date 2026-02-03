<?php
// Quick Database Check
echo "<h1>Database Check</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h3>Albums:</h3>";
    $query = "SELECT id, title, cover_image FROM albums LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($albums as $album) {
        echo "<p><strong>ID {$album['id']}:</strong> {$album['title']} - Cover: " . ($album['cover_image'] ?: 'NULL') . "</p>";
    }
    
    echo "<h3>Upload Directory:</h3>";
    $uploadDir = __DIR__ . '/uploads';
    if (is_dir($uploadDir)) {
        echo "<p>✅ Directory exists</p>";
        $files = scandir($uploadDir);
        echo "<p>Files: " . implode(', ', array_slice($files, 0, 5)) . "</p>";
    } else {
        echo "<p>❌ Directory missing</p>";
    }
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
