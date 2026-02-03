<!DOCTYPE html>
<html>
<head>
    <title>Test Fixed API</title>
</head>
<body>
    <h1>🔍 Test Fixed API</h1>
    
    <div id="results"></div>
    
    <script>
        const resultsDiv = document.getElementById('results');
        
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
        
        async function testAPIs() {
            log('🔄 Testing different API endpoints...', 'info');
            
            const apis = [
                { name: 'Original API', url: '/madam-portfolio/backend/api/albums.php' },
                { name: 'Fixed API', url: '/madam-portfolio/backend/api/albums_fixed.php' },
                { name: 'Direct Fixed API', url: 'http://localhost/madam-portfolio/backend/api/albums_fixed.php' }
            ];
            
            for (const api of apis) {
                log(`📡 Testing ${api.name}: ${api.url}`, 'info');
                
                try {
                    const response = await fetch(api.url);
                    log(`📡 ${api.name} Response: ${response.status}`, response.ok ? 'success' : 'error');
                    
                    if (response.ok) {
                        const data = await response.json();
                        log(`✅ ${api.name}: Found ${data.length} albums`, 'success');
                        
                        // Check CORS headers
                        const corsOrigin = response.headers.get('Access-Control-Allow-Origin');
                        const corsMethods = response.headers.get('Access-Control-Allow-Methods');
                        log(`🔒 CORS Headers: Origin=${corsOrigin}, Methods=${corsMethods}`, 'info');
                        
                    } else {
                        const text = await response.text();
                        log(`❌ ${api.name} Error: ${text.substring(0, 100)}...`, 'error');
                    }
                } catch (error) {
                    log(`❌ ${api.name} Error: ${error.message}`, 'error');
                }
                
                log('', 'info'); // Empty line for readability
            }
        }
        
        testAPIs();
    </script>
</body>
</html>
