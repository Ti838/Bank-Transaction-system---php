<?php
require_once '../includes/functions.php';
require_role('Customer');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE user_id = ?");
$stmt->execute([$user_id]);
$account = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0);
    $description = $_POST['description'] ?? 'Deposit';

    if ($amount <= 0) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid amount.'];
    } else {
        $result = process_deposit($account['id'], $amount, $description);
        $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'danger', 'message' => $result['message']];
        if ($result['success']) {
            redirect('receipt.php?id=' . $result['transaction_id']);
        }
    }
}

render('customer/deposit', [
    'page_title' => 'Vault Injection - Trust Mora Bank',
    'account' => $account
]);
?>