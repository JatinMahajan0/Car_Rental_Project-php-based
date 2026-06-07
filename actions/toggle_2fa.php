<?php
/**
 * AJAX: Toggle Two-Factor Authentication for the logged-in user.
 * Expects POST: csrf_token, enabled (0|1)
 */
require '../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Manual CSRF check (AJAX)
$token = $_POST['csrf_token'] ?? '';
if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$enabled = isset($_POST['enabled']) ? (int)(bool)$_POST['enabled'] : 0;
$uid     = (int)$_SESSION['user_id'];

$pdo->prepare("UPDATE users SET two_fa_enabled = ? WHERE id = ?")->execute([$enabled, $uid]);

echo json_encode([
    'success' => true,
    'enabled' => $enabled,
    'message' => $enabled ? 'Two-Factor Authentication enabled.' : 'Two-Factor Authentication disabled.',
]);
