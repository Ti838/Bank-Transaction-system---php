<?php
require_once 'includes/functions.php';

if (is_logged_in()) {
    if ($_SESSION['role'] === 'Admin')
        redirect('admin/dashboard.php');
    if ($_SESSION['role'] === 'Staff')
        redirect('staff/dashboard.php');
    redirect('customer/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $usertype = $_POST['usertype'] ?? 'Customer';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $bio = $_POST['bio'] ?? '';

    // Simplified usertype mapping to roles
    $role_name = $usertype;

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = "Email already exists.";
    } elseif (strlen($password) < 4) {
        $error = "Password too short.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
        $stmt->execute([$role_name]);
        $role = $stmt->fetch();

        if (!$role) {
            $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'Customer'");
            $stmt->execute();
            $role = $stmt->fetch();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (role_id, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$role['id'], $email, $hashed_password]);
        $user_id = $pdo->lastInsertId();

        if ($role_name === 'Customer') {
            $stmt = $pdo->prepare("INSERT INTO customer_details (user_id, full_name, gender, phone, address, bio) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $full_name, $gender, $phone, $address, $bio]);
        } elseif ($role_name === 'Staff') {
            $stmt = $pdo->prepare("INSERT INTO staff_details (user_id, full_name, phone, bio) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $full_name, $phone, $bio]);
        } elseif ($role_name === 'Admin') {
            $stmt = $pdo->prepare("INSERT INTO admin_details (user_id, full_name) VALUES (?, ?)");
            $stmt->execute([$user_id, $full_name]);
        }

        if ($role_name === 'Customer' || $role_name === 'Admin' || $role_name === 'Staff') {
            $account_number = generate_account_number();
            $status = ($role_name === 'Customer') ? 'Pending' : 'Active';
            $stmt = $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type, balance, status) VALUES (?, ?, 'Savings', 0.00, ?)");
            $stmt->execute([$account_number, $user_id, $status]);

            if ($status === 'Pending') {
                create_notification($user_id, "Welcome to Trust Mora Bank! Your account $account_number is currently pending approval.", "Info");
            } else {
                create_notification($user_id, "Welcome to Trust Mora Bank! Your account $account_number is now active.", "Success");
            }
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Signup successful! Please login.'];
        redirect('login.php');
    }
}

render('root/signup', [
    'page_title' => 'Open Account - Trust Mora Bank',
    'error' => $error ?? null
]);
?>