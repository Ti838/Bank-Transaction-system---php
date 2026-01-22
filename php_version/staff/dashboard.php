<?php
require_once '../includes/functions.php';
require_role('Staff');

$today = date('Y-m-d');

// Count Stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE transaction_type = 'Deposit' AND DATE(created_at) = ? AND status = 'Success'");
$stmt->execute([$today]);
$count_deposits = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE transaction_type = 'Withdrawal' AND DATE(created_at) = ? AND status = 'Success'");
$stmt->execute([$today]);
$count_withdrawals = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE transaction_type = 'Transfer' AND DATE(created_at) = ? AND status = 'Success'");
$stmt->execute([$today]);
$count_transfers = $stmt->fetchColumn();

// Recent Activity Stream
$stmt = $pdo->query("
    SELECT t.*, 
           COALESCE(fad.full_name, fsd.full_name, fcd.full_name) as from_name,
           COALESCE(tad.full_name, tsd.full_name, tcd.full_name) as to_name
    FROM transactions t
    LEFT JOIN accounts fa ON t.from_account_id = fa.id
    LEFT JOIN accounts ta ON t.to_account_id = ta.id
    LEFT JOIN users fu ON fa.user_id = fu.id
    LEFT JOIN users tu ON ta.user_id = tu.id
    LEFT JOIN admin_details fad ON fu.id = fad.user_id
    LEFT JOIN staff_details fsd ON fu.id = fsd.user_id
    LEFT JOIN customer_details fcd ON fu.id = fcd.user_id
    LEFT JOIN admin_details tad ON tu.id = tad.user_id
    LEFT JOIN staff_details tsd ON tu.id = tsd.user_id
    LEFT JOIN customer_details tcd ON tu.id = tcd.user_id
    ORDER BY t.created_at DESC LIMIT 10
");
$recent_transactions = $stmt->fetchAll();

// Handle Account Lookup
$balance_info = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['account_number'])) {
    $acc_num = $_POST['account_number'];
    $stmt = $pdo->prepare("
        SELECT a.balance, COALESCE(ad.full_name, sd.full_name, cd.full_name) as full_name
        FROM accounts a 
        JOIN users u ON a.user_id = u.id 
        LEFT JOIN admin_details ad ON u.id = ad.user_id
        LEFT JOIN staff_details sd ON u.id = sd.user_id
        LEFT JOIN customer_details cd ON u.id = cd.user_id
        WHERE a.account_number = ?
    ");
    $stmt->execute([$acc_num]);
    $balance_info = $stmt->fetch();
}

render('staff/dashboard', [
    'page_title' => 'Operational Nexus - Trust Mora Staff',
    'count_deposits' => $count_deposits,
    'count_withdrawals' => $count_withdrawals,
    'count_transfers' => $count_transfers,
    'recent_transactions' => $recent_transactions,
    'balance_info' => $balance_info
]);
?>