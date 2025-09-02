<?php 
require_once 'includes/meta-tags.php';
require_once 'models/Banner.php';

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$isCrawler = preg_match('/bot|crawler|spider|facebook|twitter|linkedin|whatsapp/i', $userAgent);

// Only require login for actual user interactions, not for crawlers
if (!$isCrawler) {
    require_once 'check-auth.php';
} else {
    // For crawlers, set minimal user data to prevent errors
    $currentUser = ['first_name' => '', 'username' => 'Guest', 'email' => '', 'phone' => ''];
}

$bannerModel = new Banner();
$activeBanners = $bannerModel->getActive();

echo "<!-- Debug: Active banners count: " . count($activeBanners) . " -->";
if (!empty($activeBanners)) {
    echo "<!-- Debug: First banner: " . htmlspecialchars(json_encode($activeBanners[0])) . " -->";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ethnic NX - Premium Traditional Wear</title>
    
    <?php 
    generateMetaTags(
        'Ethnic NX - Premium Traditional Wear | Sherwanis, Kurtas & Ethnic Fashion',
        'Discover premium ethnic wear at Ethnic NX. Shop exquisite sherwanis, designer kurtas, and traditional Indian clothing with custom fitting and all India delivery.',
        '/ethnic-nx/assets/images/logo.png'
    );
    ?>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Added banner slider CSS file -->
    <link rel="stylesheet" href="assets/css/banner-slider.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Inline Banner Slider CSS -->
    
</head>
<body>
    <?php if ($isCrawler): ?>
        <!-- Minimal content for crawlers to read meta tags -->
        <div style="display: none;">
            <h1>Ethnic NX - Premium Traditional Wear</h1>
            <p>Discover premium ethnic wear at Ethnic NX. Shop exquisite sherwanis, designer kurtas, and traditional Indian clothing with custom fitting and all India delivery.</p>
        </div>
    <?php endif; ?>
    
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

    <!-- Banner Slider Carousel -->
    <?php if (!empty($activeBanners)): ?>
        <section class="banner-slider">
            <div class="banner-container">
                <div class="banner-wrapper">
                    <?php foreach ($activeBanners as $index => $banner): ?>
                        <div class="banner-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                            <?php 
                            // Clean up the image path
                            $imagePath = $banner['image'];
                            // Ensure the path starts with a forward slash
                            if (!str_starts_with($imagePath, '/') && !str_starts_with($imagePath, 'http')) {
                                $imagePath = '/' . ltrim($imagePath, '/');
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                 alt="Banner Image <?php echo $index + 1; ?>" 
                                 class="banner-image"
                                 loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                                 onerror="console.error('Failed to load banner image:', this.src)">
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($activeBanners) > 1): ?>
                    <!-- Navigation Arrows -->
                    <button class="banner-nav banner-prev" onclick="changeBannerSlide(-1)" aria-label="Previous banner">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="banner-nav banner-next" onclick="changeBannerSlide(1)" aria-label="Next banner">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    
                    <!-- Dots Indicators -->
                    <div class="banner-dots">
                        <?php foreach ($activeBanners as $index => $banner): ?>
                            <span class="banner-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                                  onclick="currentBannerSlide(<?php echo $index + 1; ?>)"
                                  aria-label="Go to banner <?php echo $index + 1; ?>"></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php else: ?>
        <section class="no-banners">
            <div class="content">
                <h3><i class="fas fa-images"></i> No Banners Available</h3>
                <p>Please add some banners from the admin panel to see them here.</p>
            </div>
        </section>
    <?php endif; ?>

    <!-- Hero Carousel -->
    <!-- <section class="hero-carousel">
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
                        <div class="model-card" style="display:flex;align-items:center;justify-content:center;">
                            <img src="assets/images/logo.png" alt="Ethnic NX Logo" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        <div class="model-card" style="display:flex;align-items:center;justify-content:center;">
                            <img src="assets/images/logo.png" alt="Ethnic NX Logo" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        <div class="model-card" style="display:flex;align-items:center;justify-content:center;">
                            <img src="assets/images/logo.png" alt="Ethnic NX Logo" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        <div class="model-card" style="display:flex;align-items:center;justify-content:center;">
                            <img src="assets/images/logo.png" alt="Ethnic NX Logo" style="width:100%;height:100%;object-fit:cover;">
                        </div>
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
        <!-- Added wishlist-bottom-nav class for proper modal functionality -->
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

        // Banner slider functionality with swipe support
        let bannerSlideIndex = 0;
        let bannerSlides = [];
        let bannerDots = [];
        let autoSlideInterval;
        const totalBanners = <?php echo count($activeBanners); ?>;

        let isDown = false;
        let startX = 0;
        let startY = 0;
        let currentX = 0;
        let currentY = 0;
        let bannerContainer = null;
        const swipeThreshold = 50; // Minimum distance for swipe
        let userInteracting = false;

        console.log('[Banner Debug] Total banners available:', totalBanners);

        function initBannerSlider() {
            console.log('[Banner Debug] Initializing banner slider...');
            
            bannerSlides = document.querySelectorAll('.banner-slide');
            bannerDots = document.querySelectorAll('.banner-dot');
            bannerContainer = document.querySelector('.banner-container');
            
            console.log('[Banner Debug] Found', bannerSlides.length, 'slides and', bannerDots.length, 'dots');
            
            if (bannerSlides.length === 0) {
                console.log('[Banner Debug] No banner slides found - skipping initialization');
                return;
            }
            
            // Ensure first slide is visible
            if (bannerSlides.length > 0) {
                showBannerSlide(0);
                console.log('[Banner Debug] First slide activated');
            }
            
            if (bannerContainer && bannerSlides.length > 1) {
                initSwipeEvents();
                console.log('[Banner Debug] Swipe events initialized');
            }
            
            // Start auto-slide only if we have multiple banners
            if (bannerSlides.length > 1) {
                startAutoSlide();
                console.log('[Banner Debug] Auto-slide started for', bannerSlides.length, 'slides');
            } else {
                console.log('[Banner Debug] Only one slide - auto-slide disabled');
            }
        }

        function initSwipeEvents() {
            if (!bannerContainer) return;

            // Touch events for mobile
            bannerContainer.addEventListener('touchstart', handleTouchStart, { passive: false });
            bannerContainer.addEventListener('touchmove', handleTouchMove, { passive: false });
            bannerContainer.addEventListener('touchend', handleTouchEnd, { passive: false });

            // Mouse events for desktop
            bannerContainer.addEventListener('mousedown', handleMouseDown);
            bannerContainer.addEventListener('mousemove', handleMouseMove);
            bannerContainer.addEventListener('mouseup', handleMouseUp);
            bannerContainer.addEventListener('mouseleave', handleMouseUp);

            // Prevent context menu on long press
            bannerContainer.addEventListener('contextmenu', (e) => {
                if (userInteracting) {
                    e.preventDefault();
                }
            });
        }

        function handleTouchStart(e) {
            if (bannerSlides.length <= 1) return;
            
            userInteracting = true;
            isDown = true;
            const touch = e.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
            
            // Pause auto-slide during interaction
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
            
            console.log('[Banner Debug] Touch start at:', startX, startY);
        }

        function handleTouchMove(e) {
            if (!isDown || bannerSlides.length <= 1) return;
            
            e.preventDefault(); // Prevent scrolling
            const touch = e.touches[0];
            currentX = touch.clientX;
            currentY = touch.clientY;
        }

        function handleTouchEnd(e) {
            if (!isDown || bannerSlides.length <= 1) return;
            
            isDown = false;
            userInteracting = false;
            
            const deltaX = currentX - startX;
            const deltaY = Math.abs(currentY - startY);
            
            console.log('[Banner Debug] Touch end - deltaX:', deltaX, 'deltaY:', deltaY);
            
            // Check if it's a horizontal swipe (not vertical scroll)
            if (Math.abs(deltaX) > swipeThreshold && deltaY < 100) {
                if (deltaX > 0) {
                    // Swipe right - go to previous slide
                    changeBannerSlide(-1);
                    console.log('[Banner Debug] Swiped right - previous slide');
                } else {
                    // Swipe left - go to next slide
                    changeBannerSlide(1);
                    console.log('[Banner Debug] Swiped left - next slide');
                }
            }
            
            // Resume auto-slide after interaction
            setTimeout(() => {
                if (bannerSlides.length > 1) {
                    startAutoSlide();
                }
            }, 1000);
        }

        function handleMouseDown(e) {
            if (bannerSlides.length <= 1) return;
            
            userInteracting = true;
            isDown = true;
            startX = e.clientX;
            startY = e.clientY;
            
            // Change cursor to grabbing
            bannerContainer.style.cursor = 'grabbing';
            
            // Pause auto-slide during interaction
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
            
            console.log('[Banner Debug] Mouse down at:', startX, startY);
        }

        function handleMouseMove(e) {
            if (!isDown || bannerSlides.length <= 1) return;
            
            e.preventDefault();
            currentX = e.clientX;
            currentY = e.clientY;
        }

        function handleMouseUp(e) {
            if (!isDown || bannerSlides.length <= 1) return;
            
            isDown = false;
            userInteracting = false;
            
            // Reset cursor
            bannerContainer.style.cursor = 'grab';
            
            const deltaX = currentX - startX;
            const deltaY = Math.abs(currentY - startY);
            
            console.log('[Banner Debug] Mouse up - deltaX:', deltaX, 'deltaY:', deltaY);
            
            // Check if it's a horizontal drag
            if (Math.abs(deltaX) > swipeThreshold && deltaY < 100) {
                if (deltaX > 0) {
                    // Drag right - go to previous slide
                    changeBannerSlide(-1);
                    console.log('[Banner Debug] Dragged right - previous slide');
                } else {
                    // Drag left - go to next slide
                    changeBannerSlide(1);
                    console.log('[Banner Debug] Dragged left - next slide');
                }
            }
            
            // Resume auto-slide after interaction
            setTimeout(() => {
                if (bannerSlides.length > 1) {
                    startAutoSlide();
                }
            }, 1000);
        }

        function showBannerSlide(index) {
            console.log('[Banner Debug] Showing slide', index);
            
            if (bannerSlides.length === 0) return;
            
            // Remove active class from all slides and dots
            bannerSlides.forEach((slide, i) => {
                slide.classList.remove('active');
            });
            
            bannerDots.forEach((dot, i) => {
                dot.classList.remove('active');
            });
            
            // Add active class to current slide and dot
            if (bannerSlides[index]) {
                bannerSlides[index].classList.add('active');
                console.log('[Banner Debug] Slide', index, 'activated');
            }
            
            if (bannerDots[index]) {
                bannerDots[index].classList.add('active');
                console.log('[Banner Debug] Dot', index, 'activated');
            }
        }

        function changeBannerSlide(direction) {
            if (bannerSlides.length <= 1) {
                console.log('[Banner Debug] Cannot change slide - only', bannerSlides.length, 'slide(s)');
                return;
            }
            
            console.log('[Banner Debug] Changing slide by', direction);
            
            bannerSlideIndex += direction;
            
            if (bannerSlideIndex >= bannerSlides.length) {
                bannerSlideIndex = 0;
            } else if (bannerSlideIndex < 0) {
                bannerSlideIndex = bannerSlides.length - 1;
            }
            
            console.log('[Banner Debug] New slide index:', bannerSlideIndex);
            showBannerSlide(bannerSlideIndex);
            
            if (autoSlideInterval && !userInteracting) {
                clearInterval(autoSlideInterval);
                startAutoSlide();
            }
        }

        function currentBannerSlide(slideNumber) {
            if (bannerSlides.length <= 1) return;
            
            console.log('[Banner Debug] Going to slide number:', slideNumber);
            bannerSlideIndex = slideNumber - 1;
            showBannerSlide(bannerSlideIndex);
            
            // Restart auto-slide timer
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
                startAutoSlide();
            }
        }

        function startAutoSlide() {
            if (bannerSlides.length <= 1) return;
            
            console.log('[Banner Debug] Starting auto-slide with 5-second interval');
            autoSlideInterval = setInterval(() => {
                if (!userInteracting) {
                    changeBannerSlide(1);
                }
            }, 5000);
        }

        // Initialize banner slider when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[Banner Debug] DOM Content Loaded - initializing banner slider');
            setTimeout(initBannerSlider, 100); // Small delay to ensure all elements are rendered
        });

        // Fallback initialization
        if (document.readyState !== 'loading') {
            console.log('[Banner Debug] Document already loaded - initializing immediately');
            setTimeout(initBannerSlider, 100);
        }

        // Add error handling for banner images
        document.addEventListener('DOMContentLoaded', function() {
            const bannerImages = document.querySelectorAll('.banner-image');
            bannerImages.forEach((img, index) => {
                img.addEventListener('load', function() {
                    console.log('[Banner Debug] Image', index, 'loaded successfully:', this.src);
                });
                
                img.addEventListener('error', function() {
                    console.error('[Banner Debug] Failed to load image', index, ':', this.src);
                    // You could add a fallback image here if needed
                });
            });
        });
    </script>
</body>
</html>
