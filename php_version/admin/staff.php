<?php
require_once '../includes/functions.php';
require_role('Admin');


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $gender = $_POST['gender'] ?? '';

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Email already exists.'];
    } else {
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'Staff'");
        $stmt->execute();
        $role_id = $stmt->fetchColumn();

        $stmt = $pdo->prepare("INSERT INTO users (full_name, role_id, email, password_hash, gender) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $role_id, $email, $hashed_password, $gender]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'New staff member created successfully.'];
    }
    redirect('staff.php');
}

$stmt = $pdo->query("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = 'Staff' ORDER BY u.created_at DESC");
$staff_members = $stmt->fetchAll();

render('admin/staff', [
    'page_title' => 'Personnel HQ - Trust Mora Admin',
    'staff_members' => $staff_members
]);
?>