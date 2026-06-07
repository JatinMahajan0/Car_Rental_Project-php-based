<?php
require '../config.php';
requireLogin();

// Accept CSRF from both POST (form) and GET (legacy link) for compatibility
$token = $_POST['csrf_token'] ?? ($_GET['csrf_token'] ?? '');
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    flash('booking_error', 'Security token expired. Please try again.', 'error');
    redirect('/car-rental/user/bookings.php');
}

$id   = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id=? AND user_id=? AND status IN ('upcoming','ongoing')");
$stmt->execute([$id, $_SESSION['user_id']]);
$b = $stmt->fetch();

if (!$b) {
    flash('booking_error', 'Booking not found or cannot be cancelled.', 'error');
    redirect('/car-rental/user/bookings.php');
}

$pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=?")->execute([$id]);

$car = $pdo->prepare("SELECT name FROM cars WHERE id=?");
$car->execute([$b['car_id']]);
$car = $car->fetch();
notify($pdo, $_SESSION['user_id'], "Your booking #{$id} for {$car['name']} has been cancelled.", 'warning', '/car-rental/user/bookings.php');

flash('toast_success', 'Booking #' . str_pad($id, 4, '0', STR_PAD_LEFT) . ' cancelled successfully.', 'success');
redirect('/car-rental/user/bookings.php');
