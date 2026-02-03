<?php
// Test Login Process
echo "<h1>Test Login Process</h1>";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/config/database.php';
    
    echo "<h3>Step 1: Database Connection</h3>";
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }
    echo "<p style='color: green;'>✓ Database connected</p>";
    
    echo "<h3>Step 2: Check Admin User</h3>";
    $username = 'admin';
    $query = "SELECT * FROM admin_users WHERE username = :username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p style='color: green;'>✓ User found: " . htmlspecialchars($user['username']) . "</p>";
        echo "<pre>";
        echo "ID: " . $user['id'] . "\n";
        echo "Username: " . $user['username'] . "\n";
        echo "Email: " . $user['email'] . "\n";
        echo "Role: " . $user['role'] . "\n";
        echo "Status: " . $user['status'] . "\n";
        echo "Password Hash: " . substr($user['password'], 0, 20) . "...\n";
        echo "</pre>";
        
        echo "<h3>Step 3: Test Password Verification</h3>";
        $password = 'admin123';
        if (password_verify($password, $user['password'])) {
            echo "<p style='color: green;'>✓ Password 'admin123' matches hash</p>";
        } else {
            echo "<p style='color: red;'>✗ Password 'admin123' does not match hash</p>";
            
            // Test with different passwords
            $testPasswords = ['admin', 'password', '123456'];
            foreach ($testPasswords as $testPass) {
                if (password_verify($testPass, $user['password'])) {
                    echo "<p style='color: orange;'>⚠ Password '$testPass' matches hash!</p>";
                }
            }
        }
        
        echo "<h3>Step 4: Test Login Query</h3>";
        $loginQuery = "SELECT * FROM admin_users WHERE username = :username AND status = 'active'";
        $loginStmt = $db->prepare($loginQuery);
        $loginStmt->bindParam(':username', $username);
        $loginStmt->execute();
        $loginUser = $loginStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($loginUser) {
            echo "<p style='color: green;'>✓ Login query returns user</p>";
        } else {
            echo "<p style='color: red;'>✗ Login query returns no user</p>";
            echo "<p>Checking status: '" . $user['status'] . "'</p>";
        }
        
        echo "<h3>Step 5: Simulate Login</h3>";
        if ($loginUser && password_verify($password, $loginUser['password'])) {
            echo "<p style='color: green;'>✓ Login would succeed</p>";
            
            // Start session and set variables
            session_start();
            $_SESSION['admin_id'] = $loginUser['id'];
            $_SESSION['admin_username'] = $loginUser['username'];
            $_SESSION['admin_role'] = $loginUser['role'];
            $_SESSION['login_time'] = time();
            
            echo "<p style='color: green;'>✓ Session variables set</p>";
            echo "<pre>";
            print_r($_SESSION);
            echo "</pre>";
            
        } else {
            echo "<p style='color: red;'>✗ Login would fail</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ User 'admin' not found</p>";
        
        // Create the user
        echo "<h3>Creating Admin User</h3>";
        $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
        $insertQuery = "INSERT INTO admin_users (username, email, password, full_name, role, status) 
                        VALUES ('admin', 'admin@madam-portfolio.com', :password, 'Administrator', 'super_admin', 'active')";
        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->bindParam(':password', $passwordHash);
        
        if ($insertStmt->execute()) {
            echo "<p style='color: green;'>✓ Admin user created</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to create admin user</p>";
        }
    }
    
    echo "<h2 style='color: green;'>Test Complete!</h2>";
    echo "<p><a href='admin/login.php'>Try logging in</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Test Failed!</h2>";
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
