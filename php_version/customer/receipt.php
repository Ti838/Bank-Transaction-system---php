<?php
require_once '../includes/functions.php';
require_login();

$transaction_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Determine dashboard URL based on role
$in_subfolder = true;
$prefix = '../';
$dashboard_urls = [
    'Admin' => $prefix . 'admin/dashboard.php',
    'Staff' => $prefix . 'staff/dashboard.php',
    'Customer' => $prefix . 'customer/dashboard.php'
];
$dashboard_url = $dashboard_urls[$_SESSION['role']] ?? $prefix . 'index.php';

if (!$transaction_id || $transaction_id <= 0) {
    render('shared/error', [
        'page_title' => 'Invalid Transaction - Trust Mora Bank',
        'error_title' => 'Invalid Transaction ID',
        'error_message' => 'The transaction ID provided is invalid or missing. Please check the link and try again.',
        'back_url' => 'javascript:history.back()',
        'dashboard_url' => $dashboard_url,
        'error_details' => 'Transaction ID must be a positive integer. Received: ' . ($_GET['id'] ?? 'none')
    ]);
    exit;
}

// Fetch transaction with account details and user names from all detail tables
$stmt = $pdo->prepare("
    SELECT t.*, 
           fa.account_number as from_account_number, 
           ta.account_number as to_account_number,
           COALESCE(fad.full_name, fsd.full_name, fcd.full_name) as from_user_name,
           COALESCE(tad.full_name, tsd.full_name, tcd.full_name) as to_user_name
    FROM transactions t
    LEFT JOIN accounts fa ON t.from_account_id = fa.id
    LEFT JOIN accounts ta ON t.to_account_id = ta.id
    LEFT JOIN admin_details fad ON fa.user_id = fad.user_id
    LEFT JOIN staff_details fsd ON fa.user_id = fsd.user_id
    LEFT JOIN customer_details fcd ON fa.user_id = fcd.user_id
    LEFT JOIN admin_details tad ON ta.user_id = tad.user_id
    LEFT JOIN staff_details tsd ON ta.user_id = tsd.user_id
    LEFT JOIN customer_details tcd ON ta.user_id = tcd.user_id
    WHERE t.id = ?
");
$stmt->execute([$transaction_id]);
$transaction = $stmt->fetch();

if (!$transaction) {
    render('shared/error', [
        'page_title' => 'Transaction Not Found - Trust Mora Bank',
        'error_title' => 'Transaction Not Found',
        'error_message' => 'The requested transaction could not be found in our system. It may have been deleted or the ID is incorrect.',
        'back_url' => 'javascript:history.back()',
        'dashboard_url' => $dashboard_url,
        'error_details' => 'No transaction found with ID: ' . $transaction_id
    ]);
    exit;
}

// Access Control Logic
$is_admin_or_staff = in_array($_SESSION['role'] ?? '', ['Admin', 'Staff']);
$is_owner = false;

// If customer, check if they are the sender or receiver
if ($_SESSION['role'] === 'Customer') {
    $stmt = $pdo->prepare("SELECT id FROM accounts WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_accounts = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (in_array($transaction['from_account_id'], $user_accounts) || in_array($transaction['to_account_id'], $user_accounts)) {
        $is_owner = true;
    }
}

// Admins and Staff can see everything; Customers only see their own
if (!$is_admin_or_staff && !$is_owner) {
    render('shared/error', [
        'page_title' => 'Unauthorized Access - Trust Mora Bank',
        'error_title' => 'Unauthorized Access',
        'error_message' => 'You do not have permission to view this transaction. You can only view transactions associated with your account.',
        'back_url' => 'javascript:history.back()',
        'dashboard_url' => $dashboard_url,
        'error_details' => 'Access denied for transaction ID: ' . $transaction_id
    ]);
    exit;
}

render('customer/receipt', [
    'page_title' => 'Ledger Proof - Trust Mora Bank',
    'transaction' => $transaction
]);
?>