<?php
require_once 'includes/functions.php';

echo "<pre>Initializing Trust Mora Bank Recovery & Seeding Protocol...\n";

try {
    // 1. Clear existing data (Optional, but good for a clean start)
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE transactions");
    $pdo->exec("TRUNCATE TABLE notifications");
    $pdo->exec("TRUNCATE TABLE accounts");
    $pdo->exec("TRUNCATE TABLE admin_details");
    $pdo->exec("TRUNCATE TABLE staff_details");
    $pdo->exec("TRUNCATE TABLE customer_details");
    $pdo->exec("TRUNCATE TABLE users");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "[SUCCESS] Database wiped for clean installation.\n";

    // Get Role IDs
    $roles = $pdo->query("SELECT id, name FROM roles")->fetchAll(PDO::FETCH_KEY_PAIR);
    $admin_role = array_search('Admin', $roles);
    $staff_role = array_search('Staff', $roles);
    $customer_role = array_search('Customer', $roles);

    $pass = password_hash('password123', PASSWORD_DEFAULT);

    // 2. Seed Admin
    $pdo->prepare("INSERT INTO users (role_id, email, password_hash) VALUES (?, 'admin@trustmora.com', ?)")->execute([$admin_role, $pass]);
    $admin_id = $pdo->lastInsertId();
    $pdo->prepare("INSERT INTO admin_details (user_id, full_name) VALUES (?, 'System Administrator')")->execute([$admin_id]);
    $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type, balance, status) VALUES ('2020000001', ?, 'Savings', 1000000000.00, 'Active')")->execute([$admin_id]);
    $admin_acc_id = $pdo->lastInsertId();

    // Record initial liquidity deposit
    $pdo->prepare("INSERT INTO transactions (transaction_type, amount, to_account_id, status, description) VALUES ('Deposit', 1000000000.00, ?, 'Success', 'Initial Bank Liquidity Injection')")->execute([$admin_acc_id]);

    echo "[SUCCESS] Admin seeded - Balance: 100 Crore BDT (Bank Liquid Capital)\n";

    // 3. Seed Staff
    $pdo->prepare("INSERT INTO users (role_id, email, password_hash) VALUES (?, 'staff@trustmora.com', ?)")->execute([$staff_role, $pass]);
    $staff_id = $pdo->lastInsertId();
    $pdo->prepare("INSERT INTO staff_details (user_id, full_name, phone, bio) VALUES (?, 'Ops Manager', '01711223344', 'System Operator Delta')")->execute([$staff_id]);
    $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type, balance, status) VALUES ('2020000002', ?, 'Savings', 0.00, 'Active')")->execute([$staff_id]);
    echo "[SUCCESS] Staff seeded - Balance: 0.00 BDT\n";

    // 4. Seed Customer
    $pdo->prepare("INSERT INTO users (role_id, email, password_hash) VALUES (?, 'user@trustmora.com', ?)")->execute([$customer_role, $pass]);
    $user_id = $pdo->lastInsertId();
    $pdo->prepare("INSERT INTO customer_details (user_id, full_name, gender, phone, address, bio) VALUES (?, 'John Doe', 'Male', '01900112233', 'Dhaka, Bangladesh', 'Regular User')")->execute([$user_id]);
    $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type, balance, status) VALUES ('2020123456', ?, 'Savings', 0.00, 'Active')")->execute([$user_id]);
    echo "[SUCCESS] Customer seeded - Balance: 0.00 BDT\n";

    echo "\n[COMPLETE] Platform seeding finished. You can now login and test all features.\n";
    echo "Warning: Delete this file (seed_data.php) before moving to production.</pre>";

} catch (Exception $e) {
    echo "\n[ERROR] Seeding failed: " . $e->getMessage() . "</pre>";
}
?>