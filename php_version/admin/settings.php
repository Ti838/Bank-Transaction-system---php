<?php
require_once '../includes/functions.php';
require_role('Admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $key => $value) {
        update_system_setting($key, $value);
    }
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Settings updated successfully!'];
    redirect('settings.php');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['broadcast_message'])) {
    $message = trim($_POST['broadcast_message']);
    if (!empty($message)) {

        $stmt = $pdo->query("SELECT id FROM users");
        $all_users = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Mass Broadcast: Send Notification to ALL Users
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, notification_type) VALUES (?, ?, 'Alert')");

        foreach ($all_users as $uid) {
            $stmt->execute([$uid, $message]);
        }
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Broadcast sent to ' . count($all_users) . ' users.'];
    } else {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Message cannot be empty.'];
    }
    redirect('settings.php');
}

$settings = get_system_settings();

render('admin/settings', [
    'page_title' => 'Nexus Control - Trust Mora Bank',
    'settings' => $settings
]);
?>