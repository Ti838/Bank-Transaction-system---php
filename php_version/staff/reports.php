<?php
require_once '../includes/functions.php';
require_role('Staff');


// Filter: Today's Transactions Excluding System Reserve (2020000001)
$today = date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT t.*, fa.account_number as from_acc, ta.account_number as to_acc,
           fu.full_name as from_name,
           ta.account_number as to_acc,
           fu.full_name as from_name,
           tu.full_name as to_name,
           fu.id as from_user_id,
           tu.id as to_user_id
    FROM transactions t
    LEFT JOIN accounts fa ON t.from_account_id = fa.id
    LEFT JOIN accounts ta ON t.to_account_id = ta.id
    LEFT JOIN users fu ON fa.user_id = fu.id
    LEFT JOIN users tu ON ta.user_id = tu.id
    WHERE DATE(t.created_at) = ?
      AND (fa.account_number != '2020000001' OR fa.account_number IS NULL)
      AND (ta.account_number != '2020000001' OR ta.account_number IS NULL)
    ORDER BY t.created_at DESC
");
$stmt->execute([$today]);
$transactions = $stmt->fetchAll();

$total_in = 0;
$total_out = 0;
$total_fees = 0;
foreach ($transactions as $t) {
    if ($t['transaction_type'] === 'Deposit')
        $total_in += $t['amount'];
    if ($t['transaction_type'] === 'Withdrawal')
        $total_out += $t['amount'];
    $total_fees += $t['fee'];
}


// Export Protocol: Generate CSV Download
if (isset($_GET['export']) && $_GET['export'] === 'csv') {

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="staff_daily_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Time', 'Type', 'Amount', 'Fee', 'Customer', 'Status']);
    foreach ($transactions as $t) {
        $customer = $t['from_name'] ?: ($t['to_name'] ?: 'System');
        fputcsv($output, [$t['id'], $t['created_at'], $t['transaction_type'], $t['amount'], $t['fee'], $customer, $t['status']]);
    }
    fclose($output);
    exit;
}

render('staff/reports', [
    'page_title' => 'Operational Intelligence Hub - Trust Mora Staff',
    'transactions' => $transactions,
    'total_in' => $total_in,
    'total_out' => $total_out,
    'total_fees' => $total_fees
]);
?>