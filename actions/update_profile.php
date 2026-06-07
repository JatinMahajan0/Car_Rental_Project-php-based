<?php
require '../config.php';
requireLogin();
csrfVerify();

$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('profile_error', 'Invalid name or email.', 'error');
    redirect('/car-rental/user/profile.php');
}

// Check email not taken by another user
$check = $pdo->prepare("SELECT id FROM users WHERE email=? AND id!=?");
$check->execute([$email, $_SESSION['user_id']]);
if ($check->fetch()) {
    flash('profile_error', 'Email address already in use by another account.', 'error');
    redirect('/car-rental/user/profile.php');
}

$pdo->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?")
    ->execute([$name, $email, $phone, $_SESSION['user_id']]);

$_SESSION['name']  = $name;
$_SESSION['email'] = $email;
$_SESSION['phone'] = $phone;

flash('profile_success', 'Profile updated successfully.', 'success');
redirect('/car-rental/user/profile.php');
