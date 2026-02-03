<?php
session_start();
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function login($username, $password) {
        $query = "SELECT * FROM admin_users WHERE username = :username AND status = 'active'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['login_time'] = time();
            
            return true;
        }
        
        return false;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['admin_id']) && 
               isset($_SESSION['login_time']) && 
               (time() - $_SESSION['login_time'] < SESSION_LIFETIME);
    }
    
    public function logout() {
        session_destroy();
        header('Location: login.php');
        exit;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
    
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['admin_id'],
                'username' => $_SESSION['admin_username'],
                'role' => $_SESSION['admin_role']
            ];
        }
        return null;
    }
}

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $auth = new Auth();
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($auth->login($username, $password)) {
        header('Location: index.php');
    } else {
        header('Location: login.php?error=1');
    }
    exit;
}

// Handle logout request
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $auth = new Auth();
    $auth->logout();
}
?>
