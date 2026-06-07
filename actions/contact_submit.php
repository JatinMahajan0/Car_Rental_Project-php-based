<?php
require '../config.php';
csrfVerify();

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$subject || !$message) {
    flash('contact_error', 'Please fill in all fields.', 'error');
    redirect('/car-rental/contact.php');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('contact_error', 'Please enter a valid email address.', 'error');
    redirect('/car-rental/contact.php');
}

// Save message to DB
$pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)")
    ->execute([$name, $email, $subject, $message]);

$msgId = (int)$pdo->lastInsertId();

// Notify all admins in their notifications panel
$admins = $pdo->query("SELECT id FROM users WHERE role='admin'")->fetchAll();
foreach ($admins as $admin) {
    $notifMsg = "📩 New contact message from <strong>" . htmlspecialchars($name) . "</strong>"
              . " (" . htmlspecialchars($email) . ")"
              . " — <em>" . htmlspecialchars($subject) . "</em>: "
              . htmlspecialchars(mb_strimwidth($message, 0, 100, '…'));
    notify($pdo, $admin['id'], $notifMsg, 'info', '/car-rental/admin/messages.php');
}

flash('contact_success', "Your message has been sent! We'll get back to you within 24 hours.", 'success');
redirect('/car-rental/contact.php');
