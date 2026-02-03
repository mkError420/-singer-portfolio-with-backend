<!DOCTYPE html>
<html>
<head>
    <title>Port Test</title>
</head>
<body>
    <h1>🔍 Port Connection Test</h1>
    
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
        
        async function testPort(port, path = '') {
            const url = `http://localhost:${port}${path}`;
            log(`🔄 Testing port ${port} at ${url}...`, 'info');
            
            try {
                const response = await fetch(url, {
                    method: 'GET',
                    mode: 'no-cors'
                });
                
                log(`✅ Port ${port}: Server responding`, 'success');
                return true;
            } catch (error) {
                log(`❌ Port ${port}: ${error.message}`, 'error');
                return false;
            }
        }
        
        async function testPorts() {
            log('🚀 Testing different ports...', 'info');
            
            const ports = [80, 8080, 8000, 3000, 80443, 8443];
            const results = [];
            
            for (const port of ports) {
                const works = await testPort(port);
                results.push({ port, works });
            }
            
            log('📊 Port Test Results:', 'info');
            results.forEach(({ port, works }) => {
                log(`Port ${port}: ${works ? '✅ Open' : '❌ Closed'}`, works ? 'success' : 'error');
            });
            
            const workingPorts = results.filter(r => r.works);
            if (workingPorts.length > 0) {
                log(`🎯 Working ports: ${workingPorts.map(r => r.port).join(', ')}`, 'success');
                log(`💡 Try accessing your backend on one of these ports`, 'info');
            } else {
                log('❌ No ports are responding - Apache may not be running', 'error');
                log('💡 Check XAMPP Control Panel', 'info');
            }
        }
        
        testPorts();
    </script>
</body>
</html>
