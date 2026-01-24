<?php
require_once '../includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Map roles to their specific detail tables
$details_table = 'customer_details';
if ($role === 'Admin')
    $details_table = 'admin_details';
if ($role === 'Staff')
    $details_table = 'staff_details';

$stmt = $pdo->prepare("SELECT u.email, d.* FROM users u JOIN $details_table d ON u.id = d.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Ensure $user is not false to avoid warnings
if (!$user) {
    $user = [
        'full_name' => $_SESSION['full_name'] ?? 'User',
        'email' => $_SESSION['email'] ?? '',
        'phone' => '',
        'address' => '',
        'bio' => '',
        'gender' => '',
        'profile_picture' => 'default_avatar.png',
        'nominee_name' => '',
        'nominee_relationship' => ''
    ];
}

$stmt = $pdo->prepare("SELECT * FROM accounts WHERE user_id = ?");
$stmt->execute([$user_id]);
$account = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? $user['full_name'];
    $email = $_POST['email'] ?? $user['email'];
    $phone = $_POST['phone'] ?? $user['phone'];
    $address = $_POST['address'] ?? $user['address'];
    $gender = $_POST['gender'] ?? $user['gender'];
    $bio = $_POST['bio'] ?? $user['bio'];
    $nominee_name = $_POST['nominee_name'] ?? ($user['nominee_name'] ?? '');
    $nominee_relationship = $_POST['nominee_relationship'] ?? ($user['nominee_relationship'] ?? '');

    $profile_picture = $user['profile_picture'];
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowed_exts = ['png', 'jpg', 'jpeg', 'gif'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_exts)) {
            $new_name = uniqid('profile_', true) . '.' . $file_ext;
            $upload_dir = '../static/uploads/profiles/';
            if (!is_dir($upload_dir))
                mkdir($upload_dir, 0777, true);

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $new_name)) {
                $profile_picture = $new_name;
                $_SESSION['profile_picture'] = $profile_picture;
            }
        }
    }

    $password = $_POST['password'] ?? '';
    if (!empty($password)) {
        if (strlen($password) < 4) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Password too short (min 4 chars).'];
            redirect('profile.php');
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET email = ?, password_hash = ? WHERE id = ?");
        $stmt->execute([$email, $hashed_password, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute([$email, $user_id]);
    }

    // Update details based on role
    if ($role === 'Customer') {
        $stmt = $pdo->prepare("UPDATE customer_details SET full_name = ?, phone = ?, address = ?, gender = ?, bio = ?, profile_picture = ?, nominee_name = ?, nominee_relationship = ? WHERE user_id = ?");
        $stmt->execute([$full_name, $phone, $address, $gender, $bio, $profile_picture, $nominee_name, $nominee_relationship, $user_id]);
    } elseif ($role === 'Staff') {
        $stmt = $pdo->prepare("UPDATE staff_details SET full_name = ?, phone = ?, bio = ?, profile_picture = ? WHERE user_id = ?");
        $stmt->execute([$full_name, $phone, $bio, $profile_picture, $user_id]);
    } elseif ($role === 'Admin') {
        $stmt = $pdo->prepare("UPDATE admin_details SET full_name = ?, profile_picture = ? WHERE user_id = ?");
        $stmt->execute([$full_name, $profile_picture, $user_id]);
    }

    $_SESSION['full_name'] = $full_name;
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profile updated successfully!'];
    redirect('profile.php');
}

render('customer/profile', [
    'page_title' => 'Account Security - Trust Mora Bank',
    'user' => $user,
    'account' => $account
]);
?>