<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/User.php';

$auth = new Auth();
$auth->requireAdmin();

$productModel = new Product();
$categoryModel = new Category();
$userModel = new User();

// Get statistics
$totalProducts = count($productModel->getAll());
$activeProducts = count($productModel->getActive());
$totalCategories = count($categoryModel->getAll());
$totalUsers = count($userModel->getAll());

$currentUser = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ethnic NX</title>
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
                        <h1 class="page-title">Dashboard</h1>
                        <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($currentUser['username']); ?>!</p>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-primary">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number"><?php echo $totalProducts; ?></div>
                                <div class="stats-label">Total Products</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number"><?php echo $activeProducts; ?></div>
                                <div class="stats-label">Active Products</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number"><?php echo $totalCategories; ?></div>
                                <div class="stats-label">Categories</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon bg-info">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number"><?php echo $totalUsers; ?></div>
                                <div class="stats-label">Total Users</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Products -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Recent Products</h5>
                                <a href="products.php" class="btn btn-primary btn-sm">View All</a>
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $products = array_slice($productModel->getAll(), -5);
                                            $categories = $categoryModel->getAll();
                                            
                                            foreach (array_reverse($products) as $product):
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
</body>
</html>
