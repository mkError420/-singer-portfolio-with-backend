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
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

function getSingles($db) {
    $query = "SELECT * FROM singles WHERE status = 'active' ORDER BY release_date DESC, title ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $singles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($singles);
}

function createSingle($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    $query = "INSERT INTO singles (title, duration, artist, cover_image, release_date, audio_file) 
              VALUES (:title, :duration, :artist, :cover_image, :release_date, :audio_file)";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':title', $data->title);
    $stmt->bindParam(':duration', $data->duration);
    $stmt->bindParam(':artist', $data->artist);
    $stmt->bindParam(':cover_image', $data->cover_image);
    $stmt->bindParam(':release_date', $data->release_date);
    $stmt->bindParam(':audio_file', $data->audio_file);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Single created successfully', 'id' => $db->lastInsertId()]);
    } else {
        echo json_encode(['message' => 'Single creation failed']);
    }
}

function updateSingle($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    $query = "UPDATE singles SET 
              title = :title, 
              duration = :duration, 
              artist = :artist, 
              cover_image = :cover_image, 
              release_date = :release_date, 
              audio_file = :audio_file 
              WHERE id = :id";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':title', $data->title);
    $stmt->bindParam(':duration', $data->duration);
    $stmt->bindParam(':artist', $data->artist);
    $stmt->bindParam(':cover_image', $data->cover_image);
    $stmt->bindParam(':release_date', $data->release_date);
    $stmt->bindParam(':audio_file', $data->audio_file);
    $stmt->bindParam(':id', $data->id);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Single updated successfully']);
    } else {
        echo json_encode(['message' => 'Single update failed']);
    }
}

function deleteSingle($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    $query = "UPDATE singles SET status = 'inactive' WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Single deleted successfully']);
    } else {
        echo json_encode(['message' => 'Single deletion failed']);
    }
}
?>
