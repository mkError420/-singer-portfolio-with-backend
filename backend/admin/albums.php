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
        
        /* Filter Section Styles */
        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .filter-controls {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .filter-group select,
        .filter-group button {
            padding: 1rem;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: rgba(88, 85, 85, 0.9);
            backdrop-filter: blur(10px);
        }
        
        .filter-group select {
            color: #e2e7ecff;
            cursor: pointer;
        }
        
        .filter-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            background: rgba(255, 255, 255, 1);
        }
        
        .filter-group select:hover {
            border-color: #3498db;
            transform: translateY(-1px);
            background: rgba(88, 85, 85, 0.9);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
            color: black;
            border: none;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #7f8c8d 0%, #6c757d 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .filter-info {
            text-align: center;
            padding: 1rem;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 12px;
            color: #2c3e50;
            font-size: 0.9rem;
            font-weight: 600;
            border: 1px solid rgba(102, 126, 234, 0.2);
            backdrop-filter: blur(10px);
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
        
        .track-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
        }
        
        .track-row {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .track-title {
            flex: 2;
        }
        
        .track-youtube {
            flex: 2;
        }
        
        .track-duration {
            flex: 1;
        }
        
        .category-input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .category-input-group select {
            flex: 1;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
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
            background: #636a6bff;
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
            
            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-controls">
                    <div class="filter-group">
                        <label for="filterYear">Filter by Year:</label>
                        <select id="filterYear" onchange="applyFilters()">
                            <option value="">All Years</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filterCategory">Filter by Category:</label>
                        <select id="filterCategory" onchange="applyFilters()">
                            <option value="">All Categories</option>
                            <option value="studio">Studio</option>
                            <option value="live">Live Performance</option>
                            <option value="compilation">Compilation</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button class="btn btn-secondary text-black" onclick="clearFilters()">Clear Filters</button>
                    </div>
                </div>
                
                <div class="filter-info">
                    <span id="filterCount">Showing all albums</span>
                </div>
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
                        <option value="">Select Category</option>
                        <option value="album">Studio Album</option>
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
                
                <!-- Tracks Section -->
                <div class="form-group">
                    <label>Tracks</label>
                    <div id="tracksContainer">
                        <div class="track-item">
                            <div class="track-row">
                                <input type="text" placeholder="Track title" class="track-title">
                                <input type="text" placeholder="YouTube URL" class="track-youtube">
                                <input type="text" placeholder="Duration (e.g., 3:45)" class="track-duration">
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeTrack(this)">Remove</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm" onclick="addTrack()">+ Add Track</button>
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
        let categories = [];
        let filteredAlbums = [];
        
        // Filter states
        let currentYearFilter = '';
        let currentCategoryFilter = '';
        
        // Load albums
        async function loadAlbums() {
            try {
                console.log('Loading albums...');
                const response = await fetch('../api/albums.php');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                albums = await response.json();
                console.log('Albums loaded:', albums);
                populateYearFilter();
                applyFilters();
            } catch (error) {
                console.error('Error loading albums:', error);
            }
        }
        
        // Load categories (not needed for ENUM)
        async function loadCategories() {
            console.log('Categories not needed for ENUM system');
            categories = [];
        }
        
        // Refresh categories (not needed for ENUM)
        async function refreshCategories() {
            console.log('Categories not needed for ENUM system');
        }
        
        // Get category display name
        function getCategoryName(categoryValue) {
            if (!categoryValue) {
                return 'No Category';
            }
            
            // Map ENUM values to display names
            const categoryMap = {
                'album': 'Studio Album',
                'acoustic': 'Acoustic',
                'studio': 'Studio',
                'live': 'Live Performance',
                'compilation': 'Compilation'
            };
            
            return categoryMap[categoryValue] || categoryValue;
        }
        
        // Populate modal category dropdown (not needed for ENUM)
        function populateModalCategorySelect() {
            // Categories are hardcoded in HTML
        }
        
        // Filter functions
        function applyFilters() {
            currentYearFilter = document.getElementById('filterYear').value;
            currentCategoryFilter = document.getElementById('filterCategory').value;
            
            filteredAlbums = albums.filter(album => {
                const albumYear = album.release_year || (album.created_at ? new Date(album.created_at).getFullYear() : null);
                const yearMatch = !currentYearFilter || albumYear.toString() === currentYearFilter;
                const categoryMatch = !currentCategoryFilter || album.category === currentCategoryFilter;
                return yearMatch && categoryMatch;
            });
            
            console.log('Filter applied:', {
                yearFilter: currentYearFilter,
                categoryFilter: currentCategoryFilter,
                totalAlbums: albums.length,
                filteredAlbums: filteredAlbums.length
            });
            
            renderAlbums();
            updateFilterCount();
        }
        
        function refreshCategories() {
            console.log('Manually refreshing categories...');
            loadCategories();
        }
        
        function updateFilterCount() {
            const countElement = document.getElementById('filterCount');
            const totalItems = albums.length;
            const filteredCount = filteredAlbums.length;
            
            if (filteredCount === totalItems) {
                countElement.textContent = `Showing all ${totalItems} albums`;
            } else {
                countElement.textContent = `Showing ${filteredCount} of ${totalItems} albums`;
            }
        }
        
        function clearFilters() {
            console.log('Clearing filters...');
            
            // Clear filter dropdowns
            document.getElementById('filterYear').value = '';
            document.getElementById('filterCategory').value = '';
            
            // Reset filter variables
            currentYearFilter = '';
            currentCategoryFilter = '';
            
            // Reset filtered albums to show all
            filteredAlbums = [...albums];
            
            // Re-render albums and update count
            renderAlbums();
            updateFilterCount();
            
            console.log('Filters cleared. Total albums:', albums.length);
        }
        
        function populateYearFilter() {
            const yearSelect = document.getElementById('filterYear');
            const years = [...new Set(albums.map(album => {
                const year = album.release_year || (album.created_at ? new Date(album.created_at).getFullYear() : null);
                return year ? year.toString() : null;
            }).filter(year => year))].sort();
            
            yearSelect.innerHTML = '<option value="">All Years</option>';
            years.forEach(year => {
                yearSelect.innerHTML += `<option value="${year}">${year}</option>`;
            });
        }
        
        // Get category name by ID
        function getCategoryName(categoryId) {
            console.log('Getting category name for ID:', categoryId);
            
            if (!categoryId) {
                return 'No Category';
            }
            
            const category = categories.find(cat => cat.id == categoryId); // Use == for loose comparison
            
            if (category) {
                return category.name;
            } else {
                return 'Unknown Category';
            }
        }
        
        // Fix albums with category ID 15
        async function fixAlbumsWithCategory15() {
            console.log('Looking for albums with category ID 15...');
            
            const albumsToFix = albums.filter(album => album.category === '15');
            console.log('Albums to fix:', albumsToFix);
            
            if (albumsToFix.length === 0) {
                console.log('No albums with category ID 15 found.');
                return;
            }
            
            // Fix each album by setting category to "Studio" (ID: 1)
            for (const album of albumsToFix) {
                console.log('Fixing album:', album);
                await updateAlbumCategory(album.id, '1'); // Set to Studio category
            }
        }
        
        // Fix albums with undefined categories
        async function fixAlbumsWithUndefinedCategories() {
            console.log('Looking for albums with undefined categories...');
            
            const albumsToFix = albums.filter(album => !album.category || album.category === undefined || album.category === null);
            console.log('Albums to fix:', albumsToFix);
            
            if (albumsToFix.length === 0) {
                console.log('No albums with undefined categories found.');
                return;
            }
            
            // Fix each album by setting category to "Studio" (ID: 1)
            for (const album of albumsToFix) {
                console.log('Fixing album:', album);
                await updateAlbumCategory(album.id, '1'); // Set to Studio category
            }
        }
        
        // Update album category by ID
        async function updateAlbumCategory(albumId, newCategoryId) {
            console.log('Updating album category:', { albumId, newCategoryId });
            console.log('Current albums before update:', albums);
            
            try {
                const response = await fetch(`../api/albums.php`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: albumId,
                        category: newCategoryId
                    })
                });
                
                console.log('Update response status:', response.status);
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('Album category updated successfully:', result);
                    alert('Album category updated successfully!');
                    await loadAlbums(); // Reload albums after update
                } else {
                    const errorText = await response.text();
                    console.error('Error updating album category:', errorText);
                    alert('Error updating album category: ' + errorText);
                }
            } catch (error) {
                console.error('Error updating album category:', error);
                alert('Error updating album category: ' + error);
            }
        }
        
        // Add missing category to database
        async function addMissingCategory() {
            try {
                const response = await fetch('../api/categories.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: '15',
                        name: 'Unknown Category 15',
                        description: 'This category was missing and added automatically',
                        status: 'active'
                    })
                });
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('Missing category added successfully:', result);
                    alert('Missing category added to database successfully!');
                    loadCategories();
                } else {
                    const errorText = await response.text();
                    console.error('Error adding missing category:', errorText);
                    alert('Error adding missing category: ' + errorText);
                }
            } catch (error) {
                console.error('Error adding missing category:', error);
                alert('Error adding missing category: ' + error);
            }
        }
        
        // Render albums table
        function renderAlbums() {
            console.log('Rendering albums:', albums);
            const tbody = document.getElementById('albums-table');
            tbody.innerHTML = '';
            
            if (filteredAlbums.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: #7f8c8d;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🎵</div>
                            <h3>No albums found</h3>
                            <p>Click "Add New Album" to create your first album</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            // Use filteredAlbums instead of albums for rendering
            const albumsToRender = filteredAlbums.length > 0 ? filteredAlbums : albums;
            console.log('Albums to render:', albumsToRender);
            console.log('Current filters:', { year: currentYearFilter, category: currentCategoryFilter });
            
            albumsToRender.forEach((album, index) => {
                console.log(`Rendering album ${index + 1}:`, album);
                console.log(`Album category ID:`, album.category);
                console.log(`Category name:`, getCategoryName(album.category));
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        ${album.cover_image ? `
                            <img src="../${album.cover_image}" 
                                 alt="${album.title}" class="album-cover"
                                 onerror="console.log('Failed to load: ${album.cover_image}'); this.src='https://via.placeholder.com/60x60/2a2a2a/ffffff?text=' + encodeURIComponent('${album.title}')">
                        ` : `<img src="https://via.placeholder.com/60x60" alt="${album.title}" class="album-cover">`}
                    </td>
                    <td><strong>${album.title || 'N/A'}</strong></td>
                    <td>${album.year || 'N/A'}</td>
                    <td>
                        <span class="category-badge" style="background: #e9ecef; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                            ${getCategoryName(album.category)}
                        </span>
                    </td>
                    <td>${album.track_count || 0}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-edit btn-sm" onclick="editAlbum(${album.id})">Edit</button>
                            <button class="btn btn-delete btn-sm" onclick="deleteAlbum(${album.id})">Delete</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
                
                console.log(`Album ${index + 1} category:`, album.category);
                console.log(`Category name:`, getCategoryName(album.category));
            });
            
            console.log('Albums rendered successfully');
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
        async function editAlbum(id) {
            const album = albums.find(a => a.id === id);
            if (album) {
                editingAlbum = album;
                document.getElementById('albumId').value = album.id;
                document.getElementById('title').value = album.title;
                document.getElementById('year').value = album.year;
                document.getElementById('description').value = album.description || '';
                
                // Set the category value
                document.getElementById('category').value = album.category;
                
                console.log('Edit album category set to:', album.category);
                console.log('Category dropdown value after setting:', document.getElementById('category').value);
                
                if (album.cover_image) {
                    document.getElementById('imagePreview').src = '../' + album.cover_image;
                    document.getElementById('imagePreview').style.display = 'block';
                    document.getElementById('coverImagePath').value = album.cover_image;
                }
                
                // Load existing tracks
                try {
                    const tracksResponse = await fetch(`../api/albums.php?album_id=${id}&include_tracks=1`);
                    const albumWithTracks = await tracksResponse.json();
                    loadTracksToForm(albumWithTracks.tracks || []);
                } catch (error) {
                    console.error('Error loading tracks:', error);
                    loadTracksToForm([]); // Load empty tracks if error
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
        
        // Track management functions
        function addTrack() {
            const container = document.getElementById('tracksContainer');
            const trackItem = document.createElement('div');
            trackItem.className = 'track-item';
            trackItem.innerHTML = `
                <div class="track-row">
                    <input type="text" placeholder="Track title" class="track-title">
                    <input type="text" placeholder="YouTube URL" class="track-youtube">
                    <input type="text" placeholder="Duration (e.g., 3:45)" class="track-duration">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeTrack(this)">Remove</button>
                </div>
            `;
            container.appendChild(trackItem);
        }
        
        function removeTrack(button) {
            const trackItem = button.closest('.track-item');
            trackItem.remove();
        }
        
        function getTracksData() {
            const tracks = [];
            const trackItems = document.querySelectorAll('.track-item');
            
            trackItems.forEach((item, index) => {
                const title = item.querySelector('.track-title').value.trim();
                const youtube = item.querySelector('.track-youtube').value.trim();
                const duration = item.querySelector('.track-duration').value.trim();
                
                if (title) {
                    tracks.push({
                        title: title,
                        youtube_url: youtube,
                        duration: duration,
                        track_number: index + 1
                    });
                }
            });
            
            return tracks;
        }
        
        function loadTracksToForm(tracks) {
            const container = document.getElementById('tracksContainer');
            container.innerHTML = '';
            
            if (tracks && tracks.length > 0) {
                tracks.forEach(track => {
                    const trackItem = document.createElement('div');
                    trackItem.className = 'track-item';
                    trackItem.innerHTML = `
                        <div class="track-row">
                            <input type="text" placeholder="Track title" class="track-title" value="${track.title || ''}">
                            <input type="text" placeholder="YouTube URL" class="track-youtube" value="${track.youtube_url || ''}">
                            <input type="text" placeholder="Duration (e.g., 3:45)" class="track-duration" value="${track.duration || ''}">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeTrack(this)">Remove</button>
                        </div>
                    `;
                    container.appendChild(trackItem);
                });
            } else {
                // Add one empty track
                addTrack();
            }
        }
        
        // Handle form submission
        document.getElementById('albumForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const categoryValue = document.getElementById('category').value;
            console.log('Selected category value:', categoryValue);
            console.log('Category element:', document.getElementById('category'));
            console.log('Category options count:', document.getElementById('category').options.length);
            console.log('All category options:', Array.from(document.getElementById('category').options).map(opt => ({ value: opt.value, text: opt.text })));
            
            const formData = new FormData();
            const albumData = {
                title: document.getElementById('title').value,
                year: document.getElementById('year').value,
                category: categoryValue,
                description: document.getElementById('description').value,
                cover_image: document.getElementById('coverImagePath').value,
                tracks: getTracksData()
            };
            
            console.log('Album data being sent:', albumData);
            console.log('Editing album:', editingAlbum);
            
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
                
                console.log('Sending album data:', { method, url, albumData });
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(albumData)
                });
                
                console.log('API response status:', response.status);
                console.log('API response headers:', response.headers);
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('Album saved successfully:', result);
                    console.log('API response details:', {
                        success: result.success,
                        message: result.message,
                        albumId: result.id,
                        updatedFields: result.updated_fields,
                        affectedRows: result.affected_rows
                    });
                    
                    // Load albums after successful save to ensure data is updated
                    loadAlbums();
                    
                    // Show success message after data is confirmed loaded
                    setTimeout(() => {
                        alert(editingAlbum ? 'Album updated successfully!' : 'Album added successfully!');
                    }, 500);
                    
                    closeModal();
                } else {
                    const errorText = await response.text();
                    console.error('Error saving album:', errorText);
                    console.error('Full error response:', errorText);
                    alert('Error saving album: ' + errorText);
                }
            } catch (error) {
                console.error('Error saving album:', error);
                alert('Error saving album');
            }
        });
        
        // Load albums and categories when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadAlbums();
            loadCategories();
        });
    </script>
</body>
</html>
