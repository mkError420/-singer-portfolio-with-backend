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
    // Clear any previous output
    if (ob_get_level()) {
        ob_clean();
    }
    
    error_log("🔄 Creating gallery item");
    
    $data = json_decode(file_get_contents("php://input"));
    error_log("📊 Received data: " . print_r($data, true));
    
    // Validate required fields
    if (!$data || !isset($data->title) || !isset($data->category)) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required fields: title and category are required']);
        return;
    }
    
    // Check if columns exist, add them if they don't
    try {
        $checkColumns = "SHOW COLUMNS FROM gallery LIKE 'upload_month'";
        $stmt = $db->prepare($checkColumns);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            // Add upload_month column
            $db->exec("ALTER TABLE gallery ADD COLUMN upload_month VARCHAR(2) AFTER description");
        }
        
        $checkColumns = "SHOW COLUMNS FROM gallery LIKE 'upload_year'";
        $stmt = $db->prepare($checkColumns);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            // Add upload_year column
            $db->exec("ALTER TABLE gallery ADD COLUMN upload_year VARCHAR(4) AFTER upload_month");
        }
    } catch (Exception $e) {
        error_log("⚠️ Column check failed: " . $e->getMessage());
    }
    
    $query = "INSERT INTO gallery (title, image, thumbnail, category, description, upload_month, upload_year) 
              VALUES (:title, :image, :thumbnail, :category, :description, :upload_month, :upload_year)";
    
    try {
        $stmt = $db->prepare($query);
        
        $title = $data->title ?? '';
        $image = $data->image ?? '';
        $thumbnail = $data->thumbnail ?? null;
        $category = $data->category ?? 'general';
        $description = $data->description ?? '';
        $upload_month = $data->upload_month ?? null;
        $upload_year = $data->upload_year ?? null;
        
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':thumbnail', $thumbnail);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':upload_month', $upload_month);
        $stmt->bindParam(':upload_year', $upload_year);
        
        if ($stmt->execute()) {
            $id = $db->lastInsertId();
            error_log("✅ Gallery item created successfully with ID: " . $id);
            echo json_encode(['message' => 'Gallery item created successfully', 'id' => $id]);
        } else {
            $error = $stmt->errorInfo();
            error_log("❌ Gallery item creation failed: " . print_r($error, true));
            http_response_code(500);
            echo json_encode(['message' => 'Gallery item creation failed: ' . $error[2]]);
        }
    } catch (Exception $e) {
        error_log("❌ Database error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateGalleryItem($db) {
    // Clear any previous output
    if (ob_get_level()) {
        ob_clean();
    }
    
    error_log("🔄 Updating gallery item");
    
    $data = json_decode(file_get_contents("php://input"));
    error_log("📊 Received update data: " . print_r($data, true));
    
    // Validate required fields
    if (!$data || !isset($data->id) || !isset($data->title) || !isset($data->category)) {
        http_response_code(400);
        echo json_encode(['message' => 'Missing required fields: id, title, and category are required']);
        return;
    }
    
    // Check if columns exist, add them if they don't
    try {
        $checkColumns = "SHOW COLUMNS FROM gallery LIKE 'upload_month'";
        $stmt = $db->prepare($checkColumns);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            // Add upload_month column
            $db->exec("ALTER TABLE gallery ADD COLUMN upload_month VARCHAR(2) AFTER description");
        }
        
        $checkColumns = "SHOW COLUMNS FROM gallery LIKE 'upload_year'";
        $stmt = $db->prepare($checkColumns);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            // Add upload_year column
            $db->exec("ALTER TABLE gallery ADD COLUMN upload_year VARCHAR(4) AFTER upload_month");
        }
    } catch (Exception $e) {
        error_log("⚠️ Column check failed: " . $e->getMessage());
    }
    
    $query = "UPDATE gallery SET 
              title = :title, 
              image = :image, 
              thumbnail = :thumbnail, 
              category = :category, 
              description = :description,
              upload_month = :upload_month,
              upload_year = :upload_year
              WHERE id = :id";
    
    try {
        $stmt = $db->prepare($query);
        
        $id = $data->id;
        $title = $data->title ?? '';
        $image = $data->image ?? '';
        $thumbnail = $data->thumbnail ?? null;
        $category = $data->category ?? 'general';
        $description = $data->description ?? '';
        $upload_month = $data->upload_month ?? null;
        $upload_year = $data->upload_year ?? null;
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':thumbnail', $thumbnail);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':upload_month', $upload_month);
        $stmt->bindParam(':upload_year', $upload_year);
        
        if ($stmt->execute()) {
            error_log("✅ Gallery item updated successfully");
            echo json_encode(['message' => 'Gallery item updated successfully']);
        } else {
            $error = $stmt->errorInfo();
            error_log("❌ Gallery item update failed: " . print_r($error, true));
            http_response_code(500);
            echo json_encode(['message' => 'Gallery item update failed: ' . $error[2]]);
        }
    } catch (Exception $e) {
        error_log("❌ Database error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
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
