<?php
// Check Apache Document Root
echo "<h1>🖥️ Apache Document Root Check</h1>";

echo "<h2>Server Information</h2>";
echo "<p><strong>Document Root:</strong> " . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "</p>";
echo "<p><strong>Script Name:</strong> " . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "</p>";
echo "<p><strong>Script Filename:</strong> " . htmlspecialchars($_SERVER['SCRIPT_FILENAME'] ?? 'Not set') . "</p>";
echo "<p><strong>Request URI:</strong> " . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'Not set') . "</p>";

echo "<h2>File System vs Web Path</h2>";
$scriptPath = __FILE__;
$webPath = $_SERVER['SCRIPT_NAME'] ?? '';

echo "<p><strong>Actual File Path:</strong> " . htmlspecialchars($scriptPath) . "</p>";
echo "<p><strong>Web Path:</strong> " . htmlspecialchars($webPath) . "</p>";

$expectedWebPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $scriptPath);
echo "<p><strong>Expected Web Path:</strong> " . htmlspecialchars($expectedWebPath) . "</p>";

echo "<h2>XAMPP Configuration</h2>";
echo "<p><strong>XAMPP通常安装在:</strong> C:/xampp</p>";
echo "<p><strong>Apache Document Root通常在:</strong> C:/xampp/htdocs</p>";
echo "<p><strong>你的项目应该在:</strong> C:/xampp/htdocs/madam-portfolio</p>";

echo "<h2>Test Different Paths</h2>";
$testPaths = [
    '/madam-portfolio/backend/api/albums.php',
    '/backend/api/albums.php',
    '/api/albums.php'
];

foreach ($testPaths as $path) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $path;
    $exists = file_exists($fullPath);
    echo "<p><strong>$path:</strong> " . ($exists ? '✅ File exists' : '❌ File not found') . "</p>";
    if ($exists) {
        echo "<p>  Full path: " . htmlspecialchars($fullPath) . "</p>";
    }
}

echo "<h2>Solution</h2>";
echo "<p><strong>如果文件存在但404:</strong></p>";
echo "<ul>";
echo "<li>检查Apache配置文件 (httpd.conf)</li>";
echo "<li>确认DocumentRoot指向正确的目录</li>";
echo "<li>检查.htaccess文件是否阻止访问</li>";
echo "</ul>";

echo "<p><strong>如果文件不存在:</strong></p>";
echo "<ul>";
echo "<li>确认文件在正确的位置</li>";
echo "<li>检查路径是否正确</li>";
echo "</ul>";
?>
