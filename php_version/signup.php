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
    $account_type = $_POST['account_type'] ?? 'Savings';
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

        $hashed_password = $password; // Plain text as requested

        // Handle KYC Upload
        $kyc_document = null;
        if (isset($_FILES['kyc_document']) && $_FILES['kyc_document']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $ext = strtolower(pathinfo($_FILES['kyc_document']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = 'kyc_' . uniqid() . '.' . $ext;
                $dest = 'static/uploads/kyc/';
                if (!is_dir($dest))
                    mkdir($dest, 0777, true);
                if (move_uploaded_file($_FILES['kyc_document']['tmp_name'], $dest . $new_name)) {
                    $kyc_document = $new_name;
                }
            }
        }

        // Insert all data into users table
        $stmt = $pdo->prepare("INSERT INTO users (role_id, email, password_hash, full_name, phone, address, bio, gender, kyc_document) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$role['id'], $email, $hashed_password, $full_name, $phone, $address, $bio, $gender, $kyc_document]);
        $user_id = $pdo->lastInsertId();

        // Create Default Account for all users
        if ($role_name === 'Customer' || $role_name === 'Admin' || $role_name === 'Staff') {
            $account_number = generate_account_number();
            $status = ($role_name === 'Customer') ? 'Pending' : 'Active';

            // For non-customers (staff/admin), force Savings to keep it simple, or allow choice. 
            // Let's allow choice as per UI.

            $stmt = $pdo->prepare("INSERT INTO accounts (account_number, user_id, account_type, balance, status) VALUES (?, ?, ?, 0.00, ?)");
            $stmt->execute([$account_number, $user_id, $account_type, $status]);

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