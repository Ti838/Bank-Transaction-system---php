<?php
require_once '../includes/functions.php';
require_role('Staff');

$stmt = $pdo->query("
    SELECT u.*, r.name as role_name, a.status as account_status, a.account_number, a.balance
    FROM users u 
    JOIN roles r ON u.role_id = r.id 
    LEFT JOIN accounts a ON u.id = a.user_id 
    WHERE r.name = 'Customer'
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll();

render('staff/users', [
    'page_title' => 'Customer Database - Trust Mora Staff',
    'users' => $users
]);
?>