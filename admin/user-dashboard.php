<?php
require_once '../config/auth.php';

$auth = new Auth();
$auth->requireUser(); // Only users can access this page

$currentUser = $auth->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Ethnic NX</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Include User Sidebar -->
        <?php include 'includes/user-sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Include User Header -->
            <?php include 'includes/user-header.php'; ?>

            <!-- Dashboard Content -->
            <div class="content-wrapper">
                <div class="page-header">
                    <h1 class="page-title">Welcome, <?php echo htmlspecialchars($currentUser['first_name'] ?: $currentUser['username']); ?>!</h1>
                    <p class="page-subtitle">Manage your account and view your information</p>
                </div>

                <!-- Dashboard Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">0</h3>
                            <p class="stat-label">Total Orders</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">0</h3>
                            <p class="stat-label">Wishlist Items</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">0</h3>
                            <p class="stat-label">Pending Orders</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">0</h3>
                            <p class="stat-label">Completed Orders</p>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="content-grid">
                    <div class="content-card">
                        <div class="card-header">
                            <h2 class="card-title">Account Information</h2>
                            <a href="user-profile.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                                Edit Profile
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Username</label>
                                    <span><?php echo htmlspecialchars($currentUser['username']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Email</label>
                                    <span><?php echo htmlspecialchars($currentUser['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>First Name</label>
                                    <span><?php echo htmlspecialchars($currentUser['first_name'] ?: 'Not provided'); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Last Name</label>
                                    <span><?php echo htmlspecialchars($currentUser['last_name'] ?: 'Not provided'); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Phone</label>
                                    <span><?php echo htmlspecialchars($currentUser['phone'] ?: 'Not provided'); ?></span>
                                </div>
                                <div class="info-item">
                                    <label>Company</label>
                                    <span><?php echo htmlspecialchars($currentUser['company'] ?: 'Not provided'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header">
                            <h2 class="card-title">Quick Actions</h2>
                        </div>
                        <div class="card-body">
                            <div class="action-buttons">
                                <a href="../index.php" class="action-btn">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Visit Store</span>
                                </a>
                                <a href="../products.php" class="action-btn">
                                    <i class="fas fa-tags"></i>
                                    <span>Browse Products</span>
                                </a>
                                <a href="../contact.php" class="action-btn">
                                    <i class="fas fa-phone"></i>
                                    <span>Contact Support</span>
                                </a>
                                <a href="user-profile.php" class="action-btn">
                                    <i class="fas fa-user-cog"></i>
                                    <span>Account Settings</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">Recent Activity</h2>
                    </div>
                    <div class="card-body">
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h3>No Recent Activity</h3>
                            <p>Your recent activities will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>
