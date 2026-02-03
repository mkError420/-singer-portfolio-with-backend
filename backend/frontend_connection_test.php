<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frontend Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success { border-left: 4px solid #28a745; }
        .error { border-left: 4px solid #dc3545; }
        .info { border-left: 4px solid #17a2b8; }
        .warning { border-left: 4px solid #ffc107; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .test-result.success { background: #d4edda; color: #155724; }
        .test-result.error { background: #f8d7da; color: #721c24; }
        .test-result.info { background: #d1ecf1; color: #0c5460; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
        .album-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .album-header {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }
        .album-cover {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
        }
        .album-info h4 { margin: 0 0 5px 0; }
        .album-info p { margin: 2px 0; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <h1>🔗 Frontend Connection Test</h1>
    
    <div class="test-section info">
        <h2>📡 API Configuration</h2>
        <p><strong>React App URL:</strong> http://localhost:3000</p>
        <p><strong>Backend API URL:</strong> http://localhost:80443/madam-portfolio/backend/api</p>
        <p><strong>Expected Flow:</strong> React → Backend API → Database → React</p>
    </div>

    <div class="test-section">
        <h2>🧪 Connection Tests</h2>
        <button onclick="testBasicConnection()">Test Basic Connection</button>
        <button onclick="testAlbumsAPI()">Test Albums API</button>
        <button onclick="testCORS()">Test CORS</button>
        <button onclick="testReactLikeRequest()">Test React-like Request</button>
        <button onclick="testTracksLoading()">Test Tracks Loading</button>
        
        <div id="test-results"></div>
    </div>

    <div class="test-section">
        <h2>🎵 Albums Display</h2>
        <div id="albums-display">
            <p style="color: #666;">Click "Test Albums API" to load albums...</p>
        </div>
    </div>

    <div class="test-section info">
        <h2>🔧 React Debugging Steps</h2>
        <ol>
            <li><strong>Open React App:</strong> http://localhost:3000</li>
            <li><strong>Open Browser Console:</strong> Press F12</li>
            <li><strong>Look for these logs:</strong>
                <ul>
                    <li>"API Request: GET /albums"</li>
                    <li>"Albums loaded successfully" or error messages</li>
                    <li>Any Network errors in console</li>
                </ul>
            </li>
            <li><strong>Check Network Tab:</strong>
                <ul>
                    <li>Look for requests to albums API</li>
                    <li>Check response status (should be 200)</li>
                    <li>Check response headers for CORS</li>
                    <li>Check response body (should be JSON)</li>
                </ul>
            </li>
        </ol>
    </div>

    <div class="test-section warning">
        <h2>⚠️ Common Issues</h2>
        <div class="test-result">
            <strong>If albums don't show in React:</strong>
            <ul>
                <li>Check browser console for errors</li>
                <li>Verify React app is making requests to correct URL (port 80443)</li>
                <li>Check if React state is being updated</li>
                <li>Check if loading state is stuck</li>
            </ul>
        </div>
        <div class="test-result">
            <strong>If CORS errors:</strong>
            <ul>
                <li>Backend CORS headers should be present</li>
                <li>Check if preflight OPTIONS requests work</li>
                <li>Verify no other CORS middleware blocking</li>
            </ul>
        </div>
        <div class="test-result">
            <strong>If Network errors:</strong>
            <ul>
                <li>Verify Apache is running on port 80443</li>
                <li>Check if API URLs are accessible directly</li>
                <li>Verify no firewall blocking</li>
            </ul>
        </div>
    </div>

    <script>
        const API_BASE = 'http://localhost:80443/madam-portfolio/backend/api';
        const resultsDiv = document.getElementById('test-results');
        const albumsDiv = document.getElementById('albums-display');
        
        function addResult(message, type = 'info') {
            const div = document.createElement('div');
            div.className = `test-result ${type}`;
            div.innerHTML = message;
            resultsDiv.appendChild(div);
        }
        
        function clearResults() {
            resultsDiv.innerHTML = '';
        }
        
        async function testBasicConnection() {
            clearResults();
            addResult('🔄 Testing basic connection to backend...', 'info');
            
            try {
                const response = await fetch(API_BASE + '/cors_test.php');
                const data = await response.json();
                
                addResult(`✅ Basic connection successful!`, 'success');
                addResult(`Response: ${JSON.stringify(data)}`, 'info');
                
            } catch (error) {
                addResult(`❌ Basic connection failed: ${error.message}`, 'error');
                addResult(`Check if Apache is running on port 80443`, 'warning');
            }
        }
        
        async function testAlbumsAPI() {
            clearResults();
            addResult('🔄 Testing Albums API...', 'info');
            
            try {
                const response = await fetch(API_BASE + '/albums.php');
                addResult(`📡 Response status: ${response.status}`, 'info');
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const albums = await response.json();
                addResult(`✅ Albums API successful!`, 'success');
                addResult(`📊 Found ${albums.length} albums`, 'info');
                
                if (albums.length > 0) {
                    addResult(`📋 First album: ${albums[0].title}`, 'info');
                    displayAlbums(albums);
                } else {
                    addResult(`⚠️ No albums found in database`, 'warning');
                }
                
            } catch (error) {
                addResult(`❌ Albums API failed: ${error.message}`, 'error');
                addResult(`Check API URL and CORS headers`, 'warning');
            }
        }
        
        async function testCORS() {
            clearResults();
            addResult('🔄 Testing CORS preflight...', 'info');
            
            try {
                const response = await fetch(API_BASE + '/cors_test.php', {
                    method: 'OPTIONS',
                    headers: {
                        'Content-Type': 'application/json',
                        'Access-Control-Request-Method': 'GET',
                        'Access-Control-Request-Headers': 'Content-Type'
                    }
                });
                
                addResult(`✅ CORS preflight successful! Status: ${response.status}`, 'success');
                
                // Check CORS headers
                const corsHeaders = {
                    'Access-Control-Allow-Origin': response.headers.get('Access-Control-Allow-Origin'),
                    'Access-Control-Allow-Methods': response.headers.get('Access-Control-Allow-Methods'),
                    'Access-Control-Allow-Headers': response.headers.get('Access-Control-Allow-Headers')
                };
                
                addResult(`🔒 CORS Headers: ${JSON.stringify(corsHeaders, null, 2)}`, 'info');
                
            } catch (error) {
                addResult(`❌ CORS preflight failed: ${error.message}`, 'error');
            }
        }
        
        async function testReactLikeRequest() {
            clearResults();
            addResult('🔄 Testing React-like request with axios-style headers...', 'info');
            
            try {
                const response = await fetch(API_BASE + '/albums.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                const albums = await response.json();
                addResult(`✅ React-like request successful!`, 'success');
                addResult(`📊 Received ${albums.length} albums`, 'info');
                
                // Simulate React component behavior
                console.log('React-like albums data:', albums);
                addResult(`📝 Check browser console for album data`, 'info');
                
            } catch (error) {
                addResult(`❌ React-like request failed: ${error.message}`, 'error');
            }
        }
        
        async function testTracksLoading() {
            clearResults();
            addResult('🔄 Testing tracks loading (like React Music component)...', 'info');
            
            try {
                // First get albums
                const albumsResponse = await fetch(API_BASE + '/albums.php');
                const albums = await albumsResponse.json();
                
                addResult(`📊 Found ${albums.length} albums`, 'info');
                
                if (albums.length > 0) {
                    const firstAlbum = albums[0];
                    addResult(`🎵 Loading tracks for: ${firstAlbum.title}`, 'info');
                    
                    // Then load tracks for first album
                    const tracksResponse = await fetch(`${API_BASE}/albums.php?album_id=${firstAlbum.id}&include_tracks=1`);
                    const albumWithTracks = await tracksResponse.json();
                    
                    addResult(`✅ Tracks loaded successfully!`, 'success');
                    addResult(`🎶 Found ${albumWithTracks.tracks?.length || 0} tracks`, 'info');
                    
                    if (albumWithTracks.tracks && albumWithTracks.tracks.length > 0) {
                        addResult(`🎼 First track: ${albumWithTracks.tracks[0].title}`, 'info');
                    }
                }
                
            } catch (error) {
                addResult(`❌ Tracks loading failed: ${error.message}`, 'error');
            }
        }
        
        function displayAlbums(albums) {
            albumsDiv.innerHTML = albums.map(album => `
                <div class="album-card">
                    <div class="album-header">
                        <img src="http://localhost:80443/madam-portfolio/backend/${album.cover_image}" 
                             alt="${album.title}" 
                             class="album-cover"
                             onerror="this.src='https://via.placeholder.com/80x80/2a2a2a/ffffff?text=${encodeURIComponent(album.title)}'">
                        <div class="album-info">
                            <h4>${album.title}</h4>
                            <p><strong>Year:</strong> ${album.year}</p>
                            <p><strong>Category:</strong> ${album.category || 'No Category'}</p>
                            <p><strong>Tracks:</strong> ${album.track_count || 0}</p>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Auto-run basic test on page load
        window.onload = function() {
            addResult('🚀 Frontend connection test loaded', 'info');
            addResult('📡 API Base URL: ' + API_BASE, 'info');
            addResult('🔧 Click test buttons above to diagnose issues', 'info');
        };
    </script>
</body>
</html>
