<?php
require_once '../includes/functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT u.email, cd.* FROM users u JOIN customer_details cd ON u.id = cd.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

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
            redirect('customer/profile.php');
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET email = ?, password_hash = ? WHERE id = ?");
        $stmt->execute([$email, $hashed_password, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute([$email, $user_id]);
    }

    $stmt = $pdo->prepare("UPDATE customer_details SET full_name = ?, phone = ?, address = ?, gender = ?, bio = ?, profile_picture = ? WHERE user_id = ?");
    $stmt->execute([$full_name, $phone, $address, $gender, $bio, $profile_picture, $user_id]);

    $_SESSION['full_name'] = $full_name;
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profile updated successfully!'];
    redirect('customer/profile.php');
}

render('customer/profile', [
    'page_title' => 'Account Security - Trust Mora Bank',
    'user' => $user,
    'account' => $account
]);
?>