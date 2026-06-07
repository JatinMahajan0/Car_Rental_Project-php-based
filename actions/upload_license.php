<?php
/**
 * AJAX: Upload driving license file for the logged-in user.
 * Expects POST (multipart): csrf_token, license (file)
 */
require '../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Manual CSRF check (AJAX multipart)
$token = $_POST['csrf_token'] ?? '';
if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

if (empty($_FILES['license']) || $_FILES['license']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
    exit;
}

$file     = $_FILES['license'];
$maxBytes = 5 * 1024 * 1024; // 5 MB
$allowed  = ['image/jpeg', 'image/png', 'application/pdf'];

if ($file['size'] > $maxBytes) {
    echo json_encode(['success' => false, 'message' => 'File too large. Max 5MB allowed.']);
    exit;
}

// Validate MIME type (don't trust $_FILES['type'])
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);
if (!in_array($mimeType, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. JPG, PNG or PDF only.']);
    exit;
}

$ext      = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'][$mimeType];
$uid      = (int)$_SESSION['user_id'];
$filename = 'license_' . $uid . '_' . time() . '.' . $ext;
$destDir  = dirname(__DIR__) . '/uploads/licenses/';
$destPath = $destDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save file. Check folder permissions.']);
    exit;
}

// Delete old license file if exists
$old = $pdo->prepare("SELECT license_file FROM users WHERE id = ?");
$old->execute([$uid]);
$oldFile = $old->fetchColumn();
if ($oldFile && file_exists($destDir . basename($oldFile))) {
    @unlink($destDir . basename($oldFile));
}

// Save new filename to DB
$pdo->prepare("UPDATE users SET license_file = ? WHERE id = ?")->execute([$filename, $uid]);

echo json_encode([
    'success'  => true,
    'filename' => $filename,
    'message'  => 'License uploaded successfully.',
]);
