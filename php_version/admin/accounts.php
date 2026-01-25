<?php
require_once '../includes/functions.php';
require_role('Admin');


if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $acc_id = intval($_GET['id']);

    if ($action === 'suspend') {
        $stmt = $pdo->prepare("UPDATE accounts SET status = 'Suspended' WHERE id = ?");
        $stmt->execute([$acc_id]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Account suspended successfully.'];
    } elseif ($action === 'activate') {
        $stmt = $pdo->prepare("UPDATE accounts SET status = 'Active' WHERE id = ?");
        $stmt->execute([$acc_id]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Account activated successfully.'];
    }
    redirect('accounts.php');
}

$stmt = $pdo->query("
    SELECT a.*, u.full_name, u.email 
    FROM accounts a 
    JOIN users u ON a.user_id = u.id 
    ORDER BY a.created_at DESC
");
$accounts = $stmt->fetchAll();

render('admin/accounts', [
    'page_title' => 'Vault Oversight - Trust Mora Admin',
    'accounts' => $accounts
]);
?>