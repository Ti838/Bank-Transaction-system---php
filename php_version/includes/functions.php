<?php
require_once 'db.php';

session_start();

/**
 * Creates a notification for a user.
 */
function create_notification($user_id, $message, $type = 'Info')
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, notification_type) VALUES (?, ?, ?)");
    return $stmt->execute([$user_id, $message, $type]);
}

/**
 * Generates a random 10-digit account number starting with 202.
 */
function generate_account_number()
{
    return '202' . str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
}

/**
 * Gets system settings.
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

/**
 * Updates a system setting.
 */
function update_system_setting($key, $value)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    return $stmt->execute([$key, $value, $value]);
}

/**
 * Processes a deposit into an account.
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
 * Processes a withdrawal from an account.
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
 * Processes a transfer between accounts.
 */
function process_transfer($from_account_id, $to_account_number, $amount, $description = 'Transfer')
{
    global $pdo;
    $fee = floatval(get_system_settings('transfer_fee') ?: 10.00);
    $total_deduction = $amount + $fee;

    try {
        $pdo->beginTransaction();

        // Source account
        $stmt = $pdo->prepare("SELECT user_id, balance, status, account_number FROM accounts WHERE id = ? FOR UPDATE");
        $stmt->execute([$from_account_id]);
        $src = $stmt->fetch();

        if (!$src || $src['status'] !== 'Active') {
            throw new Exception("Source account not found or inactive.");
        }

        if ($src['balance'] < $total_deduction) {
            throw new Exception("Insufficient funds (Need ৳" . number_format($total_deduction, 2) . " including fee).");
        }

        // Destination account
        $stmt = $pdo->prepare("SELECT id, user_id, balance, status FROM accounts WHERE account_number = ? FOR UPDATE");
        $stmt->execute([$to_account_number]);
        $dest = $stmt->fetch();

        if (!$dest || $dest['status'] !== 'Active') {
            throw new Exception("Destination account not found or inactive.");
        }

        // Update balances
        $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?")->execute([$total_deduction, $from_account_id]);
        $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?")->execute([$amount, $dest['id']]);

        // Record transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (transaction_type, amount, fee, from_account_id, to_account_id, status, description) VALUES ('Transfer', ?, ?, ?, ?, 'Success', ?)");
        $stmt->execute([$amount, $fee, $from_account_id, $dest['id'], $description]);
        $transaction_id = $pdo->lastInsertId();

        // Notifications
        create_notification($src['user_id'], "Transferred ৳" . number_format($amount, 2) . " (Fee: ৳" . number_format($fee, 2) . ") to $to_account_number", "Success");
        create_notification($dest['user_id'], "Received ৳" . number_format($amount, 2) . " from " . $src['account_number'], "Success");

        $pdo->commit();
        return ['success' => true, 'message' => "Transfer successful.", 'transaction_id' => $transaction_id];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Simple Redirect function
 */
function redirect($url)
{
    header("Location: $url");
    exit();
}

/**
 * Checks if user is logged in
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Requires login
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

/**
 * Requires a specific role
 */
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
/**
 * Gets the exchange rate from a provider (mocked for this implementation).
 */
function get_exchange_rate($from = 'USD', $to = 'BDT')
{
    // Industry standard mock rate
    $rates = [
        'USD_BDT' => 110.50,
        'BDT_USD' => 0.0090
    ];
    return $rates["{$from}_{$to}"] ?? 1.0;
}

/**
 * Renders a view template with provided data.
 * @param string $template_path Path to the .html template relative to templates/
 * @param array $data Associative array of data to make available in the view
 */
function render($template_path, $data = [])
{
    global $pdo;

    // Extract data to make variables available in the scope
    extract($data);

    // Default page title if not set
    if (!isset($page_title)) {
        $page_title = 'Trust Mora Bank - Secure Digital Banking';
    }

    // Capture the view content
    ob_start();

    // Determine base path for includes
    $templates_dir = __DIR__ . '/../templates/';
    $template_file = $templates_dir . $template_path . '.html';

    if (file_exists($template_file)) {
        include $template_file;
    } else {
        echo "<div style='color:red; background:white; padding:20px; border:2px solid red;'>[ERROR] Template not found: $template_file</div>";
    }

    $view_content = ob_get_clean();

    // Include the standard layout
    include __DIR__ . '/header.php';
    include __DIR__ . '/navbar.php';
    echo $view_content;
    include __DIR__ . '/footer.php';
}
?>