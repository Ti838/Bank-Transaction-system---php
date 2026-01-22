<?php
require_once 'includes/functions.php';

if (is_logged_in()) {
    if ($_SESSION['role'] === 'Admin')
        redirect('admin/dashboard.php');
    if ($_SESSION['role'] === 'Staff')
        redirect('staff/dashboard.php');
    redirect('customer/dashboard.php');
}

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['identifier'] ?? '';
    $password = $_POST['password'] ?? '';
    $captcha_input = $_POST['captcha'] ?? '';

    $require_captcha = $_SESSION['login_attempts'] >= 3;
    $captcha_valid = true;

    if ($require_captcha) {
        if (!isset($_SESSION['captcha_answer']) || $captcha_input != $_SESSION['captcha_answer']) {
            $captcha_valid = false;
        }
    }

    if (!$captcha_valid) {
        $error = "Incorrect CAPTCHA answer.";
    } else {
        $stmt = $pdo->prepare("
            SELECT u.*, r.name as role_name, 
                   COALESCE(ad.full_name, sd.full_name, cd.full_name) as full_name,
                   COALESCE(ad.profile_picture, sd.profile_picture, cd.profile_picture) as profile_picture
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            LEFT JOIN admin_details ad ON u.id = ad.user_id
            LEFT JOIN staff_details sd ON u.id = sd.user_id
            LEFT JOIN customer_details cd ON u.id = cd.user_id
            WHERE u.email = ? OR u.id = ?
        ");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['login_attempts'] = 0; // Reset on success
            unset($_SESSION['captcha_answer']);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['profile_picture'] = $user['profile_picture'];

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Login successful.'];

            if ($user['role_name'] === 'Admin')
                redirect('admin/dashboard.php');
            if ($user['role_name'] === 'Staff')
                redirect('staff/dashboard.php');
            redirect('customer/dashboard.php');
        } else {
            $_SESSION['login_attempts']++;
            $error = "Invalid credentials.";
        }
    }
}

// Generate new CAPTCHA if needed
if ($_SESSION['login_attempts'] >= 3) {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['captcha_question'] = "$num1 + $num2";
    $_SESSION['captcha_answer'] = $num1 + $num2;
}

render('root/login', [
    'page_title' => 'Login - Trust Mora Bank',
    'error' => $error ?? null
]);
?>