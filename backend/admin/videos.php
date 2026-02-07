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
    <title>Videos - Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
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
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(240, 147, 251, 0.4);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .content-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .table th,
        .table td {
            padding: 1rem;
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
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #7f8c8d;
        }
        
        .empty-state h3 {
            margin-bottom: 1rem;
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
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="videos.php" class="active">Videos</a></li>
                <li><a href="tour.php">Tour Dates</a></li>
                <li><a href="about.php">About Content</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="auth.php?action=logout">Logout</a></li>
            </ul>
        </aside>
        
        <main class="main-content">
            <div class="header">
                <h1>Videos</h1>
                <button class="btn btn-primary" onclick="openModal()">Add New Video</button>
            </div>
            
            <div class="content-section">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Duration</th>
                            <th>Cover Image</th>
                            <th>Video URL</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="videos-table">
                        <!-- Videos will be loaded here -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <!-- Video Modal -->
    <div id="videoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Video</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="videoForm" enctype="multipart/form-data">
                <input type="hidden" id="videoId">
                
                <div class="form-group">
                    <label for="videoTitle">Title</label>
                    <input type="text" id="videoTitle" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="videoCategory">Category</label>
                    <select id="videoCategory" name="category" required>
                        <option value="">Select Category</option>
                        <option value="music">Music</option>
                        <option value="live">Live</option>
                        <option value="behind">Behind the Scenes</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="videoDescription">Description</label>
                    <textarea id="videoDescription" name="description"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="videoUrl">Video URL</label>
                    <input type="url" id="videoUrl" name="video_url" required>
                </div>
                
                <div class="form-group">
                    <label for="videoDuration">Duration</label>
                    <input type="text" id="videoDuration" name="duration" placeholder="e.g., 3:45">
                </div>
                
                <div class="form-group">
                    <label for="videoThumbnail">Cover Image</label>
                    <input type="file" id="videoThumbnail" name="thumbnail" accept="image/*">
                    <div id="currentThumbnail" style="margin-top: 0.5rem; display: none;">
                        <small style="color: #666; font-size: 0.85rem;">Current thumbnail:</small><br>
                        <img id="currentThumbnailImg" src="" alt="Current thumbnail" style="max-width: 100px; max-height: 100px; border-radius: 4px; margin-top: 0.25rem;">
                    </div>
                    <small style="color: #666; font-size: 0.85rem; margin-top: 0.25rem; display: block;">
                        Upload an image file (JPG, PNG, GIF, WebP). Leave empty to keep existing thumbnail.
                    </small>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Video</button>
                </div>
            </form>
        </div>
    </div>
    
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
        }
        
        .modal-content {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            margin: 5% auto;
            padding: 2.5rem;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .modal-header h2 {
            color: #2c3e50;
            margin: 0;
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
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            table-layout: auto;
        }
        
        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: middle;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
            white-space: nowrap;
        }
        
        .table td:nth-child(3) {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .table td:nth-child(5) {
            width: 60px;
            text-align: center;
        }
    </style>
    
    <script>
        let videos = [];
        let editingVideo = null;
        
        // Load videos from database
        async function loadVideos() {
            try {
                const response = await fetch('/madam-portfolio/backend/api/videos.php');
                if (response.ok) {
                    videos = await response.json();
                } else {
                    console.error('Failed to load videos');
                    videos = [];
                }
                renderVideos();
            } catch (error) {
                console.error('Error loading videos:', error);
                videos = [];
                renderVideos();
            }
        }
        
        // Get category display name
        function getCategoryDisplay(category) {
            const categoryMap = {
                'music': 'Music',
                'live': 'Live Performance',
                'behind': 'Behind the Scenes'
            };
            return categoryMap[category] || category;
        }
        
        // Render videos table
        function renderVideos() {
            const tbody = document.getElementById('videos-table');
            tbody.innerHTML = '';
            
            if (videos.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="empty-state">
                            <h3>No videos found</h3>
                            <p>Click "Add New Video" to add your first video</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            videos.forEach(video => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>${video.title || 'N/A'}</strong></td>
                    <td>${getCategoryDisplay(video.category)}</td>
                    <td>${video.description || 'No description'}</td>
                    <td>${video.duration || 'N/A'}</td>
                    <td>
                        ${video.thumbnail ? 
                            `<img src="${video.thumbnail}" alt="${video.title}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" onerror="this.src='https://via.placeholder.com/50x50/333/fff?text=No+Image'">` : 
                            '<span style="color: #999;">No image</span>'
                        }
                    </td>
                    <td><a href="https://www.youtube.com/watch?v=${video.videoId}" target="_blank" style="color: #667eea; text-decoration: none;">${video.videoId}</a></td>
                    <td>${video.releaseDate || 'N/A'}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-edit btn-sm" onclick="editVideo(${video.id})">Edit</button>
                            <button class="btn btn-delete btn-sm" onclick="deleteVideo(${video.id})">Delete</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // Open modal
        function openModal() {
            console.log('Opening modal');
            document.getElementById('videoModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Add New Video';
            document.getElementById('videoForm').reset();
            editingVideo = null;
            // Hide current thumbnail when adding new video
            document.getElementById('currentThumbnail').style.display = 'none';
        }
        
        // Close modal
        function closeModal() {
            document.getElementById('videoModal').style.display = 'none';
            editingVideo = null;
        }
        
        // Edit video
        function editVideo(id) {
            const video = videos.find(v => v.id === id);
            if (video) {
                editingVideo = video;
                document.getElementById('videoId').value = video.id;
                document.getElementById('videoTitle').value = video.title;
                document.getElementById('videoCategory').value = video.category;
                document.getElementById('videoDescription').value = video.description || '';
                document.getElementById('videoUrl').value = `https://www.youtube.com/watch?v=${video.videoId}`;
                document.getElementById('videoDuration').value = video.duration || '';
                // Clear file input for thumbnail (can't pre-populate file inputs for security)
                document.getElementById('videoThumbnail').value = '';
                
                // Show current thumbnail if exists
                const currentThumbnailDiv = document.getElementById('currentThumbnail');
                const currentThumbnailImg = document.getElementById('currentThumbnailImg');
                if (video.thumbnail) {
                    currentThumbnailDiv.style.display = 'block';
                    currentThumbnailImg.src = video.thumbnail;
                } else {
                    currentThumbnailDiv.style.display = 'none';
                }
                
                document.getElementById('modalTitle').textContent = 'Edit Video';
                document.getElementById('videoModal').style.display = 'block';
            }
        }
        
        // Delete video (from database)
        async function deleteVideo(id) {
            if (confirm('Are you sure you want to delete this video?')) {
                try {
                    const response = await fetch('/madam-portfolio/backend/api/videos.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id })
                    });
                    
                    if (response.ok) {
                        alert('Video deleted successfully!');
                        loadVideos(); // Reload videos from database
                    } else {
                        const error = await response.text();
                        console.error('Error response:', error);
                        console.error('Response status:', response.status);
                        console.error('Response headers:', response.headers);
                        alert('Error deleting video: ' + error);
                    }
                } catch (error) {
                    console.error('Error deleting video:', error);
                    alert('Error deleting video');
                }
            }
        }
        
        // Handle form submission (save to database)
        document.getElementById('videoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            console.log('=== FORM SUBMISSION START ===');
            console.log('Form submitted, editingVideo:', editingVideo);
            
            // Validate required fields
            const title = document.getElementById('videoTitle').value.trim();
            const category = document.getElementById('videoCategory').value;
            const videoUrl = document.getElementById('videoUrl').value.trim();
            
            if (!title) {
                alert('Please enter a video title');
                return;
            }
            
            if (!category) {
                alert('Please select a category');
                return;
            }
            
            if (!videoUrl) {
                alert('Please enter a video URL');
                return;
            }
            
            const formData = new FormData(this);
            const videoData = {
                title: formData.get('title'),
                category: formData.get('category'),
                description: formData.get('description'),
                video_url: formData.get('video_url'),
                duration: formData.get('duration')
            };
            
            // Handle file upload
            const thumbnailFile = document.getElementById('videoThumbnail').files[0];
            if (thumbnailFile) {
                formData.append('thumbnail_file', thumbnailFile);
            }
            
            // Remove thumbnail from videoData since we're handling file separately
            delete videoData.thumbnail;
            
            console.log('Video data to save:', videoData);
            console.log('Thumbnail file:', thumbnailFile);
            console.log('FormData before sending:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}:`, value);
            }
            
            try {
                let response;
                if (editingVideo) {
                    // Update existing video
                    videoData.id = editingVideo.id;
                    formData.append('video_data', JSON.stringify(videoData));
                    formData.append('_method', 'PUT'); // Use POST with method override
                    console.log('Updating video with ID:', editingVideo.id);
                    response = await fetch('/madam-portfolio/backend/api/videos.php', {
                        method: 'POST',
                        body: formData
                    });
                } else {
                    // Add new video
                    formData.append('video_data', JSON.stringify(videoData));
                    console.log('Creating new video');
                    response = await fetch('/madam-portfolio/backend/api/videos.php', {
                        method: 'POST',
                        body: formData
                    });
                }
                
                console.log('API response status:', response.status);
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('API response:', result);
                    alert(editingVideo ? 'Video updated successfully!' : 'Video added successfully!');
                    closeModal();
                    loadVideos(); // Reload videos from database
                } else {
                    const error = await response.text();
                    console.error('API error response:', error);
                    console.error('Response status:', response.status);
                    console.error('Response headers:', response.headers);
                    
                    // Try to parse as JSON, if fails show raw error
                    try {
                        const errorJson = JSON.parse(error);
                        alert('Error saving video: ' + errorJson.message);
                    } catch (e) {
                        alert('Error saving video: ' + error);
                    }
                }
            } catch (error) {
                console.error('Error saving video:', error);
                alert('Error saving video');
            }
        });
        
        // Load videos when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadVideos();
        });
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('videoModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
