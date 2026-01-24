<?php
require_once '../includes/functions.php';
require_role('Admin');

$stmt = $pdo->query("
    SELECT t.*, 
           fa.account_number as from_acc, 
           ta.account_number as to_acc,
           COALESCE(fad.full_name, fsd.full_name, fcd.full_name) as from_name,
           COALESCE(tad.full_name, tsd.full_name, tcd.full_name) as to_name
    FROM transactions t
    LEFT JOIN accounts fa ON t.from_account_id = fa.id
    LEFT JOIN accounts ta ON t.to_account_id = ta.id
    LEFT JOIN admin_details fad ON fa.user_id = fad.user_id
    LEFT JOIN staff_details fsd ON fa.user_id = fsd.user_id
    LEFT JOIN customer_details fcd ON fa.user_id = fcd.user_id
    LEFT JOIN admin_details tad ON ta.user_id = tad.user_id
    LEFT JOIN staff_details tsd ON ta.user_id = tsd.user_id
    LEFT JOIN customer_details tcd ON ta.user_id = tcd.user_id
    ORDER BY t.created_at DESC
");
$transactions = $stmt->fetchAll();

render('admin/transactions', [
    'page_title' => 'Nexus Ledger - Trust Mora Admin',
    'transactions' => $transactions
]);
?>