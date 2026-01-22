<?php
require_once '../includes/functions.php';
require_login();

$transaction_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$transaction_id) {
    die("Invalid Transaction ID.");
}

// Fetch transaction with account details
$stmt = $pdo->prepare("
    SELECT t.*, 
           fa.account_number as from_account_number, 
           ta.account_number as to_account_number,
           fu.full_name as from_user_name,
           tu.full_name as to_user_name
    FROM transactions t
    LEFT JOIN accounts fa ON t.from_account_id = fa.id
    LEFT JOIN accounts ta ON t.to_account_id = ta.id
    LEFT JOIN users fu ON fa.user_id = fu.id
    LEFT JOIN users tu ON ta.user_id = tu.id
    WHERE t.id = ?
");
$stmt->execute([$transaction_id]);
$transaction = $stmt->fetch();

if (!$transaction) {
    die("Transaction not found.");
}

// Access Control
$is_admin_or_staff = in_array($_SESSION['role'] ?? '', ['Admin', 'Staff']);
$is_owner = false;

$stmt = $pdo->prepare("SELECT id FROM accounts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_accounts = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (in_array($transaction['from_account_id'], $user_accounts) || in_array($transaction['to_account_id'], $user_accounts)) {
    $is_owner = true;
}

if (!$is_admin_or_staff && !$is_owner) {
    die("Unauthorized access.");
}

render('customer/receipt', [
    'page_title' => 'Ledger Proof - Trust Mora Bank',
    'transaction' => $transaction
]);
?>