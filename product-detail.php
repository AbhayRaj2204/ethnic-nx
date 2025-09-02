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

$productId = $_GET['id'] ?? null;
$productData = null;
$metaTitle = 'Product Detail - Ethnic NX';
$metaDescription = 'View detailed information about our premium ethnic wear products with high-quality images and specifications.';

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
if ($basePath === '/') { $basePath = ''; }

$metaImage = $protocol . '://' . $host . $basePath . '/assets/images/logo.png';

if ($productId) {
    try {
        require_once 'config/database.php';
        $db = new CSVDatabase();
        $products = $db->read('products.csv');
        
        foreach ($products as $product) {
            if ($product['id'] == $productId && $product['status'] === 'active') {
                $productData = $product;
                break;
            }
        }
        
        if ($productData) {
            $metaTitle = $productData['name'] . ' - Ethnic NX';
            $metaDescription = 'Shop ' . $productData['name'] . ' - Premium quality ethnic wear. ' . ($productData['description'] ? substr($productData['description'], 0, 120) . '...' : 'Available in multiple sizes with fast delivery.');
            
            if (!empty($productData['images'])) {
                $imageUrl = $productData['images'];
                if (!preg_match('/^https?:\\/\\//', $imageUrl)) {
                    $imageUrl = $protocol . '://' . $host . $basePath . '/' . ltrim($imageUrl, '/');
                }
                $metaImage = $imageUrl;
            }
        }
    } catch (Exception $e) {
        error_log("Error fetching product for meta tags: " . $e->getMessage());
    }
}

