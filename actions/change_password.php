<?php
require '../config.php';
requireLogin();
csrfVerify();

$current = $_POST['current_password'] ?? '';
$new     = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (strlen($new) < 8 || $new !== $confirm) {
    flash('settings_error', 'New password must be at least 8 characters and must match confirmation.', 'error');
    redirect('/car-rental/user/settings.php');
}

$user = $pdo->prepare("SELECT password FROM users WHERE id=?");
$user->execute([$_SESSION['user_id']]);
$user = $user->fetch();

if (!password_verify($current, $user['password'])) {
    flash('settings_error', 'Current password is incorrect.', 'error');
    redirect('/car-rental/user/settings.php');
}

$hashed = password_hash($new, PASSWORD_DEFAULT);
$pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hashed, $_SESSION['user_id']]);
flash('settings_success', 'Password updated successfully.', 'success');
redirect('/car-rental/user/settings.php');
