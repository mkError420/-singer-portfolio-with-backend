<!DOCTYPE html>
<html>
<head>
    <title>URL Fix Test</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>🔗 URL Fix Test</h1>
    <button onclick="testCorrectURL()">Test Correct URL (with trailing slash)</button>
    <button onclick="testIncorrectURL()">Test Incorrect URL (without trailing slash)</button>
    <div id="results"></div>
    
    <script>
        const resultsDiv = document.getElementById('results');
        
        function addResult(message, type) {
            const div = document.createElement('div');
            div.style.padding = '10px';
            div.style.margin = '10px 0';
            div.style.borderRadius = '4px';
            div.style.background = type === 'success' ? '#d4edda' : '#f8d7da';
            div.style.color = type === 'success' ? '#155724' : '#721c24';
            div.innerHTML = message;
            resultsDiv.appendChild(div);
        }
        
        async function testCorrectURL() {
            addResult('🔄 Testing correct URL with trailing slash...', 'info');
            
            try {
                const api = axios.create({
                    baseURL: 'http://localhost:80443/madam-portfolio/backend/api/',
                    timeout: 10000,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });
                
                const response = await api.get('/albums.php');
                addResult(`✅ Success! Loaded ${response.data.length} albums`, 'success');
                addResult(`📊 First album: ${response.data[0]?.title || 'None'}`, 'success');
                
            } catch (error) {
                addResult(`❌ Error: ${error.message}`, 'error');
                console.error('Error details:', error);
            }
        }
        
        async function testIncorrectURL() {
            addResult('🔄 Testing incorrect URL without trailing slash...', 'info');
            
            try {
                const api = axios.create({
                    baseURL: 'http://localhost:80443/madam-portfolio/backend/api',
                    timeout: 10000,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });
                
                const response = await api.get('/albums.php');
                addResult(`✅ Unexpected success! Loaded ${response.data.length} albums`, 'success');
                
            } catch (error) {
                addResult(`❌ Expected error: ${error.message}`, 'error');
                addResult(`This demonstrates the URL construction issue`, 'info');
            }
        }
    </script>
</body>
</html>
