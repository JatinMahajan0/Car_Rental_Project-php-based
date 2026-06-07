<?php
require '../config.php';
csrfVerify();

$name    = trim($_POST['fullname'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm'] ?? '';

// Validate
$errors = [];
if (strlen($name) < 2)          $errors[] = 'Name must be at least 2 characters.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
if (strlen($password) < 6)      $errors[] = 'Password must be at least 6 characters.';
if ($password !== $confirm)      $errors[] = 'Passwords do not match.';

if ($errors) {
    flash('signup_error', implode(' ', $errors), 'error');
    redirect('/car-rental/signup.php');
}

// Check duplicate email
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    flash('signup_error', 'An account with this email already exists.', 'error');
    redirect('/car-rental/signup.php');
}

// Create user
$hashed = password_hash($password, PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)")
    ->execute([$name, $email, $phone, $hashed]);

$userId = (int)$pdo->lastInsertId();
notify($pdo, $userId, "Welcome to LuxeDrive, $name! Your account is ready.", 'success', '/car-rental/fleet.php');

flash('login_success', 'Account created successfully. Please sign in.', 'success');
redirect('/car-rental/login.php?signup=success');
