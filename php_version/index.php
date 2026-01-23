<?php
require_once 'includes/functions.php';

if (is_logged_in()) {
    $role = $_SESSION['role'];
    if ($role === 'Admin')
        redirect('admin/dashboard.php');
    if ($role === 'Staff')
        redirect('staff/dashboard.php');
    redirect('customer/dashboard.php');
}

$data = [
    'is_authenticated' => is_logged_in(),
    'user_name' => $_SESSION['full_name'] ?? '',
    'user_role' => $_SESSION['role'] ?? '',
    'page_title' => 'Trust Mora Bank - Secure Digital Banking'
];

render('root/index', $data);
?>