<?php
session_start();
require_once __DIR__ . '/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = new CSVDatabase();
        $this->initializeAdminUser();
    }
    
    private function initializeAdminUser() {
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        
        $users = $userModel->getAll();
        if (empty($users)) {
            // The User model will handle creating the default admin
        }
    }
    
    public function login($username, $password) {
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        
        // Try to find user by username or email
        $user = $userModel->getByUsername($username);
        if (!$user) {
            $user = $userModel->getByEmail($username);
        }
        
        if ($user && password_verify($password, $user['password']) && $user['status'] === 'active') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
    }
    
    public function isUser() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'user';
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
    
    public function requireAdmin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
        
        if ($_SESSION['role'] !== 'admin') {
            // Redirect users to their dashboard
            header('Location: user-dashboard.php');
            exit();
        }
    }
    
    public function requireUser() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
        
        if ($_SESSION['role'] !== 'user') {
            // Redirect admin to admin dashboard
            header('Location: index.php');
            exit();
        }
    }
    
    public function requireAdminOrUser() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
        
        if (!in_array($_SESSION['role'], ['admin', 'user'])) {
            header('Location: login.php');
            exit();
        }
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        return $userModel->getById($_SESSION['user_id']);
    }
    
    public function redirectToDashboard() {
        if ($this->isAdmin()) {
            header('Location: index.php');
        } elseif ($this->isUser()) {
            header('Location: user-dashboard.php');
        } else {
            header('Location: login.php');
        }
        exit();
    }
}
?>
