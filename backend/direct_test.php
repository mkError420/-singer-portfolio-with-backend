<!DOCTYPE html>
<html>
<head>
    <title>Direct Backend Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
        .album { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px; }
        .album-header { display: flex; gap: 15px; margin-bottom: 10px; }
        .album-cover { width: 80px; height: 80px; border-radius: 8px; object-fit: cover; }
    </style>
</head>
<body>
    <h1>🔍 Direct Backend Test</h1>
    
    <div class="test info">
        <h3>📡 Current Configuration</h3>
        <p><strong>Backend URL:</strong> http://localhost:80443/madam-portfolio/backend/api/albums.php</p>
        <p><strong>React App URL:</strong> http://localhost:3000</p>
        <p><strong>Issue:</strong> Albums not showing in React frontend</p>
    </div>

    <div class="test">
        <h3>🧪 Quick Tests</h3>
        <button onclick="testDirectFetch()">Test Direct Fetch</button>
        <button onclick="testAxiosLike()">Test Axios-like Request</button>
        <button onclick="testReactWay()">Test React Component Way</button>
        <button onclick="showAlbums()">Display Albums</button>
        
        <div id="test-results"></div>
    </div>

    <div class="test">
        <h3>🎵 Albums from Backend</h3>
        <div id="albums-display">
            <p style="color: #666;">Click "Display Albums" to load albums...</p>
        </div>
    </div>

    <script>
        const API_URL = 'http://localhost:80443/madam-portfolio/backend/api/albums.php';
        const resultsDiv = document.getElementById('test-results');
        const albumsDiv = document.getElementById('albums-display');
        
        function log(message, type = 'info') {
            const div = document.createElement('div');
            div.style.padding = '10px';
            div.style.margin = '5px 0';
            div.style.borderRadius = '4px';
            div.style.background = type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1';
            div.style.color = type === 'success' ? '#155724' : type === 'error' ? '#721c24' : '#0c5460';
            div.innerHTML = message;
            resultsDiv.appendChild(div);
        }
        
        async function testDirectFetch() {
            log('🔄 Testing direct fetch...', 'info');
            
            try {
                const response = await fetch(API_URL);
                log(`📡 Response status: ${response.status}`, 'info');
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const albums = await response.json();
                log(`✅ Success! Found ${albums.length} albums`, 'success');
                log(`📋 First album: ${albums[0]?.title || 'None'}`, 'info');
                
                if (albums.length > 0) {
                    log('🎯 Backend is working correctly!', 'success');
                    log('❌ Issue is in React frontend', 'error');
                }
                
            } catch (error) {
                log(`❌ Error: ${error.message}`, 'error');
                log('🔧 Backend connection issue', 'error');
            }
        }
        
        async function testAxiosLike() {
            log('🔄 Testing Axios-like request...', 'info');
            
            try {
                // Simulate how axios makes requests
                const response = await fetch(API_URL, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                const albums = await response.json();
                log(`✅ Axios-like request successful!`, 'success');
                log(`📊 Found ${albums.length} albums`, 'info');
                
            } catch (error) {
                log(`❌ Axios-like request failed: ${error.message}`, 'error');
            }
        }
        
        async function testReactWay() {
            log('🔄 Testing React component way...', 'info');
            
            try {
                // Simulate exactly how React Music component loads data
                const albumsResponse = await fetch(API_URL);
                const albumsData = await albumsResponse.json();
                
                log(`📊 Loaded ${albumsData.length} albums`, 'info');
                
                // Load tracks for each album (like React does)
                const albumsWithTracks = await Promise.all(
                    albumsData.map(async (album) => {
                        try {
                            const tracksResponse = await fetch(`http://localhost:80443/madam-portfolio/backend/api/albums.php?album_id=${album.id}&include_tracks=1`);
                            const albumWithTracks = await tracksResponse.json();
                            return albumWithTracks;
                        } catch (error) {
                            log(`⚠️ Error loading tracks for ${album.title}: ${error.message}`, 'error');
                            return { ...album, tracks: [] };
                        }
                    })
                );
                
                log(`✅ React-way loading successful!`, 'success');
                log(`🎵 Loaded ${albumsWithTracks.length} albums with tracks`, 'info');
                
                // Store for display
                window.testAlbums = albumsWithTracks;
                
            } catch (error) {
                log(`❌ React-way loading failed: ${error.message}`, 'error');
            }
        }
        
        function showAlbums() {
            if (window.testAlbums) {
                displayAlbums(window.testAlbums);
            } else {
                loadAndDisplayAlbums();
            }
        }
        
        async function loadAndDisplayAlbums() {
            log('🔄 Loading and displaying albums...', 'info');
            
            try {
                const response = await fetch(API_URL);
                const albums = await response.json();
                
                displayAlbums(albums);
                log(`✅ Displayed ${albums.length} albums`, 'success');
                
            } catch (error) {
                log(`❌ Failed to load albums: ${error.message}`, 'error');
            }
        }
        
        function displayAlbums(albums) {
            if (albums.length === 0) {
                albumsDiv.innerHTML = '<p style="color: #666;">No albums found</p>';
                return;
            }
            
            albumsDiv.innerHTML = albums.map(album => `
                <div class="album">
                    <div class="album-header">
                        <img src="http://localhost:80443/madam-portfolio/backend/${album.cover_image}" 
                             alt="${album.title}" 
                             class="album-cover"
                             onerror="this.src='https://via.placeholder.com/80x80/2a2a2a/ffffff?text=${encodeURIComponent(album.title)}'">
                        <div>
                            <h4>${album.title}</h4>
                            <p><strong>Year:</strong> ${album.year}</p>
                            <p><strong>Category:</strong> ${album.category || 'No Category'}</p>
                            <p><strong>Tracks:</strong> ${album.track_count || 0}</p>
                            <p><strong>ID:</strong> ${album.id}</p>
                        </div>
                    </div>
                    ${album.description ? `<p><strong>Description:</strong> ${album.description}</p>` : ''}
                </div>
            `).join('');
        }
        
        // Auto-test on load
        window.onload = function() {
            log('🚀 Direct test page loaded', 'info');
            log('📡 Backend URL: ' + API_URL, 'info');
            log('🔧 Click test buttons to diagnose', 'info');
        };
    </script>
</body>
</html>
