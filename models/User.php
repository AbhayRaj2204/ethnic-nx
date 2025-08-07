<?php
class User {
    private $csvFile;
    private $users = [];
    
    public function __construct() {
        $this->csvFile = __DIR__ . '/../data/users.csv';
        $this->loadUsers();
    }
    
    private function loadUsers() {
        $this->users = [];
        
        if (!file_exists($this->csvFile)) {
            // Create default admin user if file doesn't exist
            $this->createDefaultAdmin();
            return;
        }
        
        $handle = fopen($this->csvFile, 'r');
        if ($handle !== FALSE) {
            // Skip header row
            $header = fgetcsv($handle);
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (count($data) >= 9) { // Ensure we have all required fields
                    $this->users[] = [
                        'id' => (int)$data[0],
                        'username' => $data[1],
                        'email' => $data[2],
                        'password' => $data[3],
                        'role' => $data[4],
                        'status' => $data[5],
                        'first_name' => $data[6] ?? '',
                        'last_name' => $data[7] ?? '',
                        'phone' => $data[8] ?? '',
                        'company' => $data[9] ?? '',
                        'created_at' => $data[10] ?? date('Y-m-d H:i:s')
                    ];
                }
            }
            fclose($handle);
        }
    }
    
    private function createDefaultAdmin() {
        $defaultAdmin = [
            'id' => 1,
            'username' => 'admin',
            'email' => 'admin@ethnicnx.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'status' => 'active',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '',
            'company' => 'EthnicNX',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->users[] = $defaultAdmin;
        $this->saveUsers();
    }
    
    private function saveUsers() {
        $handle = fopen($this->csvFile, 'w');
        if ($handle !== FALSE) {
            // Write header
            fputcsv($handle, ['id', 'username', 'email', 'password', 'role', 'status', 'first_name', 'last_name', 'phone', 'company', 'created_at']);
            
            // Write data
            foreach ($this->users as $user) {
                fputcsv($handle, [
                    $user['id'],
                    $user['username'],
                    $user['email'],
                    $user['password'],
                    $user['role'],
                    $user['status'],
                    $user['first_name'] ?? '',
                    $user['last_name'] ?? '',
                    $user['phone'] ?? '',
                    $user['company'] ?? '',
                    $user['created_at']
                ]);
            }
            fclose($handle);
        }
    }
    
    public function getAll() {
        return $this->users;
    }
    
    public function getById($id) {
        foreach ($this->users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }
        return null;
    }
    
    public function getByUsername($username) {
        foreach ($this->users as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }
        return null;
    }
    
    public function getByEmail($email) {
        foreach ($this->users as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }
    
    public function create($data) {
        try {
            // Validate required fields
            if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                return ['success' => false, 'message' => 'Username, email, and password are required'];
            }
            
            // Check if username already exists
            if ($this->getByUsername($data['username'])) {
                return ['success' => false, 'message' => 'Username already exists'];
            }
            
            // Check if email already exists
            if ($this->getByEmail($data['email'])) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            // Generate new ID
            $newId = 1;
            if (!empty($this->users)) {
                $newId = max(array_column($this->users, 'id')) + 1;
            }
            
            // Create new user
            $newUser = [
                'id' => $newId,
                'username' => trim($data['username']),
                'email' => trim($data['email']),
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role' => $data['role'] ?? 'user',
                'status' => $data['status'] ?? 'active',
                'first_name' => trim($data['first_name'] ?? ''),
                'last_name' => trim($data['last_name'] ?? ''),
                'phone' => trim($data['phone'] ?? ''),
                'company' => trim($data['company'] ?? ''),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->users[] = $newUser;
            $this->saveUsers();
            
            return ['success' => true, 'message' => 'User created successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error creating user: ' . $e->getMessage()];
        }
    }
    
    public function update($id, $data) {
        try {
            $userIndex = null;
            foreach ($this->users as $index => $user) {
                if ($user['id'] == $id) {
                    $userIndex = $index;
                    break;
                }
            }
            
            if ($userIndex === null) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Validate required fields
            if (empty($data['username']) || empty($data['email'])) {
                return ['success' => false, 'message' => 'Username and email are required'];
            }
            
            // Check if username already exists (excluding current user)
            foreach ($this->users as $user) {
                if ($user['username'] === $data['username'] && $user['id'] != $id) {
                    return ['success' => false, 'message' => 'Username already exists'];
                }
            }
            
            // Check if email already exists (excluding current user)
            foreach ($this->users as $user) {
                if ($user['email'] === $data['email'] && $user['id'] != $id) {
                    return ['success' => false, 'message' => 'Email already exists'];
                }
            }
            
            // Update user data
            $this->users[$userIndex]['username'] = trim($data['username']);
            $this->users[$userIndex]['email'] = trim($data['email']);
            $this->users[$userIndex]['role'] = $data['role'] ?? 'user';
            $this->users[$userIndex]['status'] = $data['status'] ?? 'active';
            $this->users[$userIndex]['first_name'] = trim($data['first_name'] ?? '');
            $this->users[$userIndex]['last_name'] = trim($data['last_name'] ?? '');
            $this->users[$userIndex]['phone'] = trim($data['phone'] ?? '');
            $this->users[$userIndex]['company'] = trim($data['company'] ?? '');
            
            // Update password if provided
            if (!empty($data['password'])) {
                $this->users[$userIndex]['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            $this->saveUsers();
            
            return ['success' => true, 'message' => 'User updated successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error updating user: ' . $e->getMessage()];
        }
    }
    
    public function delete($id) {
        try {
            $userIndex = null;
            foreach ($this->users as $index => $user) {
                if ($user['id'] == $id) {
                    $userIndex = $index;
                    break;
                }
            }
            
            if ($userIndex === null) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Don't allow deleting the main admin user
            if ($this->users[$userIndex]['username'] === 'admin' && $this->users[$userIndex]['role'] === 'admin') {
                return ['success' => false, 'message' => 'Cannot delete the main admin user'];
            }
            
            unset($this->users[$userIndex]);
            $this->users = array_values($this->users); // Re-index array
            $this->saveUsers();
            
            return ['success' => true, 'message' => 'User deleted successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()];
        }
    }
    
    public function verifyPassword($username, $password) {
        $user = $this->getByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
?>
