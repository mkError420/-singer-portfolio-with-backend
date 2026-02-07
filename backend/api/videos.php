<?php
// Aggressive error suppression to prevent HTML in JSON output
error_reporting(0);
ini_set('display_errors', 0);
ini_set('html_errors', 0);
ini_set('log_errors', 1);

// Clear any previous output
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
} catch(PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// Handle method override for PUT/DELETE via POST
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

// Debug logging
error_log('Request method: ' . $method);
error_log('POST data: ' . print_r($_POST, true));
error_log('Files data: ' . print_r($_FILES, true));

function getVideos() {
    global $db;
    
    try {
        $query = "SELECT * FROM videos ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Transform data for frontend
        $transformedVideos = [];
        foreach ($videos as $video) {
            $transformedVideos[] = [
                'id' => $video['id'],
                'title' => $video['title'],
                'description' => $video['description'],
                'videoId' => extractYouTubeId($video['video_url']),
                'thumbnail' => $video['thumbnail'],
                'duration' => $video['duration'],
                'category' => $video['category'],
                'views' => $video['views'] ?? '0',
                'releaseDate' => date('Y', strtotime($video['created_at']))
            ];
        }
        
        ob_end_clean();
        echo json_encode($transformedVideos);
        exit();
    } catch(PDOException $e) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
        exit();
    } catch(Exception $e) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['message' => 'Server error: ' . $e->getMessage()]);
        exit();
    }
}

// Helper function to extract YouTube video ID from URL
function extractYouTubeId($url) {
    // Handle different YouTube URL formats
    $patterns = [
        '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
        '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
        '/youtu\.be\/([a-zA-Z0-9_-]+)/'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }
    
    // If no match found, return the URL as-is or empty string
    return $url;
}

// Helper function to handle thumbnail upload
function uploadThumbnail($file) {
    // Simple validation without mime_content_type to avoid errors
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $uploadDir = __DIR__ . '/../../public/images/thumbnails/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = uniqid('thumb_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return '/madam-portfolio/public/images/thumbnails/' . $filename;
    }
    
    return null;
}

switch ($method) {
    case 'GET':
        getVideos();
        break;
    case 'POST':
        createVideo();
        break;
    case 'PUT':
        updateVideo();
        break;
    case 'DELETE':
        deleteVideo();
        break;
    default:
        ob_end_clean();
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
?>
