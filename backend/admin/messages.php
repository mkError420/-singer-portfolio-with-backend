<?php
require_once 'auth.php';
$auth = new Auth();
$auth->requireLogin();

$currentUser = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 2rem 0;
        }
        
        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar-header h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #34495e;
        }
        
        .main-content {
            flex: 1;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-view {
            background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 210, 255, 0.4);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .content-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .table-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #7f8c8d;
        }
        
        .empty-state h3 {
            margin-bottom: 1rem;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-unread {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-read {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-replied {
            background: #d4edda;
            color: #155724;
        }
        
        .message-preview {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .filter-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .filter-group label {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .filter-group select {
            padding: 0.5rem;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 8px;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.9);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Welcome, <?php echo htmlspecialchars($currentUser['username']); ?></p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="albums.php">Albums</a></li>
                <li><a href="singles.php">Singles</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="videos.php">Videos</a></li>
                <li><a href="tour.php">Tour Dates</a></li>
                <li><a href="about.php">About Content</a></li>
                <li><a href="messages.php" class="active">Messages</a></li>
                <li><a href="auth.php?action=logout">Logout</a></li>
            </ul>
        </aside>
        
        <main class="main-content">
            <div class="header">
                <h1>Contact Messages</h1>
                <button class="btn btn-primary" onclick="markAllAsRead()">Mark All as Read</button>
            </div>
            
            <div class="filter-section">
                <div class="filter-controls">
                    <div class="filter-group">
                        <label for="statusFilter">Status:</label>
                        <select id="statusFilter" onchange="applyFilters()">
                            <option value="">All Messages</option>
                            <option value="unread">Unread</option>
                            <option value="read">Read</option>
                            <option value="replied">Replied</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <button class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
                    </div>
                </div>
            </div>
            
            <div class="content-section">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="messages-table">
                        <!-- Messages will be loaded here -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <!-- Message Modal -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Message Details</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div id="messageDetails">
                <!-- Message details will be loaded here -->
            </div>
            <div class="form-actions">
                <button type="button" class="btn" onclick="closeModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="markAsReplied()">Mark as Replied</button>
                <button type="button" class="btn btn-delete" onclick="deleteMessage()">Delete Message</button>
            </div>
        </div>
    </div>
    
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
        }
        
        .modal-content {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            margin: 5% auto;
            padding: 2.5rem;
            border-radius: 20px;
            width: 90%;
            max-width: 700px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .modal-header h2 {
            color: #2c3e50;
            margin: 0;
        }
        
        .close {
            font-size: 2rem;
            cursor: pointer;
            color: #7f8c8d;
        }
        
        .message-details {
            background: rgba(248, 249, 250, 0.5);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }
        
        .message-detail-row {
            margin-bottom: 1rem;
        }
        
        .message-detail-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        
        .message-detail-value {
            color: #495057;
            line-height: 1.6;
        }
        
        .message-content {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            margin-top: 1rem;
            white-space: pre-wrap;
            line-height: 1.6;
        }
    </style>
    
    <script>
        let messages = [];
        let filteredMessages = [];
        let currentMessage = null;
        
        // Load messages
        async function loadMessages() {
            try {
                const response = await fetch('../api/messages.php');
                if (response.ok) {
                    messages = await response.json();
                    filteredMessages = [...messages];
                    renderMessages();
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }
        
        // Render messages table
        function renderMessages() {
            const tbody = document.getElementById('messages-table');
            tbody.innerHTML = '';
            
            if (filteredMessages.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="empty-state">
                            <h3>No messages found</h3>
                            <p>No contact messages match your current filters</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            filteredMessages.forEach(message => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>${message.name || 'N/A'}</strong></td>
                    <td>${message.email || 'N/A'}</td>
                    <td>${message.subject || 'No Subject'}</td>
                    <td class="message-preview">${message.message || 'No Message'}</td>
                    <td>
                        <span class="status-badge status-${message.status}">
                            ${message.status || 'unread'}
                        </span>
                    </td>
                    <td>${message.created_at ? new Date(message.created_at).toLocaleDateString() : 'N/A'}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-view btn-sm" onclick="viewMessage(${message.id})">View</button>
                            <button class="btn btn-delete btn-sm" onclick="deleteMessage(${message.id})">Delete</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // Apply filters
        function applyFilters() {
            const statusFilter = document.getElementById('statusFilter').value;
            
            filteredMessages = messages.filter(message => {
                if (statusFilter && message.status !== statusFilter) {
                    return false;
                }
                return true;
            });
            
            renderMessages();
        }
        
        // View message details
        function viewMessage(id) {
            const message = messages.find(m => m.id === id);
            if (message) {
                currentMessage = message;
                
                // Mark as read if unread
                if (message.status === 'unread') {
                    updateMessageStatus(id, 'read');
                }
                
                const detailsHtml = `
                    <div class="message-detail-row">
                        <div class="message-detail-label">From:</div>
                        <div class="message-detail-value">${message.name} (${message.email})</div>
                    </div>
                    <div class="message-detail-row">
                        <div class="message-detail-label">Subject:</div>
                        <div class="message-detail-value">${message.subject || 'No Subject'}</div>
                    </div>
                    <div class="message-detail-row">
                        <div class="message-detail-label">Date:</div>
                        <div class="message-detail-value">${message.created_at ? new Date(message.created_at).toLocaleString() : 'N/A'}</div>
                    </div>
                    <div class="message-content">${message.message || 'No Message'}</div>
                `;
                
                document.getElementById('messageDetails').innerHTML = detailsHtml;
                document.getElementById('messageModal').style.display = 'block';
            }
        }
        
        // Update message status
        async function updateMessageStatus(id, status) {
            try {
                const response = await fetch('../api/messages.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id, status: status })
                });
                
                if (response.ok) {
                    // Update local message
                    const messageIndex = messages.findIndex(m => m.id === id);
                    if (messageIndex !== -1) {
                        messages[messageIndex].status = status;
                    }
                    applyFilters(); // Re-render with updated status
                }
            } catch (error) {
                console.error('Error updating message status:', error);
            }
        }
        
        // Mark all as read
        async function markAllAsRead() {
            const unreadMessages = messages.filter(m => m.status === 'unread');
            
            for (const message of unreadMessages) {
                await updateMessageStatus(message.id, 'read');
            }
            
            alert(`Marked ${unreadMessages.length} messages as read!`);
        }
        
        // Mark as replied
        async function markAsReplied() {
            if (currentMessage) {
                await updateMessageStatus(currentMessage.id, 'replied');
                alert('Message marked as replied!');
                closeModal();
            }
        }
        
        // Delete message
        async function deleteMessage(id) {
            const messageId = id || currentMessage?.id;
            
            if (!messageId) return;
            
            if (confirm('Are you sure you want to delete this message?')) {
                try {
                    const response = await fetch('../api/messages.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: messageId })
                    });
                    
                    if (response.ok) {
                        // Remove from local arrays
                        messages = messages.filter(m => m.id !== messageId);
                        applyFilters(); // Re-render
                        closeModal();
                        alert('Message deleted successfully!');
                    } else {
                        alert('Error deleting message');
                    }
                } catch (error) {
                    console.error('Error deleting message:', error);
                    alert('Error deleting message');
                }
            }
        }
        
        // Close modal
        function closeModal() {
            document.getElementById('messageModal').style.display = 'none';
            currentMessage = null;
        }
        
        // Load messages when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadMessages();
        });
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('messageModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
