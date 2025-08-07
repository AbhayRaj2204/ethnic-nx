<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

$auth = new Auth();
$auth->requireAdmin();

$productModel = new Product();
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
                    'price' => $_POST['price'] ?? 0,
                    'category_id' => $_POST['category_id'] ?? 0,
                    'sku' => $_POST['sku'] ?? '',
                    'stock' => $_POST['stock'] ?? 0,
                    'status' => $_POST['status'] ?? 'active',
                    'featured' => isset($_POST['featured']) ? 1 : 0,
                    'images' => $_POST['images'] ?? '',
                    'fabric' => $_POST['fabric'] ?? '',
                    'occasion' => $_POST['occasion'] ?? '',
                    'care_instructions' => $_POST['care_instructions'] ?? '',
                    'sizes' => $_POST['sizes'] ?? ''
                ];
                
                $result = $productModel->create($data);
                echo json_encode($result);
                break;
                
            case 'update':
                $id = $_POST['id'] ?? 0;
                $data = [
                    'name' => $_POST['name'] ?? '',
                    'slug' => $_POST['slug'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'price' => $_POST['price'] ?? 0,
                    'category_id' => $_POST['category_id'] ?? 0,
                    'sku' => $_POST['sku'] ?? '',
                    'stock' => $_POST['stock'] ?? 0,
                    'status' => $_POST['status'] ?? 'active',
                    'featured' => isset($_POST['featured']) ? 1 : 0,
                    'images' => $_POST['images'] ?? '',
                    'fabric' => $_POST['fabric'] ?? '',
                    'occasion' => $_POST['occasion'] ?? '',
                    'care_instructions' => $_POST['care_instructions'] ?? '',
                    'sizes' => $_POST['sizes'] ?? ''
                ];
                
                $result = $productModel->update($id, $data);
                echo json_encode($result);
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? 0;
                $result = $productModel->delete($id);
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

// Get products and categories
$products = $productModel->getAll();
$categories = $categoryModel->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Panel</title>
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
                                <h1 class="page-title">Products</h1>
                                <p class="page-subtitle">Manage your product catalog</p>
                            </div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                                <i class="fas fa-plus"></i> Add Product
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">All Products</h5>
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" class="form-control search-input" placeholder="Search products...">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>Status</th>
                                                <th>Featured</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $product): 
                                                $category = null;
                                                foreach ($categories as $cat) {
                                                    if ($cat['id'] == $product['category_id']) {
                                                        $category = $cat;
                                                        break;
                                                    }
                                                }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo htmlspecialchars($product['images']); ?>" 
                                                             alt="Product" class="product-thumb me-3">
                                                        <div>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></div>
                                                            <small class="text-muted"><?php echo htmlspecialchars($product['sku']); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo $category ? htmlspecialchars($category['name']) : 'N/A'; ?></td>
                                                <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $product['stock'] > 0 ? 'success' : 'danger'; ?>">
                                                        <?php echo $product['stock']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $product['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst($product['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($product['featured']): ?>
                                                        <i class="fas fa-star text-warning"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star text-muted"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($product['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary edit-product" 
                                                                data-product='<?php echo json_encode($product); ?>'>
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger delete-product" 
                                                                data-id="<?php echo $product['id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($product['name']); ?>">
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
    
    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="productForm" action="products.php" method="POST" data-ajax="true">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <input type="hidden" name="id" value="">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name *</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug *</label>
                                    <input type="text" class="form-control" name="slug" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price *</label>
                                    <input type="number" class="form-control" name="price" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category *</label>
                                    <select class="form-control" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sku" class="form-label">SKU</label>
                                    <input type="text" class="form-control" name="sku">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" name="stock" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="featured" id="featured">
                                        <label class="form-check-label" for="featured">
                                            Featured Product
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="images" class="form-label">Image URL</label>
                            <input type="url" class="form-control" name="images" placeholder="https://example.com/image.jpg">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fabric" class="form-label">Fabric</label>
                                    <input type="text" class="form-control" name="fabric">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="occasion" class="form-label">Occasion</label>
                                    <input type="text" class="form-control" name="occasion">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="care_instructions" class="form-label">Care Instructions</label>
                                    <input type="text" class="form-control" name="care_instructions">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sizes" class="form-label">Available Sizes</label>
                                    <input type="text" class="form-control" name="sizes" placeholder="S,M,L,XL,XXL">
                                    <div class="form-text">Comma-separated values</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productModal = document.getElementById('productModal');
            const productForm = document.getElementById('productForm');
            const modalTitle = productModal.querySelector('.modal-title');
            
            // Edit product
            document.querySelectorAll('.edit-product').forEach(button => {
                button.addEventListener('click', function() {
                    const product = JSON.parse(this.dataset.product);
                    
                    modalTitle.textContent = 'Edit Product';
                    productForm.querySelector('input[name="action"]').value = 'update';
                    productForm.querySelector('input[name="id"]').value = product.id;
                    productForm.querySelector('input[name="name"]').value = product.name;
                    productForm.querySelector('input[name="slug"]').value = product.slug;
                    productForm.querySelector('textarea[name="description"]').value = product.description;
                    productForm.querySelector('input[name="price"]').value = product.price;
                    productForm.querySelector('select[name="category_id"]').value = product.category_id;
                    productForm.querySelector('input[name="sku"]').value = product.sku;
                    productForm.querySelector('input[name="stock"]').value = product.stock;
                    productForm.querySelector('select[name="status"]').value = product.status;
                    productForm.querySelector('input[name="featured"]').checked = product.featured == 1;
                    productForm.querySelector('input[name="images"]').value = product.images;
                    productForm.querySelector('input[name="fabric"]').value = product.fabric;
                    productForm.querySelector('input[name="occasion"]').value = product.occasion;
                    productForm.querySelector('input[name="care_instructions"]').value = product.care_instructions;
                    productForm.querySelector('input[name="sizes"]').value = product.sizes;
                    
                    new bootstrap.Modal(productModal).show();
                });
            });
            
            // Delete product
            document.querySelectorAll('.delete-product').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    
                    if (confirm(`Are you sure you want to delete "${name}"?`)) {
                        const formData = new FormData();
                        formData.append('action', 'delete');
                        formData.append('id', id);
                        
                        fetch('products.php', {
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
            productModal.addEventListener('hidden.bs.modal', function() {
                modalTitle.textContent = 'Add Product';
                productForm.reset();
                productForm.querySelector('input[name="action"]').value = 'create';
                productForm.querySelector('input[name="id"]').value = '';
            });
        });
    </script>
</body>
</html>
