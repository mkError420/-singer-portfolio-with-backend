<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'madam_portfolio');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('SITE_URL', 'http://localhost/madam-portfolio');
define('ADMIN_URL', SITE_URL . '/backend/admin');
define('API_URL', SITE_URL . '/backend/api');

// Upload Configuration
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Security Configuration
define('JWT_SECRET', 'your-super-secret-jwt-key-change-this-in-production');
define('SESSION_LIFETIME', 86400); // 24 hours

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
?>
