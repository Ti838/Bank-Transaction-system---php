<?php
require_once '../includes/functions.php';
require_role('Customer');

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE user_id = ?");
$stmt->execute([$user_id]);
$account = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to_account = $_POST['to_account'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $description = $_POST['description'] ?? 'Transfer';

    if ($amount <= 0) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid amount.'];
    } elseif ($to_account === $account['account_number']) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Cannot transfer to your own account.'];
    } else {
        $result = process_transfer($account['id'], $to_account, $amount, $description);
        $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'danger', 'message' => $result['message']];
        if ($result['success']) {
            redirect('receipt.php?id=' . $pdo->lastInsertId());
        }
    }
}

render('customer/transfer', [
    'page_title' => 'Quantum Bridge - Trust Mora Bank'
]);
?>