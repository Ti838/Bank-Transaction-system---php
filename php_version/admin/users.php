<?php
require_once '../includes/functions.php';
require_role('Admin');


if (isset($_GET['action']) && isset($_GET['id'])) {
    // Action Dispatcher: Handle Promote/Demote/Delete
    $action = $_GET['action'];
    $target_id = intval($_GET['id']);


    if ($target_id === $_SESSION['user_id']) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Cannot perform actions on yourself.'];
    } else {
        if ($action === 'promote') {
            $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'Staff'");
            $stmt->execute();
            $role_id = $stmt->fetchColumn();


            $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE id = ?");
            $stmt->execute([$role_id, $target_id]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User promoted to Staff.'];
        } elseif ($action === 'demote') {
            $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'Customer'");
            $stmt->execute();
            $role_id = $stmt->fetchColumn();


            $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE id = ?");
            $stmt->execute([$role_id, $target_id]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User demoted to Customer.'];
        } elseif ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE accounts SET status = 'Active' WHERE user_id = ?");
            $stmt->execute([$target_id]);
            create_notification($target_id, "Your account has been approved and is now active.", "Success");
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Account approved.'];
        } elseif ($action === 'suspend') {
            $stmt = $pdo->prepare("UPDATE accounts SET status = 'Suspended' WHERE user_id = ?");
            $stmt->execute([$target_id]);
            create_notification($target_id, "Your account has been suspended. Please contact support.", "Warning");
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Account suspended.'];
        } elseif ($action === 'delete') {
            // Cascade Delete: Wipe Transactions -> Account -> Notifications -> User
            $pdo->prepare("DELETE FROM transactions WHERE from_account_id IN (SELECT id FROM accounts WHERE user_id = ?) OR to_account_id IN (SELECT id FROM accounts WHERE user_id = ?)")->execute([$target_id, $target_id]);

            $pdo->prepare("DELETE FROM accounts WHERE user_id = ?")->execute([$target_id]);
            $pdo->prepare("DELETE FROM notifications WHERE user_id = ?")->execute([$target_id]);
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$target_id]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User and related data deleted.'];
        }
        redirect('users.php');
    }
}

$where_clause = "";
$params = [];
$current_role = 'All';

if (isset($_GET['role'])) {
    $role = $_GET['role'];
    if (in_array($role, ['Staff', 'Customer'])) {
        $where_clause = "WHERE r.name = ?";
        $params[] = $role;
        $current_role = $role;
    }
}

$stmt = $pdo->prepare("
    // Fetch Users with Role and Account Balance
    SELECT u.*, u.kyc_document, r.name as role_name, a.status as account_status, a.account_number, a.balance
    FROM users u 
    JOIN roles r ON u.role_id = r.id 
    LEFT JOIN accounts a ON u.id = a.user_id 
    $where_clause
    ORDER BY u.created_at DESC

");
$stmt->execute($params);
$users = $stmt->fetchAll();

render('admin/users', [
    'page_title' => 'Entity Control - Trust Mora Admin',
    'users' => $users,
    'current_role' => $current_role
]);
?>