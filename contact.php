<?php 
require_once 'includes/meta-tags.php';

// Check if this is a crawler/bot request for meta tags
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
    <title>Contact Us - Ethnic NX</title>
    
    <?php 
    generateMetaTags(
        'Contact Us - Ethnic NX | Get in Touch for Premium Ethnic Wear',
        'Contact Ethnic NX for inquiries about our premium ethnic wear collection. Get support for orders, custom fitting, and product information.',
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
            <h1>Contact Us - Ethnic NX</h1>
            <p>Contact Ethnic NX for inquiries about our premium ethnic wear collection. Get support for orders, custom fitting, and product information.</p>
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
                        <a href="contact.php" class="nav-link active">Contact Us</a>
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
    <!-- <section class="breadcrumb">
        <div class="container">
            <nav class="breadcrumb-nav">
                <a href="index.php">Home</a>
                <span>/</span>
                <span>Contact Us</span>
            </nav>
        </div>
    </section> -->

    <!-- Contact Hero -->
    <section class="contact-hero">
        <div class="container">
            <div class="contact-hero-content">
                <h1 class="contact-title">Get in Touch</h1>
                <p class="contact-subtitle">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Visit Our Store</h3>
                        <p>123 Fashion Street<br>Mumbai, Maharashtra 400001<br>India</p>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3>Call Us</h3>
                        <p>+91 81539 90102<br>Mon-Sat: 10AM-8PM</p>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>Email Us</h3>
                        <p>info@ethnicnx.com<br>support@ethnicnx.com</p>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <h3>WhatsApp</h3>
                        <p>+91 81539 90102<br>Quick Support</p>
                    </div>
                </div>

                <div class="contact-form-container">
                    <form class="contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" id="firstName" name="firstName" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" id="lastName" name="lastName" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="order">Order Support</option>
                                <option value="product">Product Information</option>
                                <option value="custom">Custom Order</option>
                                <option value="complaint">Complaint</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" required placeholder="Tell us how we can help you..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Frequently Asked Questions</h2>
            </div>

            <div class="faq-grid">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What is your return policy?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We offer a 30-day return policy for all unworn items with original tags. Custom-made items are non-returnable unless there's a manufacturing defect.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Do you offer custom tailoring?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, we provide custom tailoring services. Please visit our store for measurements or contact us to arrange a consultation.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What are your delivery charges?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We offer free delivery for orders above ₹5,000. For orders below this amount, delivery charges are ₹200 within India.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How long does delivery take?</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Standard delivery takes 5-7 business days. Express delivery (2-3 days) is available for an additional charge. Custom orders may take 2-3 weeks.</p>
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

        // FAQ Accordion functionality
        document.querySelectorAll('.faq-question').forEach(item => {
            item.addEventListener('click', event => {
                const faqItem = item.closest('.faq-item');
                const answer = faqItem.querySelector('.faq-answer');
                const icon = item.querySelector('i');

                // Toggle active class on question and icon
                item.classList.toggle('active');
                icon.classList.toggle('active');

                // Toggle max-height for smooth animation
                if (answer.classList.contains('active')) {
                    answer.classList.remove('active');
                    answer.style.maxHeight = null;
                } else {
                    answer.classList.add('active');
                    answer.style.maxHeight = answer.scrollHeight + "px";
                }
            });
        });
    </script>
</body>
</html>
