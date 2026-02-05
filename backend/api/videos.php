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

// Direct database connection
try {
    $db = new PDO("mysql:host=localhost;dbname=madam_portfolio", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// Handle method override for PUT/DELETE via POST
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
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

// Helper function to handle thumbnail upload
function uploadThumbnail($file) {
    try {
        $uploadDir = __DIR__ . '/../../public/images/thumbnails/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception('Failed to create upload directory.');
            }
        }
        
        // Check if file was uploaded
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error: ' . $file['error']);
        }
        
        // Generate unique filename
        $filename = uniqid('thumb_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filepath = $uploadDir . $filename;
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.');
        }
        
        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception('File too large. Maximum size is 5MB.');
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Return forward slashes for web compatibility
            return '/madam-portfolio/public/images/thumbnails/' . $filename;
        } else {
            throw new Exception('Failed to move uploaded file.');
        }
    } catch (Exception $e) {
        error_log('Upload error: ' . $e->getMessage());
        throw $e;
    }
}

function createVideo() {
    global $db;
    
    try {
        // Handle multipart form data
        if (isset($_POST['video_data'])) {
            $data = json_decode($_POST['video_data']);
            
            if ($data === null) {
                throw new Exception('Invalid JSON data in video_data field');
            }
            
            // Handle file upload (make it optional for testing)
            $thumbnailPath = null;
            if (isset($_FILES['thumbnail_file']) && $_FILES['thumbnail_file']['error'] === UPLOAD_ERR_OK) {
                $thumbnailPath = uploadThumbnail($_FILES['thumbnail_file']);
            }
            
            $query = "INSERT INTO videos (title, description, video_url, thumbnail, category, duration) 
                      VALUES (:title, :description, :video_url, :thumbnail, :category, :duration)";
            
            $stmt = $db->prepare($query);
            
            $stmt->bindValue(':title', $data->title);
            $stmt->bindValue(':description', $data->description ?? '');
            $stmt->bindValue(':video_url', $data->video_url);
            $stmt->bindValue(':thumbnail', $thumbnailPath);
            $stmt->bindValue(':category', $data->category);
            $stmt->bindValue(':duration', $data->duration ?? null);
            
            if ($stmt->execute()) {
                $newId = $db->lastInsertId();
                http_response_code(201);
                echo json_encode([
                    'message' => 'Video created successfully',
                    'id' => $newId
                ]);
            } else {
                $errorInfo = $stmt->errorInfo();
                http_response_code(500);
                echo json_encode(['message' => 'Failed to create video: ' . $errorInfo[2]]);
            }
        } else {
            throw new Exception('No video_data found in POST request');
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    } catch(Exception $e) {
        http_response_code(400);
        echo json_encode(['message' => $e->getMessage()]);
    }
}

function updateVideo() {
    global $db;
    
    try {
        // Handle multipart form data
        if (isset($_POST['video_data'])) {
            $data = json_decode($_POST['video_data']);
            
            // Handle file upload
            $thumbnailPath = null;
            if (isset($_FILES['thumbnail_file']) && $_FILES['thumbnail_file']['error'] === UPLOAD_ERR_OK) {
                $thumbnailPath = uploadThumbnail($_FILES['thumbnail_file']);
            } else {
                // Keep existing thumbnail if no new file uploaded
                $stmt = $db->prepare("SELECT thumbnail FROM videos WHERE id = :id");
                $stmt->bindParam(':id', $data->id);
                $stmt->execute();
                $existingVideo = $stmt->fetch(PDO::FETCH_ASSOC);
                $thumbnailPath = $existingVideo['thumbnail'];
            }
            
            $query = "UPDATE videos SET 
                      title = :title, 
                      description = :description, 
                      video_url = :video_url, 
                      thumbnail = :thumbnail, 
                      category = :category, 
                      duration = :duration 
                      WHERE id = :id";
            
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':title', $data->title);
            $stmt->bindParam(':description', $data->description);
            $stmt->bindParam(':video_url', $data->video_url);
            $stmt->bindParam(':thumbnail', $thumbnailPath);
            $stmt->bindParam(':category', $data->category);
            $stmt->bindParam(':duration', $data->duration ?? null);
            $stmt->bindParam(':id', $data->id);
            
            if ($stmt->execute()) {
                echo json_encode(['message' => 'Video updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to update video']);
            }
        } else {
            // Fallback to JSON for backward compatibility
            $data = json_decode(file_get_contents("php://input"));
            
            $query = "UPDATE videos SET 
                      title = :title, 
                      description = :description, 
                      video_url = :video_url, 
                      thumbnail = :thumbnail, 
                      category = :category, 
                      duration = :duration 
                      WHERE id = :id";
            
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':title', $data->title);
            $stmt->bindParam(':description', $data->description);
            $stmt->bindParam(':video_url', $data->video_url);
            $stmt->bindParam(':thumbnail', $data->thumbnail ?? null);
            $stmt->bindParam(':category', $data->category);
            $stmt->bindParam(':duration', $data->duration ?? null);
            $stmt->bindParam(':id', $data->id);
            
            if ($stmt->execute()) {
                echo json_encode(['message' => 'Video updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to update video']);
            }
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    } catch(Exception $e) {
        http_response_code(400);
        echo json_encode(['message' => $e->getMessage()]);
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
