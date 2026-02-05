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
        getTourDates();
        break;
    case 'POST':
        createTourDate();
        break;
    case 'PUT':
        updateTourDate();
        break;
    case 'DELETE':
        deleteTourDate();
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

function getTourDates() {
    global $db;
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "SELECT * FROM tour_dates WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $tourDate = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tourDate) {
            echo json_encode($tourDate);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Tour date not found']);
        }
    } else {
        $query = "SELECT * FROM tour_dates ORDER BY date ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $tourDates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($tourDates);
    }
}

function createTourDate() {
    global $db;
    
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        $query = "INSERT INTO tour_dates (venue, location, date, time, ticket_url, status) 
                  VALUES (:venue, :location, :date, :time, :ticket_url, :status)";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':venue', $data->venue);
        $stmt->bindParam(':location', $data->location);
        $stmt->bindParam(':date', $data->date);
        $stmt->bindParam(':time', $data->time);
        $stmt->bindParam(':ticket_url', $data->ticket_url);
        $stmt->bindParam(':status', $data->status);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                'message' => 'Tour date created successfully',
                'id' => $db->lastInsertId()
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create tour date']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateTourDate() {
    global $db;
    
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        $query = "UPDATE tour_dates SET 
                  venue = :venue, 
                  location = :location, 
                  date = :date, 
                  time = :time, 
                  ticket_url = :ticket_url, 
                  status = :status 
                  WHERE id = :id";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':venue', $data->venue);
        $stmt->bindParam(':location', $data->location);
        $stmt->bindParam(':date', $data->date);
        $stmt->bindParam(':time', $data->time);
        $stmt->bindParam(':ticket_url', $data->ticket_url);
        $stmt->bindParam(':status', $data->status);
        $stmt->bindParam(':id', $data->id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Tour date updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update tour date']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteTourDate() {
    global $db;
    
    $data = json_decode(file_get_contents("php://input"));
    
    try {
        $query = "DELETE FROM tour_dates WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $data->id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Tour date deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to delete tour date']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
