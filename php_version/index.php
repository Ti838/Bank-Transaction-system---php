<?php
require_once 'includes/functions.php';

$data = [
    'is_authenticated' => is_logged_in(),
    'user_name' => $_SESSION['full_name'] ?? '',
    'user_role' => $_SESSION['role'] ?? '',
    'page_title' => 'Trust Mora Bank - Secure Digital Banking'
];

render('root/index', $data);
?>