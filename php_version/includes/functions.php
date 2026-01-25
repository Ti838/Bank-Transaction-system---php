<?php
require_once 'db.php';

session_start();


function create_notification($user_id, $message, $type = 'Info')
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, notification_type) VALUES (?, ?, ?)");
    return $stmt->execute([$user_id, $message, $type]);
}


/**
 * Generates a unique 10-digit account number starting with '202'.
 */
function generate_account_number()
{
    return '202' . str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
}


/**
 * Retrieves system settings from DB or returns defaults.
 * Uses static caching to minimize DB queries.
 */
function get_system_settings($key = null)
{
    global $pdo;
    static $settings = null;
    if ($settings === null) {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $settings = $rows ?: [
            'bank_name' => 'Trust Mora Bank',
            'transfer_fee' => 10.00,
            'maintenance_mode' => false,
            'currency_symbol' => '৳'
        ];
    }
    return $key ? ($settings[$key] ?? null) : $settings;
}


function update_system_setting($key, $value)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    return $stmt->execute([$key, $value, $value]);
}


/**
 * Atomic Deposit: Uses Transaction to ensure DB consistency.
 */
function process_deposit($account_id, $amount, $description = 'Deposit')
{
    global $pdo;
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT user_id, balance, status FROM accounts WHERE id = ? FOR UPDATE");
        $stmt->execute([$account_id]);
        $account = $stmt->fetch();

        if (!$account || $account['status'] !== 'Active') {
            throw new Exception("Account not found or inactive.");
        }

        $new_balance = $account['balance'] + $amount;
        $stmt = $pdo->prepare("UPDATE accounts SET balance = ? WHERE id = ?");
        $stmt->execute([$new_balance, $account_id]);

        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_type, amount, to_account_id, status, description) VALUES ('Deposit', ?, ?, 'Success', ?)");
        $stmt->execute([$amount, $account_id, $description]);
        $transaction_id = $pdo->lastInsertId();

        create_notification($account['user_id'], "Deposit of ৳" . number_format($amount, 2) . " successful.", "Success");

        $pdo->commit();
        return ['success' => true, 'message' => "Deposit successful.", 'transaction_id' => $transaction_id];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}


/**
 * Atomic Withdrawal: Checks balance & locks row for update.
 */
function process_withdrawal($account_id, $amount, $description = 'Withdrawal')
{
    global $pdo;
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT user_id, balance, status FROM accounts WHERE id = ? FOR UPDATE");
        $stmt->execute([$account_id]);
        $account = $stmt->fetch();

        if (!$account || $account['status'] !== 'Active') {
            throw new Exception("Account not found or inactive.");
        }

        if ($account['balance'] < $amount) {
            throw new Exception("Insufficient funds.");
        }

        $new_balance = $account['balance'] - $amount;
        $stmt = $pdo->prepare("UPDATE accounts SET balance = ? WHERE id = ?");
        $stmt->execute([$new_balance, $account_id]);

        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_type, amount, from_account_id, status, description) VALUES ('Withdrawal', ?, ?, 'Success', ?)");
        $stmt->execute([$amount, $account_id, $description]);
        $transaction_id = $pdo->lastInsertId();

        create_notification($account['user_id'], "Withdrawal of ৳" . number_format($amount, 2) . " successful.", "Success");

        $pdo->commit();
        return ['success' => true, 'message' => "Withdrawal successful.", 'transaction_id' => $transaction_id];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}


/**
 * Secure Transfer: Locks both rows, deducts fee, and logs transaction.
 */
function process_transfer($from_account_id, $to_account_number, $amount, $description = 'Transfer')
{
    global $pdo;
    $fee = floatval(get_system_settings('transfer_fee') ?: 10.00);
    $total_deduction = $amount + $fee;

    try {
        $pdo->beginTransaction();



        // Lock Source: Prevent race conditions
        $stmt = $pdo->prepare("SELECT user_id, balance, status, account_number FROM accounts WHERE id = ? FOR UPDATE");

        $stmt->execute([$from_account_id]);
        $src = $stmt->fetch();

        if (!$src || $src['status'] !== 'Active') {
            throw new Exception("Source account not found or inactive.");
        }

        if ($src['balance'] < $total_deduction) {
            throw new Exception("Insufficient funds (Need ৳" . number_format($total_deduction, 2) . " including fee).");
        }



        // Lock Destination: Atomic update
        $stmt = $pdo->prepare("SELECT id, user_id, balance, status FROM accounts WHERE account_number = ? FOR UPDATE");

        $stmt->execute([$to_account_number]);
        $dest = $stmt->fetch();

        if (!$dest || $dest['status'] !== 'Active') {
            throw new Exception("Destination account not found or inactive.");
        }


        $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?")->execute([$total_deduction, $from_account_id]);
        $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?")->execute([$amount, $dest['id']]);


        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_type, amount, fee, from_account_id, to_account_id, status, description) VALUES ('Transfer', ?, ?, ?, ?, 'Success', ?)");
        $stmt->execute([$amount, $fee, $from_account_id, $dest['id'], $description]);
        $transaction_id = $pdo->lastInsertId();


        create_notification($src['user_id'], "Transferred ৳" . number_format($amount, 2) . " (Fee: ৳" . number_format($fee, 2) . ") to $to_account_number", "Success");
        create_notification($dest['user_id'], "Received ৳" . number_format($amount, 2) . " from " . $src['account_number'], "Success");

        $pdo->commit();
        return ['success' => true, 'message' => "Transfer successful.", 'transaction_id' => $transaction_id];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}


function redirect($url)
{
    header("Location: $url");
    exit();
}


function is_logged_in()
{
    return isset($_SESSION['user_id']);
}


/**
 * Enforce Login: Redirects if not authenticated.
 */
function require_login()
{
    if (!is_logged_in()) {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Please login to continue.'];
        $in_subfolder = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/staff/') !== false || strpos($_SERVER['PHP_SELF'], '/customer/') !== false);
        $prefix = $in_subfolder ? '../' : '';
        redirect($prefix . 'login.php');
    }
}


function require_role($role_name)
{
    require_login();
    if ($_SESSION['role'] !== $role_name) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Unauthorized access.'];
        $in_subfolder = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/staff/') !== false || strpos($_SERVER['PHP_SELF'], '/customer/') !== false);
        $prefix = $in_subfolder ? '../' : '';
        redirect($prefix . 'index.php');
    }
}

function get_exchange_rate($from = 'USD', $to = 'BDT')
{

    $rates = [
        'USD_BDT' => 110.50,
        'BDT_USD' => 0.0090
    ];
    return $rates["{$from}_{$to}"] ?? 1.0;
}


/**
 * Template Renderer: Loads HTML templates with header/footer shell.
 */
function render($template_path, $data = [])
{
    global $pdo;


    extract($data);


    if (!isset($page_title)) {
        $page_title = 'Trust Mora Bank - Secure Digital Banking';
    }


    ob_start();


    $templates_dir = __DIR__ . '/../templates/';
    $template_file = $templates_dir . $template_path . '.html';

    if (file_exists($template_file)) {
        include $template_file;
    } else {
        echo "<div style='color:red; background:white; padding:20px; border:2px solid red;'>[ERROR] Template not found: $template_file</div>";
    }

    $view_content = ob_get_clean();


    include __DIR__ . '/header.php';
    include __DIR__ . '/navbar.php';
    echo $view_content;
    include __DIR__ . '/footer.php';
}
?>