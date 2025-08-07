<?php
require_once 'config/web-auth.php';

$webAuth = new WebAuth();
$webAuth->logout();

header('Location: login.php');
exit();
?>
