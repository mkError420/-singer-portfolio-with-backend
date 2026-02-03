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
        getSingles($db);
        break;
    case 'POST':
        createSingle($db);
        break;
    case 'PUT':
        updateSingle($db);
        break;
    case 'DELETE':
        deleteSingle($db);
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

function getSingles($db) {
    $query = "SELECT * FROM singles WHERE status = 'active' ORDER BY release_date DESC, title ASC";
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $singles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($singles);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to retrieve singles: ' . $e->getMessage()]);
    }
}

function createSingle($db) {
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->title) || empty($data->artist)) {
        http_response_code(400);
        echo json_encode(['message' => 'Title and Artist are required.']);
        return;
    }
    
    $query = "INSERT INTO singles (title, duration, artist, cover_image, youtube_url, release_date, audio_file) 
              VALUES (:title, :duration, :artist, :cover_image, :youtube_url, :release_date, :audio_file)";
    
    try {
        $stmt = $db->prepare($query);
        
        $stmt->bindValue(':title', $data->title ?? '');
        $stmt->bindValue(':duration', $data->duration ?? null);
        $stmt->bindValue(':artist', $data->artist ?? '');
        $stmt->bindValue(':cover_image', $data->cover_image ?? null);
        $stmt->bindValue(':youtube_url', $data->youtube_url ?? null);
        $stmt->bindValue(':release_date', $data->release_date ?? null);
        $stmt->bindValue(':audio_file', $data->audio_file ?? null);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(['message' => 'Single created successfully', 'id' => $db->lastInsertId()]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Single creation failed.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error during single creation: ' . $e->getMessage()]);
    }
}

function updateSingle($db) {
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->id) || empty($data->title) || empty($data->artist)) {
        http_response_code(400);
        echo json_encode(['message' => 'ID, Title, and Artist are required.']);
        return;
    }
    
    $query = "UPDATE singles SET 
              title = :title, 
              duration = :duration, 
              artist = :artist, 
              cover_image = :cover_image, 
              youtube_url = :youtube_url,
              release_date = :release_date, 
              audio_file = :audio_file 
              WHERE id = :id";
    
    try {
        $stmt = $db->prepare($query);
        
        $stmt->bindValue(':title', $data->title ?? '');
        $stmt->bindValue(':duration', $data->duration ?? null);
        $stmt->bindValue(':artist', $data->artist ?? '');
        $stmt->bindValue(':cover_image', $data->cover_image ?? null);
        $stmt->bindValue(':youtube_url', $data->youtube_url ?? null);
        $stmt->bindValue(':release_date', $data->release_date ?? null);
        $stmt->bindValue(':audio_file', $data->audio_file ?? null);
        $stmt->bindValue(':id', $data->id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Single updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Single update failed.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error during single update: ' . $e->getMessage()]);
    }
}

function deleteSingle($db) {
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->id)) {
        http_response_code(400);
        echo json_encode(['message' => 'Single ID not provided for deletion.']);
        return;
    }
    
    $query = "UPDATE singles SET status = 'inactive' WHERE id = :id";
    
    try {
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $data->id);
        
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(['message' => 'Single deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Single not found or already deleted.']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Single deletion failed.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error during single deletion: ' . $e->getMessage()]);
    }
}
?>
