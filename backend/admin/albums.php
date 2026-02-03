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
    <title>Manage Albums - Admin Dashboard</title>
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
        
        .btn-success {
            background: #27ae60;
            color: white;
        }
        
        .btn-edit {
            background: #f39c12;
            color: white;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .content-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .modal-header h2 {
            color: #2c3e50;
        }
        
        .close {
            font-size: 2rem;
            cursor: pointer;
            color: #7f8c8d;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }
        
        .album-cover {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .file-upload {
            margin-bottom: 1rem;
        }
        
        .file-upload input[type="file"] {
            display: none;
        }
        
        .file-upload-label {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #ecf0f1;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .file-upload-label:hover {
            background: #d5dbdb;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 1rem;
            border-radius: 5px;
            border: 1px solid #ddd;
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
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="albums.php" class="active">Albums</a></li>
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
                <h1>Manage Albums</h1>
                <button class="btn btn-primary" onclick="openModal()">Add New Album</button>
            </div>
            
            <div class="content-section">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Year</th>
                            <th>Category</th>
                            <th>Tracks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="albums-table">
                        <!-- Albums will be loaded here -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <!-- Album Modal -->
    <div id="albumModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Album</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="albumForm">
                <input type="hidden" id="albumId">
                
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="year">Year</label>
                    <input type="text" id="year" name="year" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="album">Album</option>
                        <option value="acoustic">Acoustic</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Cover Image</label>
                    <div class="file-upload">
                        <label for="coverImage" class="file-upload-label">Choose Image</label>
                        <input type="file" id="coverImage" accept="image/*" onchange="previewImage(this)">
                    </div>
                    <img id="imagePreview" class="preview-image" style="display: none;">
                    <input type="hidden" id="coverImagePath" name="cover_image">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Album</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let albums = [];
        let editingAlbum = null;
        
        // Load albums
        async function loadAlbums() {
            try {
                const response = await fetch('../api/albums.php');
                albums = await response.json();
                renderAlbums();
            } catch (error) {
                console.error('Error loading albums:', error);
            }
        }
        
        // Render albums table
        function renderAlbums() {
            const tbody = document.getElementById('albums-table');
            tbody.innerHTML = '';
            
            albums.forEach(album => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        ${album.cover_image ? `
                            <img src="../${album.cover_image}" 
                                 alt="${album.title}" class="album-cover"
                                 onerror="console.log('Failed to load: ${album.cover_image}'); this.src='https://via.placeholder.com/60x60/2a2a2a/ffffff?text=' + encodeURIComponent('${album.title}')">
                        ` : `<img src="https://via.placeholder.com/60x60" alt="${album.title}" class="album-cover">`}
                    </td>
                    <td>${album.title}</td>
                    <td>${album.year}</td>
                    <td>${album.category}</td>
                    <td>${album.track_count || 0}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-edit btn-sm" onclick="editAlbum(${album.id})">Edit</button>
                            <button class="btn btn-delete btn-sm" onclick="deleteAlbum(${album.id})">Delete</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // Open modal
        function openModal() {
            document.getElementById('albumModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Add New Album';
            document.getElementById('albumForm').reset();
            document.getElementById('imagePreview').style.display = 'none';
            editingAlbum = null;
        }
        
        // Close modal
        function closeModal() {
            document.getElementById('albumModal').style.display = 'none';
            editingAlbum = null;
        }
        
        // Edit album
        function editAlbum(id) {
            const album = albums.find(a => a.id === id);
            if (album) {
                editingAlbum = album;
                document.getElementById('albumId').value = album.id;
                document.getElementById('title').value = album.title;
                document.getElementById('year').value = album.year;
                document.getElementById('category').value = album.category;
                document.getElementById('description').value = album.description || '';
                
                if (album.cover_image) {
                    document.getElementById('imagePreview').src = '../' + album.cover_image;
                    document.getElementById('imagePreview').style.display = 'block';
                    document.getElementById('coverImagePath').value = album.cover_image;
                }
                
                document.getElementById('modalTitle').textContent = 'Edit Album';
                document.getElementById('albumModal').style.display = 'block';
            }
        }
        
        // Delete album
        async function deleteAlbum(id) {
            if (confirm('Are you sure you want to delete this album?')) {
                try {
                    const response = await fetch('../api/albums.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id })
                    });
                    
                    if (response.ok) {
                        loadAlbums();
                    } else {
                        alert('Error deleting album');
                    }
                } catch (error) {
                    console.error('Error deleting album:', error);
                    alert('Error deleting album');
                }
            }
        }
        
        // Preview image
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Handle form submission
        document.getElementById('albumForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            const albumData = {
                title: document.getElementById('title').value,
                year: document.getElementById('year').value,
                category: document.getElementById('category').value,
                description: document.getElementById('description').value,
                cover_image: document.getElementById('coverImagePath').value
            };
            
            // Handle image upload
            const imageFile = document.getElementById('coverImage').files[0];
            if (imageFile) {
                const uploadFormData = new FormData();
                uploadFormData.append('file', imageFile);
                
                try {
                    const uploadResponse = await fetch('../api/upload.php', {
                        method: 'POST',
                        body: uploadFormData
                    });
                    
                    const uploadResult = await uploadResponse.json();
                    if (uploadResult.success) {
                        albumData.cover_image = uploadResult.file_path;
                    }
                } catch (error) {
                    console.error('Error uploading image:', error);
                }
            }
            
            // Save album
            try {
                const method = editingAlbum ? 'PUT' : 'POST';
                const url = '../api/albums.php';
                
                if (editingAlbum) {
                    albumData.id = editingAlbum.id;
                }
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(albumData)
                });
                
                if (response.ok) {
                    closeModal();
                    loadAlbums();
                } else {
                    alert('Error saving album');
                }
            } catch (error) {
                console.error('Error saving album:', error);
                alert('Error saving album');
            }
        });
        
        // Load albums when page loads
        document.addEventListener('DOMContentLoaded', loadAlbums);
    </script>
</body>
</html>
