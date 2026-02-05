<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/config.php';

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

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
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

function getVideos() {
    global $db;
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "SELECT * FROM videos WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $video = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($video) {
            // Transform database fields to match frontend expectations
            $transformedVideo = [
                'id' => $video['id'],
                'title' => $video['title'],
                'description' => $video['description'],
                'thumbnail' => $video['thumbnail'],
                'duration' => $video['duration'],
                'category' => $video['category'],
                'videoId' => extractYouTubeId($video['video_url']),
                'views' => '0', // Default value since not stored in database
                'releaseDate' => date('Y', strtotime($video['created_at'])) // Use creation year
            ];
            echo json_encode($transformedVideo);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Video not found']);
        }
    } else {
        $query = "SELECT * FROM videos ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Transform each video to match frontend expectations
        $transformedVideos = array_map(function($video) {
            return [
                'id' => $video['id'],
                'title' => $video['title'],
                'description' => $video['description'],
                'thumbnail' => $video['thumbnail'],
                'duration' => $video['duration'],
                'category' => $video['category'],
                'videoId' => extractYouTubeId($video['video_url']),
                'views' => '0', // Default value since not stored in database
                'releaseDate' => date('Y', strtotime($video['created_at'])) // Use creation year
            ];
        }, $videos);
        
        echo json_encode($transformedVideos);
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

function createVideo() {
    global $db;
    
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        $query = "INSERT INTO videos (title, description, video_url) 
                  VALUES (:title, :description, :video_url)";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':title', $data->title);
        $stmt->bindParam(':description', $data->description);
        $stmt->bindParam(':video_url', $data->video_url);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                'message' => 'Video created successfully',
                'id' => $db->lastInsertId()
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create video']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateVideo() {
    global $db;
    
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        $query = "UPDATE videos SET 
                  title = :title, 
                  description = :description, 
                  video_url = :video_url 
                  WHERE id = :id";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':title', $data->title);
        $stmt->bindParam(':description', $data->description);
        $stmt->bindParam(':video_url', $data->video_url);
        $stmt->bindParam(':id', $data->id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Video updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update video']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteVideo() {
    global $db;
    
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        $query = "DELETE FROM videos WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $data->id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Video deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to delete video']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
