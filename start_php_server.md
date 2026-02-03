# PHP Built-in Server Alternative

If XAMPP Apache isn't working, use PHP's built-in server:

## Step 1: Open Command Prompt
1. Press Win + R
2. Type `cmd` and press Enter

## Step 2: Navigate to Project Directory
```bash
cd C:\xampp\htdocs\madam-portfolio\backend
```

## Step 3: Start PHP Server
```bash
php -S localhost:8000
```

## Step 4: Access Your Application
- Server Status: http://localhost:8000/server_status.php
- Albums API: http://localhost:8000/api/albums.php
- CORS Test: http://localhost:8000/api/cors_test.php
- Frontend Test: http://localhost:8000/react_test.html

## Step 5: Update React App
In your React app, update the API base URL in `src/services/api.js`:

```javascript
const api = axios.create({
  baseURL: 'http://localhost:8000/api',  // Change from localhost/madam-portfolio/backend/api
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
});
```

## Step 6: Restart React App
```bash
npm start
```

## Advantages of PHP Built-in Server
- ✅ No Apache configuration needed
- ✅ Works immediately
- ✅ Perfect for development
- ✅ Supports all PHP features
- ✅ CORS headers work correctly

## Disadvantages
- ❌ Only for development (not production)
- ❌ Single thread (slower than Apache)
- ❌ No .htaccess support

This is the fastest way to get your backend working!
