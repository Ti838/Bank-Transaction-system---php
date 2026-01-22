<?php
require_once '../includes/functions.php';
require_role('Admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $key => $value) {
        update_system_setting($key, $value);
    }
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Settings updated successfully!'];
    redirect('admin/settings.php');
}

$settings = get_system_settings();

render('admin/settings', [
    'page_title' => 'Nexus Control - Trust Mora Bank',
    'settings' => $settings
]);
?>