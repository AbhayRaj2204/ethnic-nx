<?php
require_once __DIR__ . '/../config/auth.php';

header('Content-Type: application/json');

$auth = new Auth();

if ($auth->isLoggedIn()) {
    // Extend session
    $_SESSION['expire_time'] = time() + (24 * 60 * 60);
    echo json_encode(['success' => true, 'message' => 'Session extended']);
} else {
    echo json_encode(['success' => false, 'message' => 'Session expired']);
}
?>
