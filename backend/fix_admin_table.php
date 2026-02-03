<?php
// Fix Admin Users Table - Add missing status column
echo "<h1>Fix Admin Users Table</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }
    
    echo "<p style='color: green;'>✓ Connected to database</p>";
    
    // Check if status column exists
    $query = "SHOW COLUMNS FROM admin_users LIKE 'status'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "<p style='color: orange;'>⚠ Status column already exists</p>";
    } else {
        // Add status column
        $alterQuery = "ALTER TABLE admin_users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER role";
        $db->exec($alterQuery);
        echo "<p style='color: green;'>✓ Status column added successfully</p>";
    }
    
    // Update existing admin user to be active
    $updateQuery = "UPDATE admin_users SET status = 'active' WHERE username = 'admin'";
    $db->exec($updateQuery);
    echo "<p style='color: green;'>✓ Admin user status set to active</p>";
    
    echo "<h2 style='color: green;'>Fix Complete!</h2>";
    echo "<p>The admin users table has been updated successfully.</p>";
    echo "<p><a href='admin/login.php'>Try logging in again</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Fix Failed!</h2>";
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
