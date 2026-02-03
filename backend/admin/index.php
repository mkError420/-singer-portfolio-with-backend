<?php
require_once 'auth.php';
$auth = new Auth();
$auth->requireLogin();

$currentUser = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Madam Portfolio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 2rem 0;
        }
        
        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar-header h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .sidebar-header p {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #34495e;
        }
        
        .main-content {
            flex: 1;
            padding: 2rem;
        }
        
        .header {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #2c3e50;
        }
        
        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s ease;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-card p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .content-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .section-header h2 {
            color: #2c3e50;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .table-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .btn-edit {
            background: #f39c12;
            color: white;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Welcome, <?php echo htmlspecialchars($currentUser['username']); ?></p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="albums.php">Albums</a></li>
                <li><a href="singles.php">Singles</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="videos.php">Videos</a></li>
                <li><a href="tour.php">Tour Dates</a></li>
                <li><a href="about.php">About Content</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="auth.php?action=logout">Logout</a></li>
            </ul>
        </aside>
        
        <main class="main-content">
            <div class="header">
                <h1>Dashboard Overview</h1>
                <div class="header-actions">
                    <a href="../index.html" class="btn btn-primary">View Website</a>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3 id="albums-count">0</h3>
                    <p>Albums</p>
                </div>
                <div class="stat-card">
                    <h3 id="singles-count">0</h3>
                    <p>Singles</p>
                </div>
                <div class="stat-card">
                    <h3 id="gallery-count">0</h3>
                    <p>Gallery Images</p>
                </div>
                <div class="stat-card">
                    <h3 id="messages-count">0</h3>
                    <p>New Messages</p>
                </div>
            </div>
            
            <div class="content-section">
                <div class="section-header">
                    <h2>Recent Activity</h2>
                </div>
                <p>Welcome to the Madam Portfolio Admin Dashboard. Use the sidebar to manage your website content.</p>
            </div>
        </main>
    </div>
    
    <script>
        // Load dashboard statistics
        async function loadStats() {
            try {
                // Load albums count
                const albumsResponse = await fetch('../api/albums.php');
                const albums = await albumsResponse.json();
                document.getElementById('albums-count').textContent = albums.length;
                
                // Load singles count
                const singlesResponse = await fetch('../api/singles.php');
                const singles = await singlesResponse.json();
                document.getElementById('singles-count').textContent = singles.length;
                
                // Load gallery count
                const galleryResponse = await fetch('../api/gallery.php');
                const gallery = await galleryResponse.json();
                document.getElementById('gallery-count').textContent = gallery.length;
                
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }
        
        // Load stats when page loads
        document.addEventListener('DOMContentLoaded', loadStats);
    </script>
</body>
</html>
