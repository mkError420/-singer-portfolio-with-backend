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
        getCategories($db);
        break;
    case 'POST':
        createCategory($db);
        break;
    case 'PUT':
        updateCategory($db);
        break;
    case 'DELETE':
        deleteCategory($db);
        break;
    default:
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

function getCategories($db) {
    $query = "SELECT * FROM album_categories WHERE status = 'active' ORDER BY name ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If no categories in database, return default ones
    if (empty($categories)) {
        $categories = [
            ['name' => 'album', 'description' => 'Studio Albums'],
            ['name' => 'acoustic', 'description' => 'Acoustic Versions']
        ];
    }
    
    echo json_encode($categories);
}

function createCategory($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    // Check if category already exists
    $checkQuery = "SELECT id FROM album_categories WHERE name = :name AND status = 'active'";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':name', $data->name);
    $checkStmt->execute();
    
    if ($checkStmt->fetch()) {
        echo json_encode(['message' => 'Category already exists']);
        return;
    }
    
    $query = "INSERT INTO album_categories (name, description, status) 
              VALUES (:name, :description, 'active')";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':name', $data->name);
    $stmt->bindParam(':description', $data->description);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Category created successfully', 'id' => $db->lastInsertId()]);
    } else {
        echo json_encode(['message' => 'Category creation failed']);
    }
}

function updateCategory($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    $query = "UPDATE album_categories SET 
              name = :name, 
              description = :description 
              WHERE id = :id";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':name', $data->name);
    $stmt->bindParam(':description', $data->description);
    $stmt->bindParam(':id', $data->id);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Category updated successfully']);
    } else {
        echo json_encode(['message' => 'Category update failed']);
    }
}

function deleteCategory($db) {
    $data = json_decode(file_get_contents("php://input"));
    
    // Soft delete - set status to inactive
    $query = "UPDATE album_categories SET status = 'inactive' WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Category deleted successfully']);
    } else {
        echo json_encode(['message' => 'Category deletion failed']);
    }
}
?>
