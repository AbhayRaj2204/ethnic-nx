<?php
function generateMetaTags($title, $description, $image = '', $url = '', $type = 'website') {
    // Get current URL if not provided
    if (empty($url)) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        $url = $protocol . '://' . $host . $uri;
    }
    
    // Default image if not provided
    if (empty($image)) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
        if ($basePath === '/') { $basePath = ''; }
        $image = $protocol . '://' . $host . $basePath . '/assets/images/logo.png';
    }
    
    // Ensure image URL is absolute
    if (!empty($image) && substr($image, 0, 4) !== 'http') {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
        if ($basePath === '/') { $basePath = ''; }
        $image = $protocol . '://' . $host . $basePath . '/' . ltrim($image, '/');
    }
    
    echo '<!-- SEO Meta Tags -->' . "\n";
    echo '<meta name="description" content="' . htmlspecialchars($description) . '">' . "\n";
    echo '<meta name="keywords" content="ethnic wear, traditional clothing, sherwanis, kurtas, Indian fashion, premium ethnic wear">' . "\n";
    echo '<meta name="author" content="Ethnic NX">' . "\n";
    echo "\n";
    
    echo '<!-- Open Graph Meta Tags -->' . "\n";
    echo '<meta property="og:title" content="' . htmlspecialchars($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . htmlspecialchars($description) . '">' . "\n";
    echo '<meta property="og:image" content="' . htmlspecialchars($image) . '">' . "\n";
    echo '<meta property="og:image:secure_url" content="' . htmlspecialchars($image) . '">' . "\n";
    echo '<meta property="og:url" content="' . htmlspecialchars($url) . '">' . "\n";
    echo '<meta property="og:type" content="' . htmlspecialchars($type) . '">' . "\n";
    echo '<meta property="og:site_name" content="Ethnic NX">' . "\n";
    echo "\n";
    
    echo '<!-- Twitter Card Meta Tags -->' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . htmlspecialchars($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . htmlspecialchars($description) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . htmlspecialchars($image) . '">' . "\n";
}
?>