$currentUrl = $protocol . '://' . $host . ($_SERVER['REQUEST_URI'] ?? '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($metaTitle); ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="keywords" content="ethnic wear, traditional clothing, sherwanis, kurtas, Indian fashion, premium ethnic wear">
    <meta name="author" content="Ethnic NX">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($metaTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($metaImage); ?>">
    <meta property="og:image:secure_url" content="<?php echo htmlspecialchars($metaImage); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($currentUrl); ?>">
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="Ethnic NX">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($metaTitle); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($metaImage); ?>">
    
    <!-- WhatsApp specific meta tags -->
    <meta property="og:image:alt" content="<?php echo htmlspecialchars($productData['name'] ?? 'Ethnic NX Product'); ?>">
    
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
            <h1><?php echo htmlspecialchars($metaTitle); ?></h1>
            <p><?php echo htmlspecialchars($metaDescription); ?></p>
            <?php if ($productData && !empty($productData['images'])): ?>
                <img src="<?php echo htmlspecialchars($metaImage); ?>" alt="<?php echo htmlspecialchars($productData['name']); ?>">
            <?php endif; ?>
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
                            <a href="profile.php" class="user-dropdown-item">
                                <i class="fas fa-user-circle"></i> My Profile
                            </a>
                            <a href="orders.php" class="user-dropdown-item">
                                <i class="fas fa-shopping-bag"></i> My Orders
                            </a>
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
                <a href="products.php">Products</a>
                <span>/</span>
                <span>Loading...</span>
            </nav> -->
        </div>
    </section>

    <!-- Product Detail -->
    <section class="product-detail">
        <div class="container">
            <div class="product-detail-grid">
                <div class="product-images">
                    <div class="main-image">
                        <img id="main-product-image" src="<?php echo htmlspecialchars($productData['images'] ?? '/placeholder.svg?height=600&width=500'); ?>" alt="Product Image">
                        <!-- Added zoom result container for side-by-side zoom effect -->
                        <div class="zoom-result" id="zoom-result">
                            <img id="zoom-result-img" src="<?php echo htmlspecialchars($productData['images'] ?? '/placeholder.svg?height=600&width=500'); ?>" alt="Zoomed Product Image">
                        </div>
                        <button class="zoom-btn-detail">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    </div>
                    <!-- <div class="thumbnail-images">
                        <img src="/placeholder.svg?height=100&width=80" alt="Thumbnail 1" class="thumbnail active">
                        <img src="/placeholder.svg?height=100&width=80" alt="Thumbnail 2" class="thumbnail">
                        <img src="/placeholder.svg?height=100&width=80" alt="Thumbnail 3" class="thumbnail">
                        <img src="/placeholder.svg?height=100&width=80" alt="Thumbnail 4" class="thumbnail">
                    </div> -->
                </div>

                <div class="product-info">
                    <h1 class="product-title-detail"><?php echo htmlspecialchars($productData['name'] ?? 'Loading Product...'); ?></h1>
                    <!-- <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="rating-count">(128 reviews)</span>
                    </div> -->
                    <p class="product-price-detail"><?php echo htmlspecialchars($productData['price'] ?? '₹0.00'); ?></p>

                    <div class="product-options">
                        <div class="size-selection">
                            <h3>Size</h3>
                            <div class="size-options">
                                <button class="size-btn <?php echo $productData['size'] === 'S' ? 'active' : ''; ?>">S</button>
                                <button class="size-btn <?php echo $productData['size'] === 'M' ? 'active' : ''; ?>">M</button>
                                <button class="size-btn <?php echo $productData['size'] === 'L' ? 'active' : ''; ?>">L</button>
                                <button class="size-btn <?php echo $productData['size'] === 'XL' ? 'active' : ''; ?>">XL</button>
                                <button class="size-btn <?php echo $productData['size'] === 'XXL' ? 'active' : ''; ?>">XXL</button>
                            </div>
                        </div>

                        <div class="quantity-selection">
                            <h3>Quantity</h3>
                            <div class="quantity-controls">
                                <button class="qty-btn minus">-</button>
                                <input type="number" class="qty-input" value="1" min="1">
                                <button class="qty-btn plus">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="product-actions" id="productActions">
                        <button class="btn btn-secondary wishlist-detail ">
                            <i class="fas fa-heart"></i>
                            Add to Wishlist
                        </button>
                        <!-- Added WhatsApp enquiry button for product detail page -->
                        <button class="btn btn-primary enquire-detail-btn" id="enquireDetailBtn">
                            <i class="fab fa-whatsapp"></i>
                            Enquire Now
                        </button>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var mainImage = document.getElementById('main-product-image');
                            var actions = document.getElementById('productActions');
                            if (mainImage && actions) {
                                mainImage.addEventListener('mouseenter', function() {
                                    actions.style.display = 'none';
                                });
                                mainImage.addEventListener('mouseleave', function() {
                                    actions.style.display = '';
                                });
                            }
                        });
                    </script>

                    <div class="product-tabs">
                        <div class="tab-buttons">
                            <button class="tab-btn active" data-tab="details">Details</button>
                            <button class="tab-btn" data-tab="shipping">Size Chart</button>
                            <button class="tab-btn" data-tab="reviews">Contact Our Team</button>
                        </div>

                        <div class="tab-content">
                            <div id="details" class="tab-pane active">
                                <ul class="product-details-list">
                                    <li><strong><?php echo htmlspecialchars($productData['description'] ?? 'Loading product details...'); ?></strong></li>
                                </ul>
                            </div>

                            <div id="shipping" class="tab-pane">
                                <h3>Size Chart</h3>
                                <table class="size-chart-table">
                                    <thead>
                                        <tr>
                                            <th>Size</th>
                                            <th>Bust (inches)</th>
                                            <th>Waist (inches)</th>
                                            <th>Hip (inches)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>S</td>
                                            <td>34</td>
                                            <td>28</td>
                                            <td>36</td>
                                        </tr>
                                        <tr>
                                            <td>M</td>
                                            <td>36</td>
                                            <td>30</td>
                                            <td>38</td>
                                        </tr>
                                        <tr>
                                            <td>L</td>
                                            <td>38</td>
                                            <td>32</td>
                                            <td>40</td>
                                        </tr>
                                        <tr>
                                            <td>XL</td>
                                            <td>40</td>
                                            <td>34</td>
                                            <td>42</td>
                                        </tr>
                                        <tr>
                                            <td>XXL</td>
                                            <td>42</td>
                                            <td>36</td>
                                            <td>44</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p class="size-chart-note">* All measurements are in inches. Please refer to the size chart before placing your order.</p>
                                <style>
                                    .size-chart-table {
                                        width: 80%;
                                        max-width: 400px;
                                        border-collapse: collapse;
                                        margin: 20px auto;
                                        font-family: 'Poppins', Arial, sans-serif;
                                        background: #fff;
                                        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                                        font-size: 0.95em;
                                    }
                                    .size-chart-table th, .size-chart-table td {
                                        border: 1px solid #e0e0e0;
                                        padding: 8px 10px;
                                        text-align: center;
                                    }
                                    .size-chart-table th {
                                        background: #f7f7f7;
                                        color: #333;
                                        font-weight: 600;
                                        letter-spacing: 1px;
                                    }
                                    .size-chart-table tr:nth-child(even) {
                                        background: #fafafa;
                                    }
                                    .size-chart-table tr:hover {
                                        background: #f0f8ff;
                                    }
                                    .size-chart-note {
                                        margin-top: 12px;
                                        font-size: 0.92em;
                                        color: #888;
                                        font-style: italic;
                                    }
                                </style>
                            </div>

                            <div id="reviews" class="tab-pane">
                                <div class="contact-support-tab">
                                    <p>Chat to our team for further assistance and queries.</p>
                                    <p>
                                        <strong>WhatsApp Us at</strong> <a href="https://wa.me/919023386059" target="_blank">+91 9023386059</a>
                                    </p>
                                    <p>Monday to Saturday - 10:00 am to 8:00 pm IST</p>
                                    <a href="https://wa.me/919023386059" target="_blank" class="btn btn-primary" style="margin-top: 12px;">
                                        <i class="fab fa-whatsapp"></i> Contact on WhatsApp
                                    </a>
                                </div>
                                <style>
                                    .contact-support-tab {
                                        background: #f7fdf9;
                                        border: 1px solid #d4f3e3;
                                        border-radius: 8px;
                                        padding: 24px 20px;
                                        margin: 24px 0 0 0;
                                        text-align: center;
                                        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
                                    }
                                    .contact-support-tab p {
                                        margin: 0 0 10px 0;
                                        color: #222;
                                        font-size: 1.05em;
                                    }
                                    .contact-support-tab a.btn-primary {
                                        background: #25d366;
                                        border: none;
                                        color: #fff;
                                        padding: 10px 22px;
                                        border-radius: 5px;
                                        font-size: 1em;
                                        font-weight: 500;
                                        text-decoration: none;
                                        display: inline-block;
                                        transition: background 0.2s;
                                    }
                                    .contact-support-tab a.btn-primary:hover {
                                        background: #1ebe57;
                                    }
                                    .contact-support-tab .fa-whatsapp {
                                        margin-right: 8px;
                                        font-size: 1.2em;
                                        vertical-align: middle;
                                    }
                                    .contact-support-tab a {
                                        color: #25d366;
                                        text-decoration: underline;
                                        font-weight: 500;
                                    }
                                    .contact-support-tab a:hover {
                                        color: #1ebe57;
                                    }
                                </style>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <section class="related-products">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Related Products</h2>
            </div>
            <div class="related-products-grid">
                <!-- Related products will be loaded dynamically -->
            </div>
        </div>
    </section>

    <!-- WhatsApp Float -->
    <div class="whatsapp-float">
        <a href="https://wa.me/919023386059" target="_blank">
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
            <img id="zoom-image" src="<?php echo htmlspecialchars($productData['images'] ?? '/placeholder.svg'); ?>" alt="Product Image">
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

        document.addEventListener('DOMContentLoaded', function() {
            const enquireBtn = document.getElementById('enquireDetailBtn');
            if (enquireBtn) {
                enquireBtn.addEventListener('click', function() {
                    const productTitle = document.querySelector('.product-title-detail');
                    const productImage = document.getElementById('main-product-image');
                    const activeSizeBtn = document.querySelector('.size-btn.active');
                    
                    const productName = productTitle ? productTitle.textContent.trim() : 'Product';
                    const selectedSize = activeSizeBtn ? activeSizeBtn.textContent.trim() : 'M';
                    const currentPageUrl = window.location.href.split('#')[0];

                    // Get absolute image URL
                    let productImageUrl = '';
                    if (productImage && productImage.src && !productImage.src.includes('placeholder')) {
                        productImageUrl = productImage.src.startsWith('http')
                            ? productImage.src
                            : window.location.origin + (productImage.src.startsWith('/') ? '' : '/') + productImage.src;
                    }
                    
                    let message = currentPageUrl + '\n\n';
                    message += 'Hi! I\'m interested in this product:\n';
                    message += '📦 Product: ' + productName + '\n';
                    message += '📏 Size: ' + selectedSize + '\n';
                    if (productImageUrl) {
                        message += '🖼️ Product Image: ' + productImageUrl + '\n';
                    }
                    message += '\nPlease provide more details and pricing information. Thank you!';
                    
                    const encodedMessage = encodeURIComponent(message);
                    const whatsappUrl = 'https://wa.me/919023386059?text=' + encodedMessage;
                    
                    window.open(whatsappUrl, '_blank');
                });
            }

            const productTitle = document.querySelector('.product-title-detail');
            const productImage = document.getElementById('main-product-image');
            
            if (productTitle && productImage && !productImage.src.includes('placeholder')) {
                const title = productTitle.textContent.trim() + ' - Ethnic NX';
                const imageUrl = productImage.src.startsWith('http') ? productImage.src : window.location.origin + productImage.src;
                
                // Update meta tags dynamically
                updateMetaTag('og:title', title);
                updateMetaTag('twitter:title', title);
                updateMetaTag('og:image', imageUrl);
                updateMetaTag('twitter:image', imageUrl);
                updateMetaTag('og:url', window.location.href);
            }

            console.log('[v0] Product detail page loaded successfully');
        });

        function updateMetaTag(property, content) {
            let metaTag = document.querySelector(`meta[property="${property}"]`) || 
                         document.querySelector(`meta[name="${property}"]`);
            
            if (metaTag) {
                metaTag.setAttribute('content', content);
            } else {
                metaTag = document.createElement('meta');
                if (property.startsWith('og:') || property.startsWith('twitter:')) {
                    metaTag.setAttribute('property', property);
                } else {
                    metaTag.setAttribute('name', property);
                }
                metaTag.setAttribute('content', content);
                document.head.appendChild(metaTag);
            }
        }
    </script>
</body>
</html>
