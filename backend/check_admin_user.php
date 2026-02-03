<?php
// Check and Fix Admin User
echo "<h1>Check Admin User</h1>";

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }
    
    echo "<p style='color: green;'>✓ Connected to database</p>";
    
    // Check if admin user exists
    $query = "SELECT * FROM admin_users WHERE username = 'admin'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<h3>Current Admin User:</h3>";
        echo "<pre>";
        print_r($admin);
        echo "</pre>";
        
        // Test password verification
        $inputPassword = 'admin123';
        if (password_verify($inputPassword, $admin['password'])) {
            echo "<p style='color: green;'>✓ Password verification works</p>";
        } else {
            echo "<p style='color: red;'>✗ Password verification failed</p>";
            
            // Update password to correct hash
            $newPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $updateQuery = "UPDATE admin_users SET password = :password WHERE username = 'admin'";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':password', $newPassword);
            $updateStmt->execute();
            
            echo "<p style='color: green;'>✓ Password updated to correct hash</p>";
        }
        
        // Check status
        if ($admin['status'] === 'active') {
            echo "<p style='color: green;'>✓ User status is active</p>";
        } else {
            echo "<p style='color: red;'>✗ User status is not active</p>";
            
            // Update status
            $updateQuery = "UPDATE admin_users SET status = 'active' WHERE username = 'admin'";
            $db->exec($updateQuery);
            echo "<p style='color: green;'>✓ User status updated to active</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Admin user not found</p>";
        
        // Create admin user
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $insertQuery = "INSERT INTO admin_users (username, email, password, full_name, role, status) 
                        VALUES ('admin', 'admin@madam-portfolio.com', :password, 'Administrator', 'super_admin', 'active')";
        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->bindParam(':password', $password);
        $insertStmt->execute();
        
        echo "<p style='color: green;'>✓ Admin user created</p>";
    }
    
    // Test the login query directly
    echo "<h3>Testing Login Query:</h3>";
    $testQuery = "SELECT * FROM admin_users WHERE username = :username AND status = 'active'";
    $testStmt = $db->prepare($testQuery);
    $testStmt->bindParam(':username', $username);
    $username = 'admin';
    $testStmt->execute();
    $testResult = $testStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testResult) {
        echo "<p style='color: green;'>✓ Login query returns user</p>";
        echo "<pre>";
        print_r($testResult);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>✗ Login query returns no results</p>";
    }
    
    echo "<h2 style='color: green;'>Check Complete!</h2>";
    echo "<p><a href='admin/login.php'>Try logging in again</a></p>";
    echo "<p><strong>Login Credentials:</strong></p>";
    echo "<ul>";
    echo "<li>Username: admin</li>";
    echo "<li>Password: admin123</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Check Failed!</h2>";
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
