<!DOCTYPE html>
<html>
<head>
    <title>Category JavaScript Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; }
        select { padding: 10px; width: 300px; }
        button { padding: 10px 20px; margin: 10px; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Category JavaScript Test</h1>
    
    <div class="test-section">
        <h2>Category Dropdown Test</h2>
        <div class="category-input-group">
            <select id="category" name="category" required>
                <option value="">Select Category</option>
            </select>
            <button type="button" onclick="testAddCategory()">+ Add New</button>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Debug Console</h2>
        <pre id="debug">Debug output will appear here...</pre>
    </div>
    
    <div class="test-section">
        <h2>Manual Tests</h2>
        <button onclick="testLoadCategories()">Test Load Categories</button>
        <button onclick="testPopulateSelect()">Test Populate Select</button>
        <button onclick="showDebugInfo()">Show Debug Info</button>
    </div>

    <script>
        let categories = [];
        const debugElement = document.getElementById('debug');
        
        function log(message, data = null) {
            const timestamp = new Date().toLocaleTimeString();
            let output = `[${timestamp}] ${message}`;
            if (data) {
                output += '\n' + JSON.stringify(data, null, 2);
            }
            debugElement.textContent += output + '\n\n';
            debugElement.scrollTop = debugElement.scrollHeight;
            console.log(message, data);
        }
        
        // Load categories
        async function testLoadCategories() {
            try {
                log('Loading categories...');
                const response = await fetch('api/categories.php');
                
                log('Response status:', response.status);
                log('Response headers:', Object.fromEntries(response.headers.entries()));
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const responseText = await response.text();
                log('Raw response text:', responseText);
                
                categories = JSON.parse(responseText);
                log('Categories loaded:', categories);
                log('Categories count:', categories.length);
                
                await testPopulateSelect();
                
            } catch (error) {
                log('Error loading categories:', error.message);
            }
        }
        
        // Populate category select dropdown
        function testPopulateSelect() {
            log('Populating category select...');
            const select = document.getElementById('category');
            
            if (!select) {
                log('❌ Category select element not found!');
                return;
            }
            
            log('✅ Category select element found');
            log('Current options before:', select.innerHTML);
            
            select.innerHTML = '<option value="">Select Category</option>';
            
            if (!categories || categories.length === 0) {
                log('❌ No categories to populate');
                return;
            }
            
            categories.forEach((category, index) => {
                log(`Adding category ${index + 1}:`, category);
                
                const option = document.createElement('option');
                option.value = category.name;
                option.textContent = category.name;
                select.appendChild(option);
            });
            
            log('✅ Category select populated');
            log('Final options count:', select.options.length);
            log('Final select HTML:', select.innerHTML);
        }
        
        function showDebugInfo() {
            log('=== DEBUG INFO ===');
            log('Categories variable:', categories);
            log('Categories length:', categories ? categories.length : 'undefined');
            
            const select = document.getElementById('category');
            log('Select element:', select);
            log('Select options count:', select ? select.options.length : 'undefined');
            log('Select HTML:', select ? select.innerHTML : 'undefined');
            
            if (categories && categories.length > 0) {
                log('First category:', categories[0]);
                log('Category has name field:', categories[0].hasOwnProperty('name'));
            }
        }
        
        function testAddCategory() {
            log('Test add category clicked');
            alert('Add category functionality would open modal here');
        }
        
        // Auto-load on page load
        window.addEventListener('DOMContentLoaded', function() {
            log('Page loaded, starting category test...');
            setTimeout(testLoadCategories, 1000);
        });
    </script>
</body>
</html>
