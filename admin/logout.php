<?php
require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
$auth->logout();

header('Location: ../index.php');
exit();
?>
