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
        getMessages();
        break;
    case 'POST':
        createMessage();
        break;
    case 'PUT':
        updateMessage();
        break;
    case 'DELETE':
        deleteMessage();
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

function getMessages() {
    global $db;
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "SELECT * FROM contact_messages WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $message = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($message) {
            echo json_encode($message);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Message not found']);
        }
    } else {
        $query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($messages);
    }
}

function createMessage() {
    global $db;
    
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        $query = "INSERT INTO contact_messages (name, email, subject, message) 
                  VALUES (:name, :email, :subject, :message)";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':name', $data->name);
        $stmt->bindParam(':email', $data->email);
        $stmt->bindParam(':subject', $data->subject);
        $stmt->bindParam(':message', $data->message);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                'message' => 'Message created successfully',
                'id' => $db->lastInsertId()
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create message']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateMessage() {
    global $db;
    
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        $query = "UPDATE contact_messages SET 
                  status = :status 
                  WHERE id = :id";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':status', $data->status);
        $stmt->bindParam(':id', $data->id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Message updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update message']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteMessage() {
    global $db;
    
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        $query = "DELETE FROM contact_messages WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $data->id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Message deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to delete message']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
