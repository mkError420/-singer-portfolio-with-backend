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
    <title>Manage Singles - Admin Dashboard</title>
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
        
        .single-cover {
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
        
        .youtube-link {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            color: #ff0000;
            text-decoration: none;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }
        
        .youtube-link:hover {
            background-color: #f8f8f8;
            text-decoration: none;
            color: #cc0000;
        }
        
        .single-cover {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
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
                <li><a href="singles.php" class="active">Singles</a></li>
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
                <h1>Manage Singles</h1>
                <button class="btn btn-primary" onclick="openModal()">Add New Single</button>
            </div>
            
            <div class="content-section">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Artist</th>
                            <th>Duration</th>
                            <th>Release Date</th>
                            <th>YouTube URL</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="singles-table">
                        <!-- Singles will be loaded here -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <!-- Single Modal -->
    <div id="singleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Single</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="singleForm">
                <input type="hidden" id="singleId">
                
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="artist">Artist</label>
                    <input type="text" id="artist" name="artist" required>
                </div>
                
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <input type="text" id="duration" name="duration" placeholder="3:45" required>
                </div>
                
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="text" id="release_date" name="release_date" placeholder="2024" required>
                </div>
                
                <div class="form-group">
                    <label for="youtube_url">YouTube URL</label>
                    <input type="url" id="youtube_url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
                    <small>Optional: Add YouTube link for this single</small>
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
                    <button type="submit" class="btn btn-primary">Save Single</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let singles = [];
        let editingSingle = null;
        
        // Load singles
        async function loadSingles() {
            try {
                const response = await fetch('../api/singles.php');
                singles = await response.json();
                renderSingles();
            } catch (error) {
                console.error('Error loading singles:', error);
            }
        }
        
        // Render singles table
        function renderSingles() {
            const tbody = document.getElementById('singles-table');
            tbody.innerHTML = '';
            
            singles.forEach(single => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <img src="${single.cover_image ? '../' + single.cover_image : 'https://via.placeholder.com/60x60'}" 
                             alt="${single.title}" class="single-cover"
                             onerror="this.src='https://via.placeholder.com/60x60/2a2a2a/ffffff?text=' + encodeURIComponent('${single.title}')">
                    </td>
                    <td>${single.title}</td>
                    <td>${single.artist}</td>
                    <td>${single.duration}</td>
                    <td>${single.release_date}</td>
                    <td>
                        ${single.youtube_url ? 
                            `<a href="${single.youtube_url}" target="_blank" class="youtube-link" title="Open YouTube">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="red">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                                YouTube
                            </a>` : 
                            '<span style="color: #999;">No URL</span>'
                        }
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-edit btn-sm" onclick="editSingle(${single.id})">Edit</button>
                            <button class="btn btn-delete btn-sm" onclick="deleteSingle(${single.id})">Delete</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // Open modal
        function openModal() {
            document.getElementById('singleModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Add New Single';
            document.getElementById('singleForm').reset();
            document.getElementById('imagePreview').style.display = 'none';
            editingSingle = null;
        }
        
        // Close modal
        function closeModal() {
            document.getElementById('singleModal').style.display = 'none';
            editingSingle = null;
        }
        
        // Edit single
        function editSingle(id) {
            const single = singles.find(s => s.id === id);
            if (single) {
                editingSingle = single;
                document.getElementById('singleId').value = single.id;
                document.getElementById('title').value = single.title;
                document.getElementById('artist').value = single.artist;
                document.getElementById('duration').value = single.duration;
                document.getElementById('release_date').value = single.release_date;
                document.getElementById('youtube_url').value = single.youtube_url || '';
                
                if (single.cover_image) {
                    document.getElementById('imagePreview').src = '../' + single.cover_image;
                    document.getElementById('imagePreview').style.display = 'block';
                    document.getElementById('coverImagePath').value = single.cover_image;
                }
                
                document.getElementById('modalTitle').textContent = 'Edit Single';
                document.getElementById('singleModal').style.display = 'block';
            }
        }
        
        // Delete single
        async function deleteSingle(id) {
            if (confirm('Are you sure you want to delete this single?')) {
                try {
                    const response = await fetch('../api/singles.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id })
                    });
                    
                    if (response.ok) {
                        loadSingles();
                    } else {
                        alert('Error deleting single');
                    }
                } catch (error) {
                    console.error('Error deleting single:', error);
                    alert('Error deleting single');
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
        document.getElementById('singleForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const singleData = {
                title: document.getElementById('title').value,
                artist: document.getElementById('artist').value,
                duration: document.getElementById('duration').value,
                release_date: document.getElementById('release_date').value,
                youtube_url: document.getElementById('youtube_url').value,
                cover_image: document.getElementById('coverImagePath').value
            };
            
            console.log('Single data to save:', singleData);
            
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
                        singleData.cover_image = uploadResult.file_path;
                    }
                } catch (error) {
                    console.error('Error uploading image:', error);
                }
            }
            
            // Save single
            try {
                const method = editingSingle ? 'PUT' : 'POST';
                const url = '../api/singles.php';
                
                if (editingSingle) {
                    singleData.id = editingSingle.id;
                }
                
                console.log('Sending request:', method, url);
                console.log('Request body:', JSON.stringify(singleData));
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(singleData)
                });
                
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('Save result:', result);
                    closeModal();
                    loadSingles();
                } else {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    alert('Error saving single: ' + errorText);
                }
            } catch (error) {
                console.error('Error saving single:', error);
                alert('Error saving single');
            }
        });
        
        // Load singles when page loads
        document.addEventListener('DOMContentLoaded', loadSingles);
    </script>
</body>
</html>
