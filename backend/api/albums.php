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

require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getAlbums($db);
        break;
    case 'POST':
        createAlbum($db);
        break;
    case 'PUT':
        updateAlbum($db);
        break;
    case 'DELETE':
        deleteAlbum($db);
        break;
    default:
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

function getAlbums($db) {
    // Check if requesting specific album with tracks
    if (isset($_GET['album_id']) && isset($_GET['include_tracks'])) {
        $albumId = $_GET['album_id'];
        
        // Get album details
        $query = "SELECT * FROM albums WHERE id = ? AND status = 'active'";
        $stmt = $db->prepare($query);
        $stmt->execute([$albumId]);
        $album = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($album) {
            // Get tracks for this album
            $trackQuery = "SELECT * FROM tracks WHERE album_id = ? AND status = 'active' ORDER BY track_number ASC";
            $trackStmt = $db->prepare($trackQuery);
            $trackStmt->execute([$albumId]);
            $album['tracks'] = $trackStmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($album);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Album not found']);
        }
    } else {
        // Get all albums
        $query = "SELECT a.*, 
                        (SELECT COUNT(*) FROM tracks t WHERE t.album_id = a.id AND t.status = 'active') as track_count
                 FROM albums a 
                 WHERE a.status = 'active' 
                 ORDER BY a.year DESC, a.title ASC";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($albums);
    }
}

function createAlbum($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    // Debug logging
    error_log("Album creation data received: " . json_encode($data));
    error_log("Category value: " . ($data->category ?? 'NULL'));
    
    try {
        // Start transaction
        $db->beginTransaction();
        
        // Insert album
        $query = "INSERT INTO albums (title, year, category, cover_image, description) 
                  VALUES (:title, :year, :category, :cover_image, :description)";
        
        $stmt = $db->prepare($query);
        
        $title = $data->title ?? '';
        $year = $data->year ?? '';
        $category = $data->category ?? '';
        $cover_image = $data->cover_image ?? '';
        $description = $data->description ?? '';
        
        error_log("Prepared values - Title: $title, Year: $year, Category: '$category'");
        
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':cover_image', $cover_image);
        $stmt->bindParam(':description', $description);
        
        if ($stmt->execute()) {
            $albumId = $db->lastInsertId();
            
            // Insert tracks if provided
            if (!empty($data->tracks)) {
                foreach ($data->tracks as $track) {
                    $trackQuery = "INSERT INTO tracks (album_id, title, youtube_url, duration, track_number, status) 
                                  VALUES (:album_id, :title, :youtube_url, :duration, :track_number, 'active')";
                    
                    $trackStmt = $db->prepare($trackQuery);
                    $trackStmt->bindParam(':album_id', $albumId);
                    $trackStmt->bindParam(':title', $track->title);
                    $trackStmt->bindParam(':youtube_url', $track->youtube_url);
                    $trackStmt->bindParam(':duration', $track->duration);
                    $trackStmt->bindParam(':track_number', $track->track_number);
                    $trackStmt->execute();
                }
            }
            
            $db->commit();
            echo json_encode(['message' => 'Album created successfully', 'id' => $albumId]);
        } else {
            throw new Exception('Album creation failed');
        }
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['message' => 'Album creation failed: ' . $e->getMessage()]);
    }
}

function updateAlbum($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        // Start transaction
        $db->beginTransaction();
        
        // Update album
        $query = "UPDATE albums SET 
                  title = :title, 
                  year = :year, 
                  category = :category, 
                  cover_image = :cover_image, 
                  description = :description 
                  WHERE id = :id";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':title', $data->title);
        $stmt->bindParam(':year', $data->year);
        $stmt->bindParam(':category', $data->category);
        $stmt->bindParam(':cover_image', $data->cover_image);
        $stmt->bindParam(':description', $data->description);
        $stmt->bindParam(':id', $data->id);
        
        if ($stmt->execute()) {
            // Delete existing tracks for this album
            $deleteTracksQuery = "DELETE FROM tracks WHERE album_id = :album_id";
            $deleteStmt = $db->prepare($deleteTracksQuery);
            $deleteStmt->bindParam(':album_id', $data->id);
            $deleteStmt->execute();
            
            // Insert new tracks if provided
            if (!empty($data->tracks)) {
                foreach ($data->tracks as $track) {
                    $trackQuery = "INSERT INTO tracks (album_id, title, youtube_url, duration, track_number, status) 
                                  VALUES (:album_id, :title, :youtube_url, :duration, :track_number, 'active')";
                    
                    $trackStmt = $db->prepare($trackQuery);
                    $trackStmt->bindParam(':album_id', $data->id);
                    $trackStmt->bindParam(':title', $track->title);
                    $trackStmt->bindParam(':youtube_url', $track->youtube_url);
                    $trackStmt->bindParam(':duration', $track->duration);
                    $trackStmt->bindParam(':track_number', $track->track_number);
                    $trackStmt->execute();
                }
            }
            
            $db->commit();
            echo json_encode(['message' => 'Album updated successfully']);
        } else {
            throw new Exception('Album update failed');
        }
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['message' => 'Album update failed: ' . $e->getMessage()]);
    }
}

function deleteAlbum($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    $query = "UPDATE albums SET status = 'inactive' WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Album deleted successfully']);
    } else {
        echo json_encode(['message' => 'Album deletion failed']);
    }
}
?>
