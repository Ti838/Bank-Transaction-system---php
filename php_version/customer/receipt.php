<?php
require_once '../includes/functions.php';
require_login();

$transaction_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$transaction_id) {
    die("Invalid Transaction ID.");
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
    die("Transaction not found.");
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
    die("Unauthorized access.");
}

render('customer/receipt', [
    'page_title' => 'Ledger Proof - Trust Mora Bank',
    'transaction' => $transaction
]);
?>