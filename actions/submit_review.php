<?php
require '../config.php';
requireLogin();

header('Content-Type: application/json');

$token = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    echo json_encode(['success' => false, 'message' => 'Security token expired.']);
    exit;
}

$carId    = (int)($_POST['car_id'] ?? 0);
$bookingId= (int)($_POST['booking_id'] ?? 0);
$rating   = (int)($_POST['rating'] ?? 0);
$comment  = trim($_POST['comment'] ?? '');

if ($rating < 1 || $rating > 5 || !$carId) {
    echo json_encode(['success' => false, 'message' => 'Please select a rating.']);
    exit;
}

// Verify user actually completed a booking for this car
$check = $pdo->prepare("SELECT id FROM bookings WHERE id=? AND user_id=? AND car_id=? AND status='completed'");
$check->execute([$bookingId, $_SESSION['user_id'], $carId]);
if (!$check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Booking not found or not eligible for review.']);
    exit;
}

// One review per booking
$existing = $pdo->prepare("SELECT id FROM reviews WHERE user_id=? AND car_id=?");
$existing->execute([$_SESSION['user_id'], $carId]);
if ($existing->fetch()) {
    echo json_encode(['success' => false, 'message' => 'You have already reviewed this vehicle.']);
    exit;
}

$pdo->prepare("INSERT INTO reviews (user_id, car_id, rating, comment) VALUES (?,?,?,?)")
    ->execute([$_SESSION['user_id'], $carId, $rating, $comment]);

echo json_encode(['success' => true, 'message' => 'Review submitted! Thank you.']);
