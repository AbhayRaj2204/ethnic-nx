<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="../assets/images/logo.png" alt="Ethnic NX" class="logo-img">
            <span class="logo-text">Ethnic NX</span>
        </div>
    </div>

    <div class="sidebar-menu">
        <div class="menu-section">
            <h3 class="menu-title">Dashboard</h3>
            <ul class="menu-list">
                <li class="menu-item active">
                    <a href="user-dashboard.php" class="menu-link">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3 class="menu-title">Account</h3>
            <ul class="menu-list">
                <li class="menu-item">
                    <a href="user-profile.php" class="menu-link">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="user-orders.php" class="menu-link">
                        <i class="fas fa-shopping-bag"></i>
                        <span>My Orders</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="user-wishlist.php" class="menu-link">
                        <i class="fas fa-heart"></i>
                        <span>Wishlist</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3 class="menu-title">Store</h3>
            <ul class="menu-list">
                <li class="menu-item">
                    <a href="../index.php" class="menu-link" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Visit Store</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="../products.php" class="menu-link" target="_blank">
                        <i class="fas fa-tags"></i>
                        <span>Browse Products</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="../contact.php" class="menu-link" target="_blank">
                        <i class="fas fa-phone"></i>
                        <span>Contact Support</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-details">
                <span class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] ?: $currentUser['username']); ?></span>
                <span class="user-role">Customer</span>
            </div>
        </div>
    </div>
</div>
