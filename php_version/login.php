<?php
require_once 'includes/functions.php';

// Redirection Check: If already logged in, route to appropriate dashboard
if (is_logged_in()) {

    if ($_SESSION['role'] === 'Admin')
        redirect('admin/dashboard.php');
    if ($_SESSION['role'] === 'Staff')
        redirect('staff/dashboard.php');
    redirect('customer/dashboard.php');
}

// Brute Force Protection: Init Attempt Counter
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
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.email = ? OR u.id = ?
        ");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if ($user && $password === $user['password_hash']) {
            // Reset Security Flags
            $_SESSION['login_attempts'] = 0;

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