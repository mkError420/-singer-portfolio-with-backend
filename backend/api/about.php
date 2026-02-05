<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';

try {
    $db = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getAboutContent();
        break;
    case 'POST':
        createContent();
        break;
    case 'PUT':
        updateContent();
        break;
    case 'DELETE':
        deleteContent();
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

function getAboutContent() {
    global $db;
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "SELECT * FROM about_content WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $content = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($content) {
            echo json_encode($content);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Content not found']);
        }
    } else {
        $query = "SELECT * FROM about_content ORDER BY section_name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $content = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($content);
    }
}

function createContent() {
    global $db;
    
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        $query = "INSERT INTO about_content (section_name, content, image, status) 
                  VALUES (:section_name, :content, :image, :status)";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':section_name', $data->section_name);
        $stmt->bindParam(':content', $data->content);
        $stmt->bindParam(':image', $data->image);
        $stmt->bindParam(':status', $data->status);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                'message' => 'Content created successfully',
                'id' => $db->lastInsertId()
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create content']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateContent() {
    global $db;
    
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        $query = "UPDATE about_content SET 
                  section_name = :section_name, 
                  content = :content, 
                  image = :image, 
                  status = :status 
                  WHERE id = :id";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':section_name', $data->section_name);
        $stmt->bindParam(':content', $data->content);
        $stmt->bindParam(':image', $data->image);
        $stmt->bindParam(':status', $data->status);
        $stmt->bindParam(':id', $data->id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Content updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update content']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteContent() {
    global $db;
    
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        $query = "DELETE FROM about_content WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $data->id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Content deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to delete content']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
