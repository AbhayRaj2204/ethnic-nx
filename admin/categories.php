<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Category.php';

$auth = new Auth();
$auth->requireAdmin();

$categoryModel = new Category();
$currentUser = $auth->getCurrentUser();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'create':
                $data = [
                    'name' => $_POST['name'] ?? '',
                    'slug' => $_POST['slug'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'status' => $_POST['status'] ?? 'active'
                ];
                
                $result = $categoryModel->create($data);
                echo json_encode($result);
                break;
                
            case 'update':
                $id = $_POST['id'] ?? 0;
                $data = [
                    'name' => $_POST['name'] ?? '',
                    'slug' => $_POST['slug'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'status' => $_POST['status'] ?? 'active'
                ];
                
                $result = $categoryModel->update($id, $data);
                echo json_encode($result);
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? 0;
                $result = $categoryModel->delete($id);
                echo json_encode($result);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}

// Get categories
$categories = $categoryModel->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h1 class="page-title">Categories</h1>
                                <p class="page-subtitle">Manage product categories</p>
                            </div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                <i class="fas fa-plus"></i> Add Category
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">All Categories</h5>
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" class="form-control search-input" placeholder="Search categories...">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Slug</th>
                                                <th>Description</th>
                                                <th>Products</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($category['id']); ?></td>
                                                <td>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($category['name']); ?></div>
                                                </td>
                                                <td>
                                                    <code><?php echo htmlspecialchars($category['slug']); ?></code>
                                                </td>
                                                <td><?php echo htmlspecialchars(substr($category['description'], 0, 50)) . (strlen($category['description']) > 50 ? '...' : ''); ?></td>
                                                <td>
                                                    <?php
                                                    $productCount = count($categoryModel->hasProducts($category['id']) ? [1] : []);
                                                    echo $productCount;
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $category['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst($category['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($category['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary edit-category" 
                                                                data-id="<?php echo $category['id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                                                data-slug="<?php echo htmlspecialchars($category['slug']); ?>"
                                                                data-description="<?php echo htmlspecialchars($category['description']); ?>"
                                                                data-status="<?php echo $category['status']; ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger delete-category" 
                                                                data-id="<?php echo $category['id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($category['name']); ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="categoryForm" action="categories.php" method="POST" data-ajax="true">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <input type="hidden" name="id" value="">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug *</label>
                            <input type="text" class="form-control" name="slug" required>
                            <div class="form-text">URL-friendly version of the name</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryModal = document.getElementById('categoryModal');
            const categoryForm = document.getElementById('categoryForm');
            const modalTitle = categoryModal.querySelector('.modal-title');
            
            // Edit category
            document.querySelectorAll('.edit-category').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const slug = this.dataset.slug;
                    const description = this.dataset.description;
                    const status = this.dataset.status;
                    
                    modalTitle.textContent = 'Edit Category';
                    categoryForm.querySelector('input[name="action"]').value = 'update';
                    categoryForm.querySelector('input[name="id"]').value = id;
                    categoryForm.querySelector('input[name="name"]').value = name;
                    categoryForm.querySelector('input[name="slug"]').value = slug;
                    categoryForm.querySelector('textarea[name="description"]').value = description;
                    categoryForm.querySelector('select[name="status"]').value = status;
                    
                    new bootstrap.Modal(categoryModal).show();
                });
            });
            
            // Delete category
            document.querySelectorAll('.delete-category').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    
                    if (confirm(`Are you sure you want to delete "${name}"?`)) {
                        const formData = new FormData();
                        formData.append('action', 'delete');
                        formData.append('id', id);
                        
                        fetch('categories.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showAlert(data.message, 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                showAlert(data.message, 'danger');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showAlert('An error occurred. Please try again.', 'danger');
                        });
                    }
                });
            });
            
            // Reset form when modal is hidden
            categoryModal.addEventListener('hidden.bs.modal', function() {
                modalTitle.textContent = 'Add Category';
                categoryForm.reset();
                categoryForm.querySelector('input[name="action"]').value = 'create';
                categoryForm.querySelector('input[name="id"]').value = '';
            });
        });
    </script>
</body>
</html>
