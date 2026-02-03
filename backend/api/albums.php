<?php
header("Content-Type: application/json");
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
    $query = "SELECT a.*, 
                     (SELECT COUNT(*) FROM tracks t WHERE t.album_id = a.id AND t.status = 'active') as track_count
              FROM albums a 
              WHERE a.status = 'active' 
              ORDER BY a.year DESC, a.title ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get tracks for each album
    foreach ($albums as &$album) {
        $trackQuery = "SELECT * FROM tracks WHERE album_id = ? AND status = 'active' ORDER BY track_number ASC";
        $trackStmt = $db->prepare($trackQuery);
        $trackStmt->execute([$album['id']]);
        $album['tracks'] = $trackStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode($albums);
}

function createAlbum($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    $query = "INSERT INTO albums (title, year, category, cover_image, description) 
              VALUES (:title, :year, :category, :cover_image, :description)";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':title', $data->title);
    $stmt->bindParam(':year', $data->year);
    $stmt->bindParam(':category', $data->category);
    $stmt->bindParam(':cover_image', $data->cover_image);
    $stmt->bindParam(':description', $data->description);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Album created successfully', 'id' => $db->lastInsertId()]);
    } else {
        echo json_encode(['message' => 'Album creation failed']);
    }
}

function updateAlbum($db) {
    $data = json_decode(file_get_contents("php://input"));
    
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
        echo json_encode(['message' => 'Album updated successfully']);
    } else {
        echo json_encode(['message' => 'Album update failed']);
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
