<?php
require '../config.php';
requireLogin();

$userId = (int)$_SESSION['user_id'];

if (isset($_GET['mark_all'])) {
    $pdo->prepare("UPDATE notifications SET status='read' WHERE user_id=?")->execute([$userId]);
    redirect('/car-rental/user/notifications.php');
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    $notif = $stmt->fetch();
    if ($notif) {
        $pdo->prepare("UPDATE notifications SET status='read' WHERE id=?")->execute([$id]);
        redirect($notif['link_ref'] ?: '/car-rental/user/notifications.php');
    }
}

redirect('/car-rental/user/notifications.php');
