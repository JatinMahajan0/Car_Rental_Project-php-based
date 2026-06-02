<?php
require __DIR__ . '/config.php';

try {
    $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
    $userHash = password_hash('password123', PASSWORD_DEFAULT);

    $pdo->prepare("UPDATE users SET password = ? WHERE role = 'admin'")->execute([$adminHash]);
    $pdo->prepare("UPDATE users SET password = ? WHERE role = 'user'")->execute([$userHash]);

    echo "Passwords updated successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
