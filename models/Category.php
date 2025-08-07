<?php
require_once __DIR__ . '/../config/database.php';

class Category {
    private $db;
    private $filename = 'categories.csv';
    
    public function __construct() {
        $this->db = new CSVDatabase();
        $this->initializeCategories();
    }
    
    private function initializeCategories() {
        $categories = $this->db->read($this->filename);
        if (empty($categories)) {
            $defaultCategories = [
                ['id' => 1, 'name' => 'Sherwani', 'slug' => 'sherwani', 'description' => 'Traditional Sherwani Collection', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 2, 'name' => 'Indo-Western', 'slug' => 'indo-western', 'description' => 'Modern Indo-Western Wear', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 3, 'name' => 'Blazer', 'slug' => 'blazer', 'description' => 'Premium Blazer Collection', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 4, 'name' => 'Jodhpuri Suit', 'slug' => 'jodhpuri-suit', 'description' => 'Royal Jodhpuri Suits', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 5, 'name' => 'Koti-Kurta', 'slug' => 'koti-kurta', 'description' => 'Traditional Koti-Kurta Sets', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 6, 'name' => 'Kurta', 'slug' => 'kurta', 'description' => 'Designer Kurta Collection', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 7, 'name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Traditional Accessories', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]
            ];
            
            $headers = ['id', 'name', 'slug', 'description', 'status', 'created_at', 'updated_at'];
            $this->db->write($this->filename, $defaultCategories, $headers);
        }
    }
    
    public function getAll() {
        return $this->db->read($this->filename);
    }
    
    public function getActive() {
        $categories = $this->getAll();
        return array_filter($categories, function($category) {
            return $category['status'] === 'active';
        });
    }
    
    public function getById($id) {
        $categories = $this->getAll();
        foreach ($categories as $category) {
            if ($category['id'] == $id) {
                return $category;
            }
        }
        return null;
    }
    
    public function getBySlug($slug) {
        $categories = $this->getAll();
        foreach ($categories as $category) {
            if ($category['slug'] === $slug) {
                return $category;
            }
        }
        return null;
    }
    
    public function create($data) {
        $categories = $this->getAll();
        
        // Validate required fields
        if (empty($data['name'])) {
            return ['success' => false, 'message' => 'Category name is required'];
        }
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        // Check if name already exists
        foreach ($categories as $category) {
            if (strtolower($category['name']) === strtolower(trim($data['name']))) {
                return ['success' => false, 'message' => 'Category name already exists'];
            }
        }
        
        // Check if slug already exists
        foreach ($categories as $category) {
            if ($category['slug'] === $data['slug']) {
                return ['success' => false, 'message' => 'Category slug already exists'];
            }
        }
        
        $newCategory = [
            'id' => $this->db->generateId($this->filename),
            'name' => trim($data['name']),
            'slug' => $data['slug'],
            'description' => trim($data['description'] ?? ''),
            'status' => $data['status'] ?? 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $categories[] = $newCategory;
        
        if ($this->db->write($this->filename, $categories)) {
            return ['success' => true, 'message' => 'Category created successfully', 'data' => $newCategory];
        }
        
        return ['success' => false, 'message' => 'Failed to create category'];
    }
    
    public function update($id, $data) {
        $categories = $this->getAll();
        $updated = false;
        
        // Validate required fields
        if (empty($data['name'])) {
            return ['success' => false, 'message' => 'Category name is required'];
        }
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        foreach ($categories as &$category) {
            if ($category['id'] == $id) {
                // Check if name already exists (excluding current category)
                foreach ($categories as $checkCategory) {
                    if ($checkCategory['id'] != $id && strtolower($checkCategory['name']) === strtolower(trim($data['name']))) {
                        return ['success' => false, 'message' => 'Category name already exists'];
                    }
                }
                
                // Check if slug already exists (excluding current category)
                foreach ($categories as $checkCategory) {
                    if ($checkCategory['id'] != $id && $checkCategory['slug'] === $data['slug']) {
                        return ['success' => false, 'message' => 'Category slug already exists'];
                    }
                }
                
                $category['name'] = trim($data['name']);
                $category['slug'] = $data['slug'];
                $category['description'] = trim($data['description'] ?? $category['description']);
                $category['status'] = $data['status'] ?? $category['status'];
                $category['updated_at'] = date('Y-m-d H:i:s');
                $updated = true;
                break;
            }
        }
        
        if ($updated && $this->db->write($this->filename, $categories)) {
            return ['success' => true, 'message' => 'Category updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update category'];
    }
    
    public function delete($id) {
        // Check if category has products
        if ($this->hasProducts($id)) {
            return ['success' => false, 'message' => 'Cannot delete category with existing products'];
        }
        
        $categories = $this->getAll();
        $filteredCategories = array_filter($categories, function($category) use ($id) {
            return $category['id'] != $id;
        });
        
        if (count($filteredCategories) < count($categories)) {
            if ($this->db->write($this->filename, array_values($filteredCategories))) {
                return ['success' => true, 'message' => 'Category deleted successfully'];
            }
        }
        
        return ['success' => false, 'message' => 'Failed to delete category'];
    }
    
    public function hasProducts($categoryId) {
        require_once __DIR__ . '/Product.php';
        $productModel = new Product();
        $products = $productModel->getByCategoryId($categoryId);
        return !empty($products);
    }
    
    public function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
}
?>
