<?php
require_once 'includes/functions.php';

echo "<pre>Initializing Trust Mora Bank Recovery & Seeding Protocol...\n";

try {

    // 1. DANGER: Truncate All Data for Clean Slate
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    $pdo->exec("TRUNCATE TABLE transactions");
    $pdo->exec("TRUNCATE TABLE notifications");
    $pdo->exec("TRUNCATE TABLE accounts");

    $pdo->exec("TRUNCATE TABLE users");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "[SUCCESS] Database wiped for clean installation.\n";


    // 2. Fetch Helper IDs for Roles
    $roles = $pdo->query("SELECT id, name FROM roles")->fetchAll(PDO::FETCH_KEY_PAIR);

    $admin_role = array_search('Admin', $roles);
    $staff_role = array_search('Staff', $roles);
    $customer_role = array_search('Customer', $roles);

    $pass = 'password123';


    // 3. Create Root Admin (Bank Reserve Owner)
    $pdo->prepare("INSERT INTO users (role_id, email, password_hash, full_name) VALUES (?, 'admin@trustmora.com', ?, 'System Administrator')")->execute([$admin_role, $pass]);

    $admin_id = $pdo->lastInsertId();
    $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type, balance, status) VALUES ('2020000001', ?, 'Savings', 1000000000.00, 'Active')")->execute([$admin_id]);
    $admin_acc_id = $pdo->lastInsertId();


    $pdo->prepare("INSERT INTO transactions (transaction_type, amount, to_account_id, status, description) VALUES ('Deposit', 1000000000.00, ?, 'Success', 'Initial Bank Liquidity Injection')")->execute([$admin_acc_id]);

    echo "[SUCCESS] Admin seeded - Balance: 100 Crore BDT (Bank Liquid Capital)\n";


    $pdo->prepare("INSERT INTO users (role_id, email, password_hash, full_name, phone, bio) VALUES (?, 'staff@trustmora.com', ?, 'Ops Manager', '01711223344', 'System Operator Delta')")->execute([$staff_role, $pass]);
    $staff_id = $pdo->lastInsertId();
    $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type, balance, status) VALUES ('2020000002', ?, 'Savings', 0.00, 'Active')")->execute([$staff_id]);
    echo "[SUCCESS] Staff seeded - Balance: 0.00 BDT\n";


    $pdo->prepare("INSERT INTO users (role_id, email, password_hash, full_name, gender, phone, address, bio) VALUES (?, 'user@trustmora.com', ?, 'John Doe', 'Male', '01900112233', 'Dhaka, Bangladesh', 'Regular User')")->execute([$customer_role, $pass]);
    $user_id = $pdo->lastInsertId();
    $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type, balance, status) VALUES ('2020123456', ?, 'Savings', 0.00, 'Active')")->execute([$user_id]);
    echo "[SUCCESS] Customer seeded - Balance: 0.00 BDT\n";

    echo "\n[COMPLETE] Platform seeding finished. You can now login and test all features.\n";
    echo "Warning: Delete this file (seed_data.php) before moving to production.</pre>";

} catch (Exception $e) {
    echo "\n[ERROR] Seeding failed: " . $e->getMessage() . "</pre>";
}
?>