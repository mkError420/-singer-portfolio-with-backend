<?php
// Create Database Script
echo "<h1>Create Database</h1>";

try {
    // Connect to MySQL without specifying database
    $conn = new PDO('mysql:host=localhost', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✓ Connected to MySQL server</p>";
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS madam_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn->exec($sql);
    
    echo "<p style='color: green;'>✓ Database 'madam_portfolio' created successfully</p>";
    
    // Grant privileges
    $sql = "GRANT ALL PRIVILEGES ON madam_portfolio.* TO 'root'@'localhost'";
    $conn->exec($sql);
    
    echo "<p style='color: green;'>✓ Database privileges granted</p>";
    
    echo "<h2 style='color: green;'>Database Created Successfully!</h2>";
    echo "<p><a href='setup.php'>Now run the setup script to create tables</a></p>";
    
} catch(PDOException $e) {
    echo "<h2 style='color: red;'>Database Creation Failed!</h2>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure:</p>";
    echo "<ul>";
    echo "<li>MySQL server is running</li>";
    echo "<li>User 'root' has privileges to create databases</li>";
    echo "<li>XAMPP/WAMP is properly configured</li>";
    echo "</ul>";
}

$conn = null;
?>
