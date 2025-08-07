<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

$productModel = new Product();
$categoryModel = new Category();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            switch ($action) {
                case 'featured':
                    $products = $productModel->getFeatured();
                    // Add category names to products
                    $categories = $categoryModel->getAll();
                    foreach ($products as &$product) {
                        foreach ($categories as $category) {
                            if ($category['id'] == $product['category_id']) {
                                $product['category_name'] = $category['name'];
                                $product['category_slug'] = $category['slug'];
                                break;
                            }
                        }
                    }
                    echo json_encode(['success' => true, 'data' => $products]);
                    break;
                    
                case 'by-category':
                    $categorySlug = $_GET['category'] ?? '';
                    if (empty($categorySlug)) {
                        echo json_encode(['success' => false, 'message' => 'Category slug is required']);
                        break;
                    }
                    
                    $category = $categoryModel->getBySlug($categorySlug);
                    if ($category) {
                        $products = $productModel->getByCategoryId($category['id']);
                        // Add category names to products
                        foreach ($products as &$product) {
                            $product['category_name'] = $category['name'];
                            $product['category_slug'] = $category['slug'];
                        }
                        echo json_encode(['success' => true, 'data' => $products]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Category not found']);
                    }
                    break;
                    
                case 'search':
                    $query = $_GET['q'] ?? '';
                    $categoryId = $_GET['category_id'] ?? null;
                    $products = $productModel->search($query, $categoryId);
                    // Add category names to products
                    $categories = $categoryModel->getAll();
                    foreach ($products as &$product) {
                        foreach ($categories as $category) {
                            if ($category['id'] == $product['category_id']) {
                                $product['category_name'] = $category['name'];
                                $product['category_slug'] = $category['slug'];
                                break;
                            }
                        }
                    }
                    echo json_encode(['success' => true, 'data' => $products]);
                    break;
                    
                case 'single':
                    $id = $_GET['id'] ?? 0;
                    if (empty($id)) {
                        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
                        break;
                    }
                    
                    $product = $productModel->getById($id);
                    if ($product) {
                        // Get category name
                        $category = $categoryModel->getById($product['category_id']);
                        if ($category) {
                            $product['category_name'] = $category['name'];
                            $product['category_slug'] = $category['slug'];
                        }
                        echo json_encode(['success' => true, 'data' => $product]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Product not found']);
                    }
                    break;
                    
                default:
                    $products = $productModel->getActive();
                    // Add category names to products
                    $categories = $categoryModel->getAll();
                    foreach ($products as &$product) {
                        foreach ($categories as $category) {
                            if ($category['id'] == $product['category_id']) {
                                $product['category_name'] = $category['name'];
                                $product['category_slug'] = $category['slug'];
                                break;
                            }
                        }
                    }
                    echo json_encode(['success' => true, 'data' => $products]);
                    break;
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while processing your request']);
}
