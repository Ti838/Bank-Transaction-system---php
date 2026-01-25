<?php
require_once '../includes/functions.php';
require_role('Customer');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE user_id = ?");
$stmt->execute([$user_id]);
$account = $stmt->fetch();

$recent_transactions = [];
$chart_data = [];

if ($account) {

    $stmt = $pdo->prepare("
        SELECT * FROM transactions 
        WHERE from_account_id = ? OR to_account_id = ? 
        ORDER BY created_at DESC LIMIT 10
    ");
    $stmt->execute([$account['id'], $account['id']]);
    $recent_transactions = $stmt->fetchAll();


    $stmt = $pdo->prepare("
        SELECT transaction_type as type, amount, created_at as date 
        FROM transactions 
        WHERE from_account_id = ? OR to_account_id = ? 
        ORDER BY created_at DESC LIMIT 100
    ");
    $stmt->execute([$account['id'], $account['id']]);
    $chart_data = $stmt->fetchAll();
}

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND read_status = 0 ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();

render('customer/dashboard', [
    'page_title' => 'Dashboard - Trust Mora Bank',
    'account' => $account,
    'recent_transactions' => $recent_transactions,
    'chart_data' => $chart_data,
    'notifications' => $notifications
]);
?>