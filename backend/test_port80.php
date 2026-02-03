<!DOCTYPE html>
<html>
<head>
    <title>Port 80 Test</title>
</head>
<body>
    <h1>🔍 Port 80 Backend Test</h1>
    
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
        
        async function testBackendPaths() {
            log('🔄 Testing backend paths on port 80...', 'info');
            
            const paths = [
                '/madam-portfolio/backend/api/albums.php',
                '/madam-portfolio/backend/',
                '/madam-portfolio/',
                '/'
            ];
            
            for (const path of paths) {
                const url = `http://localhost:80${path}`;
                log(`📡 Testing: ${url}`, 'info');
                
                try {
                    const response = await fetch(url);
                    log(`✅ ${path}: ${response.status}`, 'success');
                    
                    if (response.ok) {
                        const text = await response.text();
                        if (text.includes('[') || text.includes('{')) {
                            log(`📊 ${path}: Returns JSON data`, 'success');
                        } else {
                            log(`📄 ${path}: Returns HTML (${text.substring(0, 50)}...)`, 'info');
                        }
                    }
                } catch (error) {
                    log(`❌ ${path}: ${error.message}`, 'error');
                }
            }
        }
        
        testBackendPaths();
    </script>
</body>
</html>
