<?php require_once 'check-auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ethnic NX - Premium Traditional Wear</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="loader">
            <div class="logo-loader">
                <img src="assets/images/logo.png" alt="Ethnic NX" class="loading-logo">
            </div>
            <div class="loading-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <p class="loading-text">Ethnic NX</p>
        </div>
    </div>

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
                        <a href="index.php" class="nav-link active">Home</a>
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

    <!-- Hero Carousel -->
    <section class="hero-carousel">
        <div class="carousel-container">
            <div class="carousel-slide active">
                <div class="slide-bg" style="background: linear-gradient(135deg, #e53e3e 0%, #dc2626 100%);">
                    <div class="decorative-arch arch-1"></div>
                    <div class="decorative-arch arch-2"></div>
                    <div class="decorative-arch arch-3"></div>
                </div>
                <div class="slide-content">
                    <div class="slide-text">
                        <h1 class="slide-title">Welcome <?php echo htmlspecialchars($currentUser['first_name'] ?: $currentUser['username']); ?>!</h1>
                        <h2 class="slide-subtitle-main">Royal Collection</h2>
                        <p class="slide-subtitle">Exquisite Sherwanis & Premium Ethnic Wear</p>
                        <a href="products.php" class="btn btn-primary">Explore Collection</a>
                    </div>
                    <div class="slide-models">
                        <div class="model-card model-1"></div>
                        <div class="model-card model-2"></div>
                        <div class="model-card model-3"></div>
                        <div class="model-card model-4"></div>
                    </div>
                </div>
            </div>

            <div class="carousel-slide">
                <div class="slide-bg" style="background: linear-gradient(135deg, #f6ad55 0%, #f59e0b 100%);">
                    <div class="decorative-arch arch-1"></div>
                    <div class="decorative-arch arch-2"></div>
                    <div class="decorative-arch arch-3"></div>
                </div>
                <div class="slide-content">
                    <div class="slide-text">
                        <h1 class="slide-title">Designer Kurtas</h1>
                        <p class="slide-subtitle">Contemporary Ethnic Elegance</p>
                        <a href="products.php?category=kurta" class="btn btn-primary">Shop Now</a>
                    </div>
                    <div class="slide-models">
                        <div class="model-card model-5"></div>
                        <div class="model-card model-6"></div>
                        <div class="model-card model-7"></div>
                        <div class="model-card model-8"></div>
                    </div>
                </div>
            </div>

            <div class="carousel-slide">
                <div class="slide-bg" style="background: linear-gradient(135deg, #e53e3e 0%, #f6ad55 100%);">
                    <div class="decorative-arch arch-1"></div>
                    <div class="decorative-arch arch-2"></div>
                    <div class="decorative-arch arch-3"></div>
                </div>
                <div class="slide-content">
                    <div class="slide-text">
                        <h1 class="slide-title">Wedding Special</h1>
                        <p class="slide-subtitle">Premium Bridal Collection</p>
                        <a href="products.php?category=sherwani" class="btn btn-primary">View Collection</a>
                    </div>
                    <div class="slide-models">
                        <div class="model-card model-9"></div>
                        <div class="model-card model-10"></div>
                        <div class="model-card model-11"></div>
                        <div class="model-card model-12"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="carousel-controls">
            <button class="carousel-btn prev-btn"><i class="fas fa-chevron-left"></i></button>
            <button class="carousel-btn next-btn"><i class="fas fa-chevron-right"></i></button>
        </div>

        <div class="carousel-indicators">
            <span class="indicator active" data-slide="0"></span>
            <span class="indicator" data-slide="1"></span>
            <span class="indicator" data-slide="2"></span>
        </div>
    </section>

    <!-- Categories Section -->
    <!-- <section class="categories">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="title-decoration"></span>
                    CATALOGUE
                    <span class="title-decoration"></span>
                </h2>
            </div>

            <div class="categories-grid" id="categories-grid"> -->
                <!-- Categories will be loaded dynamically -->
                <!-- <div class="category-loading">
                    <div class="loading-spinner"></div>
                    <p>Loading categories...</p>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Products Section -->
    <section class="products">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="title-decoration"></span>
                    NEW ARRIVALS
                    <span class="title-decoration"></span>
                </h2>
            </div>

            <div class="products-grid">
                <!-- Products will be loaded dynamically from admin panel -->
                <div class="products-loading">
                    <div class="loading-spinner"></div>
                    <p>Loading products...</p>
                </div>
            </div>

            <div class="products-cta">
                <a href="products.php" class="btn btn-secondary">
                    <span>More Products</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3 class="feature-title">ASSURED QUALITY</h3>
                    <p class="feature-description">Premium fabrics and meticulous craftsmanship</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-ruler"></i>
                    </div>
                    <h3 class="feature-title">CUSTOM FITTING</h3>
                    <p class="feature-description">Personalized tailoring for perfect fit</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 class="feature-title">ALL INDIA DELIVERY</h3>
                    <p class="feature-description">Fast and reliable delivery nationwide</p>
                </div>
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
