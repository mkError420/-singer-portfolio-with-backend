<?php
// Database Setup Script
echo "<h1>Database Setup</h1>";

try {
    // First, create database if it doesn't exist
    $conn = new PDO('mysql:host=localhost', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS madam_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn->exec($sql);
    echo "<p style='color: green;'>✓ Database 'madam_portfolio' created/verified</p>";
    
    // Now connect to the specific database
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }
    
    echo "<p style='color: green;'>✓ Connected to database 'madam_portfolio'</p>";
    
    // Read and execute schema
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    
    if ($schema === false) {
        throw new Exception("Could not read schema file");
    }
    
    // Split schema into individual statements
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    echo "<h2>Executing SQL Statements...</h2>";
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $db->exec($statement);
                echo "<p style='color: green;'>✓ " . htmlspecialchars(substr($statement, 0, 50)) . "...</p>";
            } catch (PDOException $e) {
                echo "<p style='color: orange;'>⚠ " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }
    
    echo "<h2 style='color: green;'>Setup Complete!</h2>";
    echo "<p>Database and tables have been created successfully.</p>";
    echo "<p><strong>Default Admin Login:</strong></p>";
    echo "<ul>";
    echo "<li>Username: admin</li>";
    echo "<li>Password: admin123</li>";
    echo "</ul>";
    echo "<p><a href='admin/login.php'>Go to Admin Dashboard</a></p>";
    echo "<p><a href='../index.html'>Go to Website</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Setup Failed!</h2>";
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>MySQL server is running (check XAMPP/WAMP)</li>";
    echo "<li>Database configuration in config/config.php</li>";
    echo "<li>User 'root' has sufficient privileges</li>";
    echo "</ul>";
    echo "<p><a href='create_database.php'>Try creating database first</a></p>";
}
?>
