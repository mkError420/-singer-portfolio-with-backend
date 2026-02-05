<?php
require_once 'backend/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Delete the test entry
    $stmt = $pdo->prepare("DELETE FROM videos WHERE title = :title");
    $stmt->execute([':title' => 'All this videos']);
    
    echo "Test entry deleted successfully";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
