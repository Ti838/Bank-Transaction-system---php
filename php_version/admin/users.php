<?php
require_once '../includes/functions.php';
require_role('Admin');

// Handle Actions (Promote/Demote/Delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $target_id = intval($_GET['id']);

    if ($target_id === $_SESSION['user_id']) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Cannot perform actions on yourself.'];
    } else {
        if ($action === 'promote') {
            $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'Staff'");
            $stmt->execute();
            $role_id = $stmt->fetchColumn();

            // Move details from customer to staff if they were a customer
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("SELECT * FROM customer_details WHERE user_id = ?");
            $stmt->execute([$target_id]);
            $details = $stmt->fetch();
            if ($details) {
                $stmt = $pdo->prepare("INSERT INTO staff_details (user_id, full_name, phone, bio, profile_picture) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$target_id, $details['full_name'], $details['phone'], $details['bio'], $details['profile_picture']]);
                $pdo->prepare("DELETE FROM customer_details WHERE user_id = ?")->execute([$target_id]);
            }
            $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE id = ?");
            $stmt->execute([$role_id, $target_id]);
            $pdo->commit();

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User promoted to Staff.'];
        } elseif ($action === 'demote') {
            $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'Customer'");
            $stmt->execute();
            $role_id = $stmt->fetchColumn();

            // Move details from staff to customer if they were staff
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("SELECT * FROM staff_details WHERE user_id = ?");
            $stmt->execute([$target_id]);
            $details = $stmt->fetch();
            if ($details) {
                $stmt = $pdo->prepare("INSERT INTO customer_details (user_id, full_name, phone, bio, profile_picture) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$target_id, $details['full_name'], $details['phone'], $details['bio'], $details['profile_picture']]);
                $pdo->prepare("DELETE FROM staff_details WHERE user_id = ?")->execute([$target_id]);
            }
            $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE id = ?");
            $stmt->execute([$role_id, $target_id]);
            $pdo->commit();

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
            $pdo->prepare("DELETE FROM transactions WHERE from_account_id IN (SELECT id FROM accounts WHERE user_id = ?) OR to_account_id IN (SELECT id FROM accounts WHERE user_id = ?)")->execute([$target_id, $target_id]);
            $pdo->prepare("DELETE FROM accounts WHERE user_id = ?")->execute([$target_id]);
            $pdo->prepare("DELETE FROM notifications WHERE user_id = ?")->execute([$target_id]);
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$target_id]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User and related data deleted.'];
        }
    }
    redirect('admin/users.php');
}

$stmt = $pdo->query("
    SELECT u.*, r.name as role_name, a.status as account_status, a.account_number,
           COALESCE(ad.full_name, sd.full_name, cd.full_name) as full_name
    FROM users u 
    JOIN roles r ON u.role_id = r.id 
    LEFT JOIN accounts a ON u.id = a.user_id 
    LEFT JOIN admin_details ad ON u.id = ad.user_id
    LEFT JOIN staff_details sd ON u.id = sd.user_id
    LEFT JOIN customer_details cd ON u.id = cd.user_id
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll();

render('admin/users', [
    'page_title' => 'Entity Control - Trust Mora Admin',
    'users' => $users
]);
?>