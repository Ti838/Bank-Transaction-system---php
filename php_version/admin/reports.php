<?php
require_once '../includes/functions.php';
require_role('Admin');

// Get today's date for filtering
$today = date('Y-m-d');

// Fetch all transactions with user names, excluding bank's own account
$stmt = $pdo->prepare("
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
    WHERE DATE(t.created_at) = ?
      AND (fa.account_number != '2020000001' OR fa.account_number IS NULL)
      AND (ta.account_number != '2020000001' OR ta.account_number IS NULL)
    ORDER BY t.created_at DESC
");
$stmt->execute([$today]);
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