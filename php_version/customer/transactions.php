<?php
require_once '../includes/functions.php';
require_role('Customer');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE user_id = ?");
$stmt->execute([$user_id]);
$account = $stmt->fetch();

$transactions = [];
if ($account) {
    $stmt = $pdo->prepare("
        // Fetch All Ingress (To) and Egress (From) Transactions
        SELECT * FROM transactions 
        WHERE from_account_id = ? OR to_account_id = ? 
        ORDER BY created_at DESC

    ");
    $stmt->execute([$account['id'], $account['id']]);
    $transactions = $stmt->fetchAll();
}

render('customer/transactions', [
    'page_title' => 'Archive Stream - Trust Mora Bank',
    'transactions' => $transactions
]);
?>