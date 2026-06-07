<?php
require '../config.php';
csrfVerify();

$token    = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm'] ?? '';

if (!$token || strlen($password) < 8 || $password !== $confirm) {
    flash('reset_error', 'Invalid request. Please check your password (min 8 chars) and ensure they match.', 'error');
    redirect('/car-rental/reset_password.php?token=' . urlencode($token));
}

$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token=? AND expires_at > NOW()");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    flash('forgot_error', 'This reset link has expired. Please request a new one.', 'error');
    redirect('/car-rental/forgot_password.php');
}

// Update password
$hashed = password_hash($password, PASSWORD_DEFAULT);
$pdo->prepare("UPDATE users SET password=? WHERE email=?")->execute([$hashed, $reset['email']]);

// Delete used token
$pdo->prepare("DELETE FROM password_resets WHERE token=?")->execute([$token]);

redirect('/car-rental/reset_success.php');
