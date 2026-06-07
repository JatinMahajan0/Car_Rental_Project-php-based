<?php
require '../config.php';
requireLogin();
csrfVerify();

$draft = $_SESSION['booking_draft'] ?? null;
if (!$draft) {
    redirect('/car-rental/fleet.php');
}

$car = $pdo->prepare("SELECT * FROM cars WHERE id=? AND status='available'");
$car->execute([$draft['car_id']]);
$car = $car->fetch();
if (!$car) {
    flash('error', 'Vehicle no longer available.', 'error');
    redirect('/car-rental/fleet.php');
}

// Create booking
$pdo->prepare("INSERT INTO bookings (user_id, car_id, pickup_date, return_date, total_price, status, add_ons, pickup_location, license_number)
               VALUES (?,?,?,?,?,?,?,?,?)")
    ->execute([
        $_SESSION['user_id'],
        $draft['car_id'],
        $draft['pickup_date'],
        $draft['return_date'],
        $draft['grand_total'],
        'upcoming',
        implode(', ', $draft['addons']),
        $draft['pickup_location'],
        $draft['license_number'],
    ]);

$bookingId = (int) $pdo->lastInsertId();

// Notify user
notify(
    $pdo,
    $_SESSION['user_id'],
    "Your booking #{$bookingId} for {$car['name']} is confirmed! Pickup: {$draft['pickup_date']}.",
    'success',
    "/car-rental/user/booking_details.php?id={$bookingId}"
);

// Notify admin
$adminId = $pdo->query("SELECT id FROM users WHERE role='admin' LIMIT 1")->fetchColumn();
if ($adminId) {
    notify(
        $pdo,
        $adminId,
        "New booking #{$bookingId}: {$car['name']} for {$_SESSION['name']} (₹{$draft['grand_total']}).",
        'info',
        "/car-rental/admin/bookings.php"
    );
}

unset($_SESSION['booking_draft']);
redirect("/car-rental/confirmation.php?booking_id={$bookingId}");
