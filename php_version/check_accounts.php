<?php
require_once 'includes/db.php';

echo "=== Checking All Accounts ===\n\n";

$stmt = $pdo->query("SELECT account_number, user_id, status, balance FROM accounts ORDER BY id");
$accounts = $stmt->fetchAll();

foreach ($accounts as $acc) {
    echo "Account: " . $acc['account_number'] . "\n";
    echo "  Status: " . $acc['status'] . "\n";
    echo "  Balance: " . $acc['balance'] . "\n";
    echo "  User ID: " . $acc['user_id'] . "\n\n";
}

echo "Total Accounts: " . count($accounts) . "\n";
?>