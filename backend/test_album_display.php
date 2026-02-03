<?php
// Test Album Display with Debug Info
require_once __DIR__ . '/config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get albums
$query = "SELECT * FROM albums";
$stmt = $db->prepare($query);
$stmt->execute();
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Album Display</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .album-cover { width: 60px; height: 60px; object-fit: cover; }
        .debug { font-family: monospace; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <h1>Album Display Test</h1>
    
    <h2>Current Albums in Database:</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Cover Image Path</th>
            <th>Image Display</th>
            <th>Debug Info</th>
        </tr>
        <?php foreach ($albums as $album): ?>
        <tr>
            <td><?php echo $album['id']; ?></td>
            <td><?php echo htmlspecialchars($album['title']); ?></td>
            <td class="debug"><?php echo htmlspecialchars($album['cover_image'] ?: 'NULL'); ?></td>
            <td>
                <?php if ($album['cover_image']): ?>
                    <!-- Test different path formats -->
                    <div>
                        <strong>Original:</strong><br>
                        <img src="<?php echo $album['cover_image']; ?>" class="album-cover" 
                             onerror="this.style.border='2px solid red'; this.title='Failed: <?php echo $album['cover_image']; ?>'">
                    </div>
                    <div>
                        <strong>With ../:</strong><br>
                        <img src="../<?php echo $album['cover_image']; ?>" class="album-cover"
                             onerror="this.style.border='2px solid red'; this.title='Failed: ../<?php echo $album['cover_image']; ?>'">
                    </div>
                    <div>
                        <strong>With /:</strong><br>
                        <img src="/<?php echo $album['cover_image']; ?>" class="album-cover"
                             onerror="this.style.border='2px solid red'; this.title='Failed: /<?php echo $album['cover_image']; ?>'">
                    </div>
                <?php else: ?>
                    <span style="color: red;">No cover image</span>
                <?php endif; ?>
            </td>
            <td class="debug">
                <?php if ($album['cover_image']): ?>
                    Path: <?php echo htmlspecialchars($album['cover_image']); ?><br>
                    Full Path: <?php echo htmlspecialchars(__DIR__ . '/../' . $album['cover_image']); ?><br>
                    File Exists: <?php echo file_exists(__DIR__ . '/../' . $album['cover_image']) ? 'YES' : 'NO'; ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>File System Check:</h2>
    <?php
    $paths = [
        'public/images/demo/',
        '../public/images/demo/',
        '../../public/images/demo/'
    ];
    
    foreach ($paths as $path) {
        $fullPath = __DIR__ . '/' . $path;
        echo "<p><strong>$path</strong>: ";
        if (is_dir($fullPath)) {
            echo "✅ Directory exists<br>";
            $files = scandir($fullPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    echo "- $file<br>";
                }
            }
        } else {
            echo "❌ Directory missing";
        }
        echo "</p>";
    }
    ?>
    
    <p><a href="admin/albums.php">Back to Albums Admin</a></p>
</body>
</html>
