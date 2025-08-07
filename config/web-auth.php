<?php
session_start();
require_once __DIR__ . '/database.php';

class WebAuth {
    private $db;
    
    public function __construct() {
        $this->db = new CSVDatabase();
    }
    
    public function login($username, $password) {
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        
        // Try to find user by username or email
        $user = $userModel->getByUsername($username);
        if (!$user) {
            $user = $userModel->getByEmail($username);
        }
        
        // Only allow users with 'user' role to login to website
        if ($user && password_verify($password, $user['password']) && $user['status'] === 'active' && $user['role'] === 'user') {
            $_SESSION['web_user_id'] = $user['id'];
            $_SESSION['web_username'] = $user['username'];
            $_SESSION['web_email'] = $user['email'];
            $_SESSION['web_logged_in'] = true;
            $_SESSION['web_login_time'] = time();
            
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        // Only destroy web session variables
        unset($_SESSION['web_user_id']);
        unset($_SESSION['web_username']);
        unset($_SESSION['web_email']);
        unset($_SESSION['web_logged_in']);
        unset($_SESSION['web_login_time']);
        
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['web_logged_in']) && $_SESSION['web_logged_in'] === true;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            // Store the current page to redirect after login
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
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
        return $userModel->getById($_SESSION['web_user_id']);
    }
    
    public function getRedirectUrl() {
        $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']);
        return $redirect;
    }
}
?>
