<?php require_once 'check-auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail - Ethnic NX</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <nav class="breadcrumb-nav">
                <a href="index.php">Home</a>
                <span>/</span>
                <a href="products.php">Products</a>
                <span>/</span>
                <span>Loading...</span>
            </nav>
        </div>
    </section>

    <!-- Product Detail -->
    <section class="product-detail">
        <div class="container">
            <div class="product-detail-grid">
                <div class="product-images">
                    <div class="main-image">
                        <img id="main-product-image" src="/placeholder.svg?height=600&width=500" alt="Product Image">
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
                    <h1 class="product-title-detail">Loading Product...</h1>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="rating-count">(128 reviews)</span>
                    </div>
                    <p class="product-price-detail">₹0.00</p>

                    <div class="product-options">
                        <div class="size-selection">
                            <h3>Size</h3>
                            <div class="size-options">
                                <button class="size-btn">S</button>
                                <button class="size-btn active">M</button>
                                <button class="size-btn">L</button>
                                <button class="size-btn">XL</button>
                                <button class="size-btn">XXL</button>
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

                    <div class="product-actions">
                        <button class="btn btn-primary add-to-cart">
                            <i class="fas fa-shopping-cart"></i>
                            Add to Cart
                        </button>
                        <button class="btn btn-secondary wishlist-detail">
                            <i class="fas fa-heart"></i>
                            Add to Wishlist
                        </button>
                    </div>

                    <div class="product-tabs">
                        <div class="tab-buttons">
                            <button class="tab-btn active" data-tab="details">Details</button>
                            <button class="tab-btn" data-tab="shipping">Size Chart</button>
                            <button class="tab-btn" data-tab="reviews">Contact Our Team</button>
                        </div>

                        <div class="tab-content">
                            <div id="details" class="tab-pane active">
                                <ul class="product-details-list">
                                    <li><strong>Loading product details...</strong></li>
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
                                        width: 100%;
                                        border-collapse: collapse;
                                        margin: 20px 0;
                                        font-family: 'Poppins', Arial, sans-serif;
                                        background: #fff;
                                        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                                    }
                                    .size-chart-table th, .size-chart-table td {
                                        border: 1px solid #e0e0e0;
                                        padding: 12px 16px;
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
                                        font-size: 0.95em;
                                        color: #888;
                                        font-style: italic;
                                    }
                                </style>
                            </div>

                            <div id="reviews" class="tab-pane">
                                <div class="contact-support-tab">
                                    <p>Chat to our team for further assistance and queries.</p>
                                    <p>
                                        <strong>WhatsApp Us at</strong> <a href="https://wa.me/918153990102" target="_blank">+91 8153990102</a>
                                    </p>
                                    <p>Monday to Saturday - 10:00 am to 8:00 pm IST</p>
                                    <a href="https://wa.me/918153990102" target="_blank" class="btn btn-primary" style="margin-top: 12px;">
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
