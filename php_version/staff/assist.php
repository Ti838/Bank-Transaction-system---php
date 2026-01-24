<?php
require_once '../includes/functions.php';
require_role('Staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_number = $_POST['account_number'] ?? '';
    $transaction_type = $_POST['transaction_type'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $description = $_POST['description'] ?? 'Staff Assisted';

    if ($amount <= 0) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid amount.'];
    } else {
        $stmt = $pdo->prepare("SELECT id FROM accounts WHERE account_number = ?");
        $stmt->execute([$account_number]);
        $acc_id = $stmt->fetchColumn();

        if (!$acc_id) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Account not found.'];
        } else {
            if ($transaction_type === 'Deposit') {
                $result = process_deposit($acc_id, $amount, $description);
            } elseif ($transaction_type === 'Withdrawal') {
                $result = process_withdrawal($acc_id, $amount, $description);
            } else {
                $result = ['success' => false, 'message' => 'Invalid transaction type.'];
            }

            $_SESSION['flash'] = ['type' => $result['success'] ? 'success' : 'danger', 'message' => $result['message']];
            if ($result['success']) {
                redirect('dashboard.php');
            }
        }
    }
}

render('staff/assist', [
    'page_title' => 'Resolution Core - Trust Mora Staff'
]);
?>