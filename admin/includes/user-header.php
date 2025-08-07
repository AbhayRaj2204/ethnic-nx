<div class="header">
    <div class="header-left">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="breadcrumb">
            <span class="breadcrumb-item">User Dashboard</span>
        </div>
    </div>

    <div class="header-right">
        <div class="header-actions">
            <a href="../index.php" class="header-btn" title="Visit Store" target="_blank">
                <i class="fas fa-external-link-alt"></i>
            </a>
            
            <div class="user-menu">
                <button class="user-menu-toggle" id="userMenuToggle">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] ?: $currentUser['username']); ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></span>
                            <span class="user-email"><?php echo htmlspecialchars($currentUser['email']); ?></span>
                        </div>
                    </div>
                    
                    <div class="dropdown-body">
                        <a href="user-profile.php" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="user-orders.php" class="dropdown-item">
                            <i class="fas fa-shopping-bag"></i>
                            <span>My Orders</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
