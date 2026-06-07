<?php
require '../config.php';
csrfVerify();

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$returnUrl = $_POST['return_url'] ?? '';

if (!$email || !$password) {
    flash('login_error', 'Please enter your email and password.', 'error');
    redirect('/car-rental/login.php' . ($returnUrl ? '?return_url=' . urlencode($returnUrl) : ''));
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    flash('login_error', 'Invalid email or password.', 'error');
    redirect('/car-rental/login.php' . ($returnUrl ? '?return_url=' . urlencode($returnUrl) : ''));
}

if ($user['status'] === 'blocked') {
    flash('login_error', 'Your account has been restricted. Please contact support.', 'error');
    redirect('/car-rental/login.php');
}

// Regenerate session ID to prevent fixation
session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];
$_SESSION['name']    = $user['name'];
$_SESSION['role']    = $user['role'];
$_SESSION['email']   = $user['email'];

if ($user['role'] === 'admin') {
    redirect('/car-rental/admin/index.php');
}

// Return to intended page if set
if ($returnUrl && str_starts_with($returnUrl, '/car-rental/')) {
    redirect($returnUrl);
}

redirect('/car-rental/index.php');
