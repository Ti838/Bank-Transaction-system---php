<?php
require_once '../includes/functions.php';

// Allow both Admin and Staff to access this page
require_login();
if ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff') {
    redirect('../index.php');
}

if (!isset($_GET['id'])) {
    redirect('users.php'); // or dashboard depending on role
}

$user_id = intval($_GET['id']);

// Handle Fund Injection/Withdrawal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fund_action'])) {
    if ($_SESSION['role'] !== 'Admin') {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Unauthorized access.'];
    } else {
        $amount = floatval($_POST['amount']);
        $action = $_POST['fund_action'];
        $description = $_POST['description'] ?? 'Admin Override';

        $stmt = $pdo->prepare("SELECT id FROM accounts WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $acc_id = $stmt->fetchColumn();

        if ($acc_id) {
            if ($action === 'deposit') {
                $res = process_deposit($acc_id, $amount, $description);
            } elseif ($action === 'withdraw') {
                $res = process_withdrawal($acc_id, $amount, $description);
            }
            $_SESSION['flash'] = ['type' => $res['success'] ? 'success' : 'danger', 'message' => $res['message']];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'User has no active account.'];
        }
        redirect("user_details.php?id=$user_id");
    }
}

// Fetch user details
$stmt = $pdo->prepare("
    SELECT u.*, r.name as role_name, a.account_number, a.balance, a.status as account_status, a.account_type
    FROM users u 
    JOIN roles r ON u.role_id = r.id 
    LEFT JOIN accounts a ON u.id = a.user_id 
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'User not found.'];
    redirect($_SESSION['role'] === 'Admin' ? 'users.php' : 'assist.php');
}

// Fetch recent transactions for this user
$stmt = $pdo->prepare("
    SELECT * FROM transactions 
    WHERE from_account_id = (SELECT id FROM accounts WHERE user_id = ?) 
       OR to_account_id = (SELECT id FROM accounts WHERE user_id = ?)
    ORDER BY created_at DESC LIMIT 10
");
$stmt->execute([$user_id, $user_id]);
$transactions = $stmt->fetchAll();

render('admin/user_details', [
    'page_title' => 'Entity Profile - ' . $user['full_name'],
    'u' => $user,
    'transactions' => $transactions
]);
?>