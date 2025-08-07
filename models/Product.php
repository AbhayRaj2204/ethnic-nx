<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $db;
    private $filename = 'products.csv';
    
    public function __construct() {
        $this->db = new CSVDatabase();
        $this->initializeProducts();
    }
    
    private function initializeProducts() {
        $products = $this->db->read($this->filename);
        if (empty($products)) {
            $defaultProducts = [
                [
                    'id' => 1,
                    'name' => 'Aurum Maroon Grace Rajwadi Koti Kurta Set',
                    'slug' => 'aurum-maroon-grace-rajwadi-koti-kurta-set',
                    'description' => 'Premium Rajwadi Koti Kurta Set in elegant maroon color with intricate handwork and premium fabric',
                    'price' => '8500.00',
                    'category_id' => 5,
                    'sku' => 'ENX001',
                    'stock' => 10,
                    'status' => 'active',
                    'featured' => 1,
                    'images' => 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?w=350&h=350&fit=crop',
                    'fabric' => 'Premium Silk',
                    'occasion' => 'Wedding, Festival',
                    'care_instructions' => 'Dry Clean Only',
                    'sizes' => 'S,M,L,XL,XXL',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 2,
                    'name' => 'Peela Grace Rajwadi Koti Kurta Set',
                    'slug' => 'peela-grace-rajwadi-koti-kurta-set',
                    'description' => 'Beautiful yellow Rajwadi Koti Kurta Set with traditional embroidery',
                    'price' => '8500.00',
                    'category_id' => 5,
                    'sku' => 'ENX002',
                    'stock' => 8,
                    'status' => 'active',
                    'featured' => 1,
                    'images' => 'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=350&h=350&fit=crop',
                    'fabric' => 'Premium Silk',
                    'occasion' => 'Wedding, Festival',
                    'care_instructions' => 'Dry Clean Only',
                    'sizes' => 'S,M,L,XL,XXL',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 3,
                    'name' => 'Royal Blue Sherwani Set',
                    'slug' => 'royal-blue-sherwani-set',
                    'description' => 'Elegant royal blue sherwani with matching churidar and dupatta',
                    'price' => '12500.00',
                    'category_id' => 1,
                    'sku' => 'ENX003',
                    'stock' => 5,
                    'status' => 'active',
                    'featured' => 1,
                    'images' => 'https://images.unsplash.com/photo-1506629905607-d405872a4d86?w=350&h=350&fit=crop',
                    'fabric' => 'Banarasi Silk',
                    'occasion' => 'Wedding, Reception',
                    'care_instructions' => 'Dry Clean Only',
                    'sizes' => 'M,L,XL,XXL',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ];
            
            $headers = ['id', 'name', 'slug', 'description', 'price', 'category_id', 'sku', 'stock', 'status', 'featured', 'images', 'fabric', 'occasion', 'care_instructions', 'sizes', 'created_at', 'updated_at'];
            $this->db->write($this->filename, $defaultProducts, $headers);
        }
    }
    
    public function getAll() {
        return $this->db->read($this->filename);
    }
    
    public function getActive() {
        $products = $this->getAll();
        return array_filter($products, function($product) {
            return $product['status'] === 'active';
        });
    }
    
    public function getFeatured() {
        $products = $this->getActive();
        return array_filter($products, function($product) {
            return $product['featured'] == 1;
        });
    }
    
    public function getById($id) {
        $products = $this->getAll();
        foreach ($products as $product) {
            if ($product['id'] == $id) {
                return $product;
            }
        }
        return null;
    }
    
    public function getBySlug($slug) {
        $products = $this->getAll();
        foreach ($products as $product) {
            if ($product['slug'] === $slug) {
                return $product;
            }
        }
        return null;
    }
    
    public function getByCategoryId($categoryId) {
        $products = $this->getActive();
        return array_filter($products, function($product) use ($categoryId) {
            return $product['category_id'] == $categoryId;
        });
    }
    
    public function create($data) {
        $products = $this->getAll();
        
        // Validate required fields
        if (empty($data['name']) || empty($data['price']) || empty($data['category_id'])) {
            return ['success' => false, 'message' => 'Name, price, and category are required'];
        }
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        // Check if slug already exists
        foreach ($products as $product) {
            if ($product['slug'] === $data['slug']) {
                return ['success' => false, 'message' => 'Product slug already exists'];
            }
        }
        
        // Check if SKU already exists
        if (!empty($data['sku'])) {
            foreach ($products as $product) {
                if ($product['sku'] === $data['sku']) {
                    return ['success' => false, 'message' => 'Product SKU already exists'];
                }
            }
        }
        
        $newProduct = [
            'id' => $this->db->generateId($this->filename),
            'name' => trim($data['name']),
            'slug' => $data['slug'],
            'description' => trim($data['description'] ?? ''),
            'price' => floatval($data['price']),
            'category_id' => intval($data['category_id']),
            'sku' => trim($data['sku'] ?? ''),
            'stock' => intval($data['stock'] ?? 0),
            'status' => $data['status'] ?? 'active',
            'featured' => intval($data['featured'] ?? 0),
            'images' => trim($data['images'] ?? ''),
            'fabric' => trim($data['fabric'] ?? ''),
            'occasion' => trim($data['occasion'] ?? ''),
            'care_instructions' => trim($data['care_instructions'] ?? ''),
            'sizes' => trim($data['sizes'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $products[] = $newProduct;
        
        if ($this->db->write($this->filename, $products)) {
            return ['success' => true, 'message' => 'Product created successfully', 'data' => $newProduct];
        }
        
        return ['success' => false, 'message' => 'Failed to create product'];
    }
    
    public function update($id, $data) {
        $products = $this->getAll();
        $updated = false;
        
        // Validate required fields
        if (empty($data['name']) || empty($data['price']) || empty($data['category_id'])) {
            return ['success' => false, 'message' => 'Name, price, and category are required'];
        }
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        foreach ($products as &$product) {
            if ($product['id'] == $id) {
                // Check if slug already exists (excluding current product)
                foreach ($products as $checkProduct) {
                    if ($checkProduct['id'] != $id && $checkProduct['slug'] === $data['slug']) {
                        return ['success' => false, 'message' => 'Product slug already exists'];
                    }
                }
                
                // Check if SKU already exists (excluding current product)
                if (!empty($data['sku'])) {
                    foreach ($products as $checkProduct) {
                        if ($checkProduct['id'] != $id && $checkProduct['sku'] === $data['sku']) {
                            return ['success' => false, 'message' => 'Product SKU already exists'];
                        }
                    }
                }
                
                $product['name'] = trim($data['name']);
                $product['slug'] = $data['slug'];
                $product['description'] = trim($data['description'] ?? $product['description']);
                $product['price'] = floatval($data['price']);
                $product['category_id'] = intval($data['category_id']);
                $product['sku'] = trim($data['sku'] ?? $product['sku']);
                $product['stock'] = intval($data['stock'] ?? $product['stock']);
                $product['status'] = $data['status'] ?? $product['status'];
                $product['featured'] = intval($data['featured'] ?? $product['featured']);
                $product['images'] = trim($data['images'] ?? $product['images']);
                $product['fabric'] = trim($data['fabric'] ?? $product['fabric']);
                $product['occasion'] = trim($data['occasion'] ?? $product['occasion']);
                $product['care_instructions'] = trim($data['care_instructions'] ?? $product['care_instructions']);
                $product['sizes'] = trim($data['sizes'] ?? $product['sizes']);
                $product['updated_at'] = date('Y-m-d H:i:s');
                $updated = true;
                break;
            }
        }
        
        if ($updated && $this->db->write($this->filename, $products)) {
            return ['success' => true, 'message' => 'Product updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update product'];
    }
    
    public function delete($id) {
        $products = $this->getAll();
        $filteredProducts = array_filter($products, function($product) use ($id) {
            return $product['id'] != $id;
        });
        
        if (count($filteredProducts) < count($products)) {
            if ($this->db->write($this->filename, array_values($filteredProducts))) {
                return ['success' => true, 'message' => 'Product deleted successfully'];
            }
        }
        
        return ['success' => false, 'message' => 'Failed to delete product'];
    }
    
    public function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
    
    public function search($query, $categoryId = null) {
        $products = $this->getActive();
        
        $results = array_filter($products, function($product) use ($query, $categoryId) {
            $matchesQuery = empty($query) || 
                           stripos($product['name'], $query) !== false || 
                           stripos($product['description'], $query) !== false ||
                           stripos($product['sku'], $query) !== false;
            
            $matchesCategory = empty($categoryId) || $product['category_id'] == $categoryId;
            
            return $matchesQuery && $matchesCategory;
        });
        
        return array_values($results);
    }
}
?>
