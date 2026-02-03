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
    <title>Manage Gallery - Admin Dashboard</title>
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
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }
        
        .gallery-item {
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .gallery-item:hover {
            transform: translateY(-5px);
        }
        
        .gallery-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .gallery-info {
            padding: 1rem;
        }
        
        .gallery-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .gallery-category {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background: #3498db;
            color: white;
            border-radius: 3px;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }
        
        .gallery-description {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .gallery-actions {
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
            max-width: 100%;
            max-height: 300px;
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
                <li><a href="albums.php">Albums</a></li>
                <li><a href="singles.php">Singles</a></li>
                <li><a href="gallery.php" class="active">Gallery</a></li>
                <li><a href="videos.php">Videos</a></li>
                <li><a href="tour.php">Tour Dates</a></li>
                <li><a href="about.php">About Content</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="auth.php?action=logout">Logout</a></li>
            </ul>
        </aside>
        
        <main class="main-content">
            <div class="header">
                <h1>Manage Gallery</h1>
                <button class="btn btn-primary" onclick="openModal()">Add New Image</button>
            </div>
            
            <div class="content-section">
                <div class="gallery-grid" id="gallery-grid">
                    <!-- Gallery items will be loaded here -->
                </div>
            </div>
        </main>
    </div>
    
    <!-- Gallery Modal -->
    <div id="galleryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Image</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="galleryForm">
                <input type="hidden" id="galleryId">
                
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="performance">Performance</option>
                        <option value="studio">Studio</option>
                        <option value="behind">Behind the Scenes</option>
                        <option value="general">General</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Image</label>
                    <div class="file-upload">
                        <label for="imageFile" class="file-upload-label">Choose Image</label>
                        <input type="file" id="imageFile" accept="image/*" onchange="previewImage(this)" required>
                    </div>
                    <img id="imagePreview" class="preview-image" style="display: none;">
                    <input type="hidden" id="imagePath" name="image">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Image</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let galleryItems = [];
        let editingItem = null;
        
        // Load gallery items
        async function loadGallery() {
            try {
                const response = await fetch('../api/gallery.php');
                galleryItems = await response.json();
                renderGallery();
            } catch (error) {
                console.error('Error loading gallery:', error);
            }
        }
        
        // Render gallery grid
        function renderGallery() {
            const grid = document.getElementById('gallery-grid');
            grid.innerHTML = '';
            
            galleryItems.forEach(item => {
                const galleryItem = document.createElement('div');
                galleryItem.className = 'gallery-item';
                galleryItem.innerHTML = `
                    <img src="${item.image ? '../' + item.image : 'https://via.placeholder.com/300x200'}" 
                         alt="${item.title}" class="gallery-image"
                         onerror="this.src='https://via.placeholder.com/300x200/2a2a2a/ffffff?text=' + encodeURIComponent('${item.title}')">
                    <div class="gallery-info">
                        <div class="gallery-title">${item.title}</div>
                        <span class="gallery-category">${item.category}</span>
                        <div class="gallery-description">${item.description || ''}</div>
                        <div class="gallery-actions">
                            <button class="btn btn-edit btn-sm" onclick="editItem(${item.id})">Edit</button>
                            <button class="btn btn-delete btn-sm" onclick="deleteItem(${item.id})">Delete</button>
                        </div>
                    </div>
                `;
                grid.appendChild(galleryItem);
            });
        }
        
        // Open modal
        function openModal() {
            document.getElementById('galleryModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Add New Image';
            document.getElementById('galleryForm').reset();
            document.getElementById('imagePreview').style.display = 'none';
            editingItem = null;
        }
        
        // Close modal
        function closeModal() {
            document.getElementById('galleryModal').style.display = 'none';
            editingItem = null;
        }
        
        // Edit item
        function editItem(id) {
            const item = galleryItems.find(g => g.id === id);
            if (item) {
                editingItem = item;
                document.getElementById('galleryId').value = item.id;
                document.getElementById('title').value = item.title;
                document.getElementById('category').value = item.category;
                document.getElementById('description').value = item.description || '';
                
                if (item.image) {
                    document.getElementById('imagePreview').src = '../' + item.image;
                    document.getElementById('imagePreview').style.display = 'block';
                    document.getElementById('imagePath').value = item.image;
                }
                
                document.getElementById('modalTitle').textContent = 'Edit Image';
                document.getElementById('galleryModal').style.display = 'block';
            }
        }
        
        // Delete item
        async function deleteItem(id) {
            if (confirm('Are you sure you want to delete this image?')) {
                try {
                    const response = await fetch('../api/gallery.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id })
                    });
                    
                    if (response.ok) {
                        loadGallery();
                    } else {
                        alert('Error deleting image');
                    }
                } catch (error) {
                    console.error('Error deleting image:', error);
                    alert('Error deleting image');
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
        document.getElementById('galleryForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const galleryData = {
                title: document.getElementById('title').value,
                category: document.getElementById('category').value,
                description: document.getElementById('description').value,
                image: document.getElementById('imagePath').value
            };
            
            // Handle image upload
            const imageFile = document.getElementById('imageFile').files[0];
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
                        galleryData.image = uploadResult.file_path;
                    }
                } catch (error) {
                    console.error('Error uploading image:', error);
                }
            }
            
            // Save gallery item
            try {
                const method = editingItem ? 'PUT' : 'POST';
                const url = '../api/gallery.php';
                
                if (editingItem) {
                    galleryData.id = editingItem.id;
                }
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(galleryData)
                });
                
                if (response.ok) {
                    closeModal();
                    loadGallery();
                } else {
                    alert('Error saving image');
                }
            } catch (error) {
                console.error('Error saving image:', error);
                alert('Error saving image');
            }
        });
        
        // Load gallery when page loads
        document.addEventListener('DOMContentLoaded', loadGallery);
    </script>
</body>
</html>
