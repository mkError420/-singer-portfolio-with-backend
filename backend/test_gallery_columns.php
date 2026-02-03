<?php
// Test script to check and add gallery date columns
require_once __DIR__ . '/../config/database.php';

echo "🔍 Checking gallery table structure...\n";

$database = new Database();
$db = $database->getConnection();

// Check if columns exist
$checkColumns = "
    SELECT COLUMN_NAME 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'madam_portfolio' 
    AND TABLE_NAME = 'gallery' 
    AND COLUMN_NAME IN ('upload_month', 'upload_year')
";

$stmt = $db->prepare($checkColumns);
$stmt->execute();
$existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "📊 Existing columns: " . implode(', ', $existingColumns) . "\n";

// Add missing columns
if (!in_array('upload_month', $existingColumns)) {
    echo "➕ Adding upload_month column...\n";
    $db->exec("ALTER TABLE gallery ADD COLUMN upload_month VARCHAR(2) AFTER description");
} else {
    echo "✅ upload_month column already exists\n";
}

if (!in_array('upload_year', $existingColumns)) {
    echo "➕ Adding upload_year column...\n";
    $db->exec("ALTER TABLE gallery ADD COLUMN upload_year VARCHAR(4) AFTER upload_month");
} else {
    echo "✅ upload_year column already exists\n";
}

// Update existing records
echo "🔄 Updating existing records...\n";
$updateQuery = "
    UPDATE gallery 
    SET 
        upload_month = COALESCE(upload_month, MONTH(created_at)),
        upload_year = COALESCE(upload_year, YEAR(created_at))
    WHERE upload_month IS NULL OR upload_year IS NULL
";

$result = $db->exec($updateQuery);
echo "✅ Updated $result existing records\n";

// Show table structure
echo "\n📋 Current gallery table structure:\n";
$describeQuery = "DESCRIBE gallery";
$stmt = $db->prepare($describeQuery);
$stmt->execute();
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($columns as $column) {
    echo "  - {$column['Field']} ({$column['Type']})\n";
}

echo "\n🎉 Gallery table setup complete!\n";
?>
