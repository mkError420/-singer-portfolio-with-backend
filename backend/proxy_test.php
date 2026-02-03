<!DOCTYPE html>
<html>
<head>
    <title>Proxy Test</title>
</head>
<body>
    <h1>🔍 Proxy Connection Test</h1>
    
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
        
        async function testDirectBackend() {
            log('🔄 Testing direct backend connection...', 'info');
            
            try {
                const response = await fetch('http://localhost:80443/madam-portfolio/backend/api/albums.php');
                log(`📡 Direct backend response: ${response.status}`, 'info');
                
                if (response.ok) {
                    const albums = await response.json();
                    log(`✅ Direct backend works! Found ${albums.length} albums`, 'success');
                    return true;
                } else {
                    log(`❌ Direct backend failed: ${response.status}`, 'error');
                    return false;
                }
            } catch (error) {
                log(`❌ Direct backend error: ${error.message}`, 'error');
                return false;
            }
        }
        
        async function testProxy() {
            log('🔄 Testing proxy connection...', 'info');
            
            try {
                const response = await fetch('/api/albums.php');
                log(`📡 Proxy response: ${response.status}`, 'info');
                
                if (response.ok) {
                    const albums = await response.json();
                    log(`✅ Proxy works! Found ${albums.length} albums`, 'success');
                    return true;
                } else {
                    log(`❌ Proxy failed: ${response.status}`, 'error');
                    return false;
                }
            } catch (error) {
                log(`❌ Proxy error: ${error.message}`, 'error');
                return false;
            }
        }
        
        async function runTests() {
            log('🚀 Starting connection tests...', 'info');
            
            const backendWorks = await testDirectBackend();
            const proxyWorks = await testProxy();
            
            log('📊 Test Results:', 'info');
            log(`Direct Backend: ${backendWorks ? '✅ Working' : '❌ Failed'}`, backendWorks ? 'success' : 'error');
            log(`Proxy: ${proxyWorks ? '✅ Working' : '❌ Failed'}`, proxyWorks ? 'success' : 'error');
            
            if (backendWorks && !proxyWorks) {
                log('🔧 Diagnosis: Backend works but proxy is misconfigured', 'error');
                log('💡 Solution: Check proxy setup in setupProxy.js', 'info');
            } else if (!backendWorks && !proxyWorks) {
                log('🔧 Diagnosis: Backend server is not accessible', 'error');
                log('💡 Solution: Check if Apache is running on port 80443', 'info');
            } else if (backendWorks && proxyWorks) {
                log('🎉 Everything is working!', 'success');
            }
        }
        
        runTests();
    </script>
</body>
</html>
