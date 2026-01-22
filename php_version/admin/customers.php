<?php
require_once '../includes/functions.php';
require_role('Admin');

$stmt = $pdo->query("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'Customer' ORDER BY u.created_at DESC");
$users = $stmt->fetchAll();

render('admin/customers', [
    'page_title' => 'Entity Registry - Trust Mora Admin',
    'users' => $users
]);
?>