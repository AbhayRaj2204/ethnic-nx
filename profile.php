<?php require_once 'check-auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Ethnic NX</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }
        
        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #e53e3e 0%, #dc2626 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
        }
        
        .profile-name {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .profile-email {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .profile-body {
            padding: 40px;
        }
        
        .profile-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-weight: 500;
            color: #666;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: #333;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .profile-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        
        .btn-profile {
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #e53e3e 0%, #dc2626 100%);
            color: white;
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .btn-profile:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-active {
            background: #dcfce7;
            color: #16a34a;
        }
        
        .breadcrumb {
            background: #f8f9fa;
            padding: 20px 0;
            margin-top: 80px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="logo">
                    <a href="index.php">
                        <img src="assets/images/logo.png" alt="Ethnic NX" class="logo-img">
                    </a>
                </div>
                
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="products.php" class="nav-link">
                            Products
                            <i class="fas fa-chevron-down dropdown-icon"></i>
                        </a>
                        <div class="dropdown-menu" id="products-dropdown">
                            <!-- Dynamic category links will be loaded here -->
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="contact.php" class="nav-link">Contact Us</a>
                    </li>
                </ul>

                <div class="nav-actions">
                    <div class="search-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="wishlist-icon">
                        <i class="fas fa-heart"></i>
                        <span class="wishlist-count">0</span>
                    </div>
                    <div class="user-menu">
                        <div class="user-icon" onclick="toggleUserMenu()">
                            <i class="fas fa-user"></i>
                            <span class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] ?: $currentUser['username']); ?></span>
                        </div>
                        <div class="user-dropdown" id="userDropdown">
                            <a href="profile.php" class="user-dropdown-item active">
                                <i class="fas fa-user-circle"></i> My Profile
                            </a>
                            <!-- <a href="orders.php" class="user-dropdown-item">
                                <i class="fas fa-shopping-bag"></i> My Orders
                            </a> -->
                            <a href="logout.php" class="user-dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>

                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Breadcrumb -->
    <section class="breadcrumb">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="index.php">Home</a>
                <span>/</span>
                <span>My Profile</span>
            </nav>
        </div>
    </section>

    <!-- Profile Content -->
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h1 class="profile-name">
                    <?php echo htmlspecialchars(trim($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?: $currentUser['username']); ?>
                </h1>
                <p class="profile-email"><?php echo htmlspecialchars($currentUser['email']); ?></p>
            </div>

            <div class="profile-body">
                <div class="profile-section">
                    <h2 class="section-title">Account Information</h2>
                    <div class="profile-info">
                        <div class="info-item">
                            <span class="info-label">Username</span>
                            <span class="info-value"><?php echo htmlspecialchars($currentUser['username']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email Address</span>
                            <span class="info-value"><?php echo htmlspecialchars($currentUser['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">First Name</span>
                            <span class="info-value"><?php echo htmlspecialchars($currentUser['first_name'] ?: 'Not provided'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Last Name</span>
                            <span class="info-value"><?php echo htmlspecialchars($currentUser['last_name'] ?: 'Not provided'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Phone Number</span>
                            <span class="info-value"><?php echo htmlspecialchars($currentUser['phone'] ?: 'Not provided'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Company</span>
                            <span class="info-value"><?php echo htmlspecialchars($currentUser['company'] ?: 'Not provided'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Account Status</span>
                            <span class="info-value">
                                <span class="status-badge status-active"><?php echo ucfirst($currentUser['status']); ?></span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Member Since</span>
                            <span class="info-value"><?php echo date('F j, Y', strtotime($currentUser['created_at'])); ?></span>
                        </div>
                    </div>
                </div>

                <!-- <div class="profile-actions">
                    <a href="edit-profile.php" class="btn-profile btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Profile
                    </a>
                    <a href="orders.php" class="btn-profile btn-secondary">
                        <i class="fas fa-shopping-bag"></i>
                        My Orders
                    </a>
                    <a href="change-password.php" class="btn-profile btn-secondary">
                        <i class="fas fa-lock"></i>
                        Change Password
                    </a>
                </div> -->
            </div>
        </div>
    </div>

    <!-- WhatsApp Float -->
    <div class="whatsapp-float">
        <a href="https://wa.me/918153990102" target="_blank">
            <i class="fab fa-whatsapp"></i>
            <span>WhatsApp us</span>
        </a>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <div class="bottom-nav-item" onclick="window.location.href='index.php'">
            <i class="fas fa-home"></i>
            <span>Shop</span>
        </div>
        <div class="bottom-nav-item">
            <i class="fas fa-heart"></i>
            <span>Wishlist</span>
            <span class="nav-badge">0</span>
        </div>
        <div class="bottom-nav-item">
            <i class="fas fa-search"></i>
            <span>Search</span>
        </div>
    </div>

    <script src="assets/js/scripts.js"></script>
    <script>
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');
            
            if (!userMenu.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>
