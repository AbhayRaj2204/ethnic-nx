<?php 
require_once 'includes/meta-tags.php';

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$isCrawler = preg_match('/bot|crawler|spider|facebook|twitter|linkedin|whatsapp/i', $userAgent);

// Only require login for actual user interactions, not for crawlers
if (!$isCrawler) {
    require_once 'check-auth.php';
} else {
    // For crawlers, set minimal user data to prevent errors
    $currentUser = ['first_name' => '', 'username' => 'Guest', 'email' => '', 'phone' => ''];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Ethnic NX</title>
    
    <?php 
    generateMetaTags(
        'Premium Ethnic Wear Collection | Products - Ethnic NX',
        'Browse our premium collection of traditional ethnic wear. Find the perfect sherwani, kurta, or ethnic outfit with custom fitting and quality assurance.',
        '/ethnic-nx/assets/images/logo.png'
    );
    ?>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php if ($isCrawler): ?>
        <!-- Minimal content for crawlers to read meta tags -->
        <div style="display: none;">
            <h1>Premium Ethnic Wear Collection - Ethnic NX</h1>
            <p>Browse our premium collection of traditional ethnic wear. Find the perfect sherwani, kurta, or ethnic outfit with custom fitting and quality assurance.</p>
        </div>
    <?php endif; ?>
    
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
                        <a href="products.php" class="nav-link active">
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
                            <a href="profile.php" class="user-dropdown-item">
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
            <!-- <nav class="breadcrumb-nav">
                <a href="index.php">Home</a>
                <span>/</span>
                <span id="breadcrumb-category">Products</span>
            </nav> -->
        </div>
    </section>

    <!-- Products Page -->
    <section class="products-page">
        <div class="container">
            <div class="products-header">
                <h1 class="page-title" id="page-title">Our Premium Collection</h1>
                
                <div class="products-controls">
                    <div class="sort-control">
                        <label for="sort">Sort by:</label>
                        <select class="sort-select" id="sort">
                            <option value="default">Default</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="newest">Newest First</option>
                            <option value="popular">Most Popular</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="products-content">
                <div class="products-sidebar">
                    <div class="filter-section">
                        <h3 class="filter-title">Categories</h3>
                        <div class="category-filter">
                            <button class="category-filter-btn active" data-category="all">All Products</button>
                            <!-- Dynamic category buttons will be loaded here -->
                        </div>
                    </div>
                </div>

                <div class="products-main">
                    <div class="loading-products" style="display: none;">
                        <div class="loading-spinner"></div>
                        <p>Loading products...</p>
                    </div>

                    <div class="no-products" style="display: none;">
                        <div class="no-products-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>No products found</h3>
                        <p>Try adjusting your filters or search terms</p>
                        <button class="btn btn-primary" onclick="showAllProducts()">Show All Products</button>
                    </div>

                    <div class="products-grid-page">
                        <!-- Products will be loaded dynamically -->
                    </div>

                    <div class="load-more" style="display: none;">
                        <button class="btn btn-secondary">Load More Products</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- WhatsApp Float -->
    <div class="whatsapp-float">
        <a href="https://wa.me/9023386059" target="_blank">
            <i class="fab fa-whatsapp"></i>
            <span class="whatsapp-text">WhatsApp us</span>
        </a>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <div class="bottom-nav-item" onclick="window.location.href='index.php'">
            <i class="fas fa-home"></i>
            <span>Shop</span>
        </div>
         <div class="bottom-nav-item wishlist-bottom-nav">
            <i class="fas fa-heart"></i>
            <span>Wishlist</span>
            <span class="nav-badge">0</span>
        </div>
        <div class="bottom-nav-item">
            <i class="fas fa-search"></i>
            <span>Search</span>
        </div>
    </div>

    <!-- Product Zoom Modal -->
    <div id="zoom-modal" class="zoom-modal">
        <div class="zoom-modal-content">
            <span class="zoom-close">&times;</span>
            <img id="zoom-image" src="/placeholder.svg" alt="Product Image">
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
