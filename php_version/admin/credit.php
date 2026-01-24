<?php
require_once '../includes/functions.php';
require_role('Admin');

$acc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$acc_id)
    die("Invalid Account ID.");

$stmt = $pdo->prepare("
    SELECT a.*, COALESCE(ad.full_name, sd.full_name, cd.full_name) as full_name 
    FROM accounts a 
    JOIN users u ON a.user_id = u.id 
    LEFT JOIN admin_details ad ON u.id = ad.user_id
    LEFT JOIN staff_details sd ON u.id = sd.user_id
    LEFT JOIN customer_details cd ON u.id = cd.user_id
    WHERE a.id = ?
");
$stmt->execute([$acc_id]);
$account = $stmt->fetch();

if (!$account)
    die("Account not found.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0);
    $description = $_POST['description'] ?? 'Bank Credit';

    if ($amount <= 0) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid amount.'];
    } else {
        $result = process_deposit($acc_id, $amount, $description);
        $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'danger', 'message' => $result['message']];
        if ($result['success']) {
            redirect('../customer/receipt.php?id=' . $pdo->lastInsertId());
        }
    }
}

render('admin/credit', [
    'page_title' => 'Asset Injection Hub - Trust Mora Admin',
    'account' => $account
]);
?>