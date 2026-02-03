# Frontend Setup Instructions

## The Problem
You're getting "Not Found" error because React applications need to be served by a development server, not accessed directly as HTML files.

## Solution: Start the React Development Server

### Step 1: Open Command Prompt/Terminal
1. Press `Win + R`
2. Type `cmd` and press Enter
3. Navigate to your project directory:
```bash
cd C:\xampp\htdocs\madam-portfolio
```

### Step 2: Install Dependencies (if not already done)
```bash
npm install
```

### Step 3: Start the Development Server
```bash
npm start
```

This will start the React development server and automatically open your browser to:
```
http://localhost:3000
```

## Alternative: Use Live Server (if you don't have Node.js)

If you don't have Node.js/npm installed, you can use VS Code's Live Server extension:

1. Install VS Code if you haven't already
2. Install the "Live Server" extension in VS Code
3. Open your project folder in VS Code
4. Right-click on `public/index.html`
5. Select "Open with Live Server"

## What This Fixes

### ❌ What Doesn't Work:
- Directly opening `src/index.html` in browser
- Accessing React files without a server

### ✅ What Works:
- React development server (`npm start`)
- Live Server extension
- Proper React application rendering

## Expected Result

After starting the development server, you should see:
- Your React application running at `http://localhost:3000`
- The Music page loading albums from your backend
- All React components working properly

## Backend Connection

The React app is already configured to connect to your backend at:
```
http://localhost/madam-portfolio/backend/api
```

Make sure your XAMPP server is running so the backend API is accessible.

## Troubleshooting

### If `npm start` fails:
1. Make sure Node.js is installed
2. Run `npm install` first
3. Check for any error messages

### If backend connection fails:
1. Make sure XAMPP is running
2. Check that your backend files are in the correct location
3. Test the API directly: `http://localhost/madam-portfolio/backend/api/albums.php`

### If you see a blank page:
1. Check browser console (F12) for errors
2. Make sure all dependencies are installed
3. Verify the backend API is working
