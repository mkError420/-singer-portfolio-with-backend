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
    <title>About Content - Admin Dashboard</title>
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
        
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-top: 1rem;
        }
        
        .content-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
        }
        
        .content-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .content-preview {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 1rem;
            max-height: 100px;
            overflow: hidden;
            position: relative;
        }
        
        .content-preview:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: linear-gradient(transparent, rgba(255, 255, 255, 0.9));
        }
        
        .content-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
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
                <li><a href="videos.php">Videos</a></li>
                <li><a href="tour.php">Tour Dates</a></li>
                <li><a href="about.php" class="active">About Content</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="auth.php?action=logout">Logout</a></li>
            </ul>
        </aside>
        
        <main class="main-content">
            <div class="header">
                <h1>About Content</h1>
                <button class="btn btn-primary" onclick="openModal()">Add New Section</button>
            </div>
            
            <div class="content-section">
                <div class="content-grid" id="content-grid">
                    <!-- Content sections will be loaded here -->
                </div>
            </div>
        </main>
    </div>
    
    <!-- Content Modal -->
    <div id="contentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Section</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="contentForm">
                <input type="hidden" id="contentId">
                
                <div class="form-group">
                    <label for="sectionName">Section Name</label>
                    <input type="text" id="sectionName" name="section_name" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="10" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="imageUrl">Image URL (optional)</label>
                    <input type="url" id="imageUrl" name="image">
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Section</button>
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
            max-width: 800px;
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 200px;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }
    </style>
    
    <script>
        let aboutContent = [];
        let editingContent = null;
        
        // Load about content
        async function loadAboutContent() {
            try {
                const response = await fetch('../api/about.php');
                if (response.ok) {
                    aboutContent = await response.json();
                    renderContent();
                }
            } catch (error) {
                console.error('Error loading about content:', error);
            }
        }
        
        // Render content sections
        function renderContent() {
            const grid = document.getElementById('content-grid');
            grid.innerHTML = '';
            
            if (aboutContent.length === 0) {
                grid.innerHTML = `
                    <div class="empty-state">
                        <h3>No content sections found</h3>
                        <p>Click "Add New Section" to create your first content section</p>
                    </div>
                `;
                return;
            }
            
            aboutContent.forEach(content => {
                const card = document.createElement('div');
                card.className = 'content-card';
                card.innerHTML = `
                    <h3>${content.section_name || 'Untitled Section'}</h3>
                    <div class="content-preview">
                        ${content.content ? content.content.substring(0, 200) + '...' : 'No content'}
                    </div>
                    <div class="content-actions">
                        <button class="btn btn-edit btn-sm" onclick="editContent(${content.id})">Edit</button>
                    </div>
                `;
                grid.appendChild(card);
            });
        }
        
        // Open modal
        function openModal() {
            document.getElementById('contentModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Add New Section';
            document.getElementById('contentForm').reset();
            editingContent = null;
        }
        
        // Close modal
        function closeModal() {
            document.getElementById('contentModal').style.display = 'none';
            editingContent = null;
        }
        
        // Edit content
        function editContent(id) {
            const content = aboutContent.find(c => c.id === id);
            if (content) {
                editingContent = content;
                document.getElementById('contentId').value = content.id;
                document.getElementById('sectionName').value = content.section_name;
                document.getElementById('content').value = content.content;
                document.getElementById('imageUrl').value = content.image || '';
                document.getElementById('status').value = content.status || 'active';
                
                document.getElementById('modalTitle').textContent = 'Edit Section';
                document.getElementById('contentModal').style.display = 'block';
            }
        }
        
        // Handle form submission
        document.getElementById('contentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const contentData = {
                section_name: formData.get('section_name'),
                content: formData.get('content'),
                image: formData.get('image'),
                status: formData.get('status')
            };
            
            if (editingContent) {
                contentData.id = editingContent.id;
            }
            
            try {
                const method = editingContent ? 'PUT' : 'POST';
                const response = await fetch('../api/about.php', {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(contentData)
                });
                
                if (response.ok) {
                    await loadAboutContent();
                    alert(editingContent ? 'Section updated successfully!' : 'Section added successfully!');
                    closeModal();
                } else {
                    alert('Error saving section');
                }
            } catch (error) {
                console.error('Error saving section:', error);
                alert('Error saving section');
            }
        });
        
        // Load content when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadAboutContent();
        });
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('contentModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
