<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getGallery($db);
        break;
    case 'POST':
        createGalleryItem($db);
        break;
    case 'PUT':
        updateGalleryItem($db);
        break;
    case 'DELETE':
        deleteGalleryItem($db);
        break;
    default:
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

function getGallery($db) {
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    
    $query = "SELECT * FROM gallery WHERE status = 'active'";
    
    if (!empty($category)) {
        $query .= " AND category = :category";
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $db->prepare($query);
    
    if (!empty($category)) {
        $stmt->bindParam(':category', $category);
    }
    
    $stmt->execute();
    $gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($gallery);
}

function createGalleryItem($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    $query = "INSERT INTO gallery (title, image, thumbnail, category, description) 
              VALUES (:title, :image, :thumbnail, :category, :description)";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':title', $data->title);
    $stmt->bindParam(':image', $data->image);
    $stmt->bindParam(':thumbnail', $data->thumbnail);
    $stmt->bindParam(':category', $data->category);
    $stmt->bindParam(':description', $data->description);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Gallery item created successfully', 'id' => $db->lastInsertId()]);
    } else {
        echo json_encode(['message' => 'Gallery item creation failed']);
    }
}

function updateGalleryItem($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    $query = "UPDATE gallery SET 
              title = :title, 
              image = :image, 
              thumbnail = :thumbnail, 
              category = :category, 
              description = :description 
              WHERE id = :id";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':title', $data->title);
    $stmt->bindParam(':image', $data->image);
    $stmt->bindParam(':thumbnail', $data->thumbnail);
    $stmt->bindParam(':category', $data->category);
    $stmt->bindParam(':description', $data->description);
    $stmt->bindParam(':id', $data->id);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Gallery item updated successfully']);
    } else {
        echo json_encode(['message' => 'Gallery item update failed']);
    }
}

function deleteGalleryItem($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    $query = "UPDATE gallery SET status = 'inactive' WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Gallery item deleted successfully']);
    } else {
        echo json_encode(['message' => 'Gallery item deletion failed']);
    }
}
?>
