<?php
require_once '../includes/functions.php';
require_role('Admin');



// Total Customer Count (Role ID Check)
$stmt = $pdo->query("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'Customer'");

$total_customers = $stmt->fetchColumn();



// Liability: Total User Holdings (Excluding Reserve)
$stmt = $pdo->query("SELECT SUM(balance) FROM accounts WHERE account_number != '2020000001'");

$total_balance = $stmt->fetchColumn() ?: 0;



// Bank Reserve: Actual Liquid Cash (Account 2020000001)
$stmt = $pdo->query("SELECT balance FROM accounts WHERE account_number = '2020000001'");

$bank_reserve = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT SUM(fee) FROM transactions WHERE status = 'Success'");
$total_revenue = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM transactions WHERE DATE(created_at) = CURDATE()");
$today_transactions_count = $stmt->fetchColumn();


$stmt = $pdo->query("SELECT SUM(amount) FROM transactions WHERE transaction_type = 'Deposit' AND DATE(created_at) = CURDATE() AND status = 'Success'");
$total_deposits_today = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT SUM(amount) FROM transactions WHERE transaction_type = 'Withdrawal' AND DATE(created_at) = CURDATE() AND status = 'Success'");
$total_withdrawals_today = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->query("SELECT SUM(amount) FROM transactions WHERE transaction_type = 'Transfer' AND DATE(created_at) = CURDATE() AND status = 'Success'");
$total_transfers_today = $stmt->fetchColumn() ?: 0;


$stmt = $pdo->query("SELECT * FROM transactions ORDER BY created_at DESC LIMIT 10");
$recent_transactions = $stmt->fetchAll();


$growth_labels = [];
$growth_data = [];
$chart_dates = [];
$chart_volumes = [];


// Generate Last 7 Days Growth Data for Charts
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $display_date = date('D', strtotime($date));

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users u JOIN roles r ON u.role_id = r.id WHERE DATE(u.created_at) <= ? AND r.name = 'Customer'");
    $stmt->execute([$date]);
    $growth_labels[] = $display_date;
    $growth_data[] = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE DATE(created_at) = ?");
    $stmt->execute([$date]);
    $chart_dates[] = $display_date;
    $chart_volumes[] = $stmt->fetchColumn();
}

render('admin/dashboard', [
    'page_title' => 'Admin Control - Trust Mora Bank',
    'total_customers' => $total_customers,
    'total_balance' => $total_balance,
    'bank_reserve' => $bank_reserve,
    'total_revenue' => $total_revenue,
    'today_transactions_count' => $today_transactions_count,
    'total_deposits_today' => $total_deposits_today,
    'total_withdrawals_today' => $total_withdrawals_today,
    'total_transfers_today' => $total_transfers_today,
    'recent_transactions' => $recent_transactions,
    'growth_labels' => $growth_labels,
    'growth_data' => $growth_data,
    'chart_dates' => $chart_dates,
    'chart_volumes' => $chart_volumes
]);
?>