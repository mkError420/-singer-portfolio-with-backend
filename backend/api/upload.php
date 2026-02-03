<?php
// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];
$uploadDir = UPLOAD_PATH;

// Create upload directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Validate file
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Upload error: ' . $file['error']]);
    exit;
}

if ($file['size'] > MAX_FILE_SIZE) {
    echo json_encode(['success' => false, 'message' => 'File too large']);
    exit;
}

$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileType = mime_content_type($fileTmpName);

// Get file extension
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Validate file extension
if (!in_array($fileExt, ALLOWED_EXTENSIONS)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

// Generate unique filename
$newFileName = uniqid('upload_', true) . '.' . $fileExt;
$uploadPath = $uploadDir . $newFileName;

// Move file to upload directory
if (move_uploaded_file($fileTmpName, $uploadPath)) {
    // Return the relative path for database storage
    $relativePath = 'uploads/' . $newFileName;
    
    echo json_encode([
        'success' => true,
        'message' => 'File uploaded successfully',
        'file_path' => $relativePath,
        'file_name' => $newFileName,
        'file_size' => $fileSize,
        'file_type' => $fileType
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
}
?>
