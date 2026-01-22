<?php
require_once '../includes/functions.php';
require_role('Admin');

$stmt = $pdo->query("
    SELECT t.*, 
           fa.account_number as from_acc, 
           ta.account_number as to_acc
    FROM transactions t
    LEFT JOIN accounts fa ON t.from_account_id = fa.id
    LEFT JOIN accounts ta ON t.to_account_id = ta.id
    ORDER BY t.created_at DESC
");
$transactions = $stmt->fetchAll();

render('admin/transactions', [
    'page_title' => 'Nexus Ledger - Trust Mora Admin',
    'transactions' => $transactions
]);
?>