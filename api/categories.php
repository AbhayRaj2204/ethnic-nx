<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Category.php';

$categoryModel = new Category();

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $action = $_GET['action'] ?? 'all';
            
            switch ($action) {
                case 'active':
                    $categories = $categoryModel->getActive();
                    echo json_encode(['success' => true, 'data' => $categories]);
                    break;
                    
                case 'single':
                    $id = $_GET['id'] ?? 0;
                    $category = $categoryModel->getById($id);
                    if ($category) {
                        echo json_encode(['success' => true, 'data' => $category]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Category not found']);
                    }
                    break;
                    
                case 'by-slug':
                    $slug = $_GET['slug'] ?? '';
                    $category = $categoryModel->getBySlug($slug);
                    if ($category) {
                        echo json_encode(['success' => true, 'data' => $category]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Category not found']);
                    }
                    break;
                    
                default:
                    $categories = $categoryModel->getAll();
                    echo json_encode(['success' => true, 'data' => $categories]);
                    break;
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }
            
            $action = $input['action'] ?? '';
            
            switch ($action) {
                case 'create':
                    $data = [
                        'name' => $input['name'] ?? '',
                        'slug' => $input['slug'] ?? '',
                        'description' => $input['description'] ?? '',
                        'status' => $input['status'] ?? 'active'
                    ];
                    
                    $result = $categoryModel->create($data);
                    echo json_encode($result);
                    break;
                    
                case 'update':
                    $id = $input['id'] ?? 0;
                    $data = [
                        'name' => $input['name'] ?? '',
                        'slug' => $input['slug'] ?? '',
                        'description' => $input['description'] ?? '',
                        'status' => $input['status'] ?? 'active'
                    ];
                    
                    $result = $categoryModel->update($id, $data);
                    echo json_encode($result);
                    break;
                    
                case 'delete':
                    $id = $input['id'] ?? 0;
                    $result = $categoryModel->delete($id);
                    echo json_encode($result);
                    break;
                    
                default:
                    echo json_encode(['success' => false, 'message' => 'Invalid action']);
                    break;
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
