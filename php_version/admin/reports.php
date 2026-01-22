<?php
require_once '../includes/functions.php';
require_role('Admin');

// Fetch all transactions
$stmt = $pdo->query("SELECT * FROM transactions ORDER BY created_at DESC");
$transactions = $stmt->fetchAll();

// Calculate stats
$total_in = 0;
$total_out = 0;
$total_fees = 0;
foreach ($transactions as $t) {
    if ($t['transaction_type'] === 'Deposit') {
        $total_in += $t['amount'];
    } elseif ($t['transaction_type'] === 'Withdrawal') {
        $total_out += $t['amount'];
    } elseif ($t['transaction_type'] === 'Transfer') {
        $total_out += $t['amount'];
    }
    $total_fees += $t['fee'];
}

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="bank_report_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Date', 'Type', 'Description', 'Amount', 'Fee', 'Status']);
    foreach ($transactions as $t) {
        fputcsv($output, [$t['id'], $t['created_at'], $t['transaction_type'], $t['description'], $t['amount'], $t['fee'], $t['status']]);
    }
    fclose($output);
    exit;
}

render('admin/reports', [
    'page_title' => 'Intelligence Complex - Trust Mora Admin',
    'transactions' => $transactions,
    'total_in' => $total_in,
    'total_out' => $total_out,
    'total_fees' => $total_fees
]);
?>