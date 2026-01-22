<?php
require_once '../includes/functions.php';
require_role('Admin');

// Handle Activation/Suspension
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
    redirect('admin/accounts.php');
}

$stmt = $pdo->query("
    SELECT a.*, COALESCE(ad.full_name, sd.full_name, cd.full_name) as full_name, u.email 
    FROM accounts a 
    JOIN users u ON a.user_id = u.id 
    LEFT JOIN admin_details ad ON u.id = ad.user_id
    LEFT JOIN staff_details sd ON u.id = sd.user_id
    LEFT JOIN customer_details cd ON u.id = cd.user_id
    ORDER BY a.created_at DESC
");
$accounts = $stmt->fetchAll();

render('admin/accounts', [
    'page_title' => 'Vault Oversight - Trust Mora Admin',
    'accounts' => $accounts
]);
?>