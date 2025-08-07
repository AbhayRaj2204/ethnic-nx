<?php
require_once 'config/web-auth.php';

$webAuth = new WebAuth();
$webAuth->requireLogin();

// Get current user data for use in pages
$currentUser = $webAuth->getCurrentUser();
?>
