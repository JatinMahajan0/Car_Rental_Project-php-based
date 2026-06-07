<?php
require '../config.php';
requireLogin();
csrfVerify();

$car_id      = (int)($_POST['car_id'] ?? 0);
$pickup      = $_POST['pickup_date'] ?? '';
$return_date = $_POST['return_date'] ?? '';
$addons      = $_POST['addons'] ?? [];

$car = $pdo->prepare("SELECT * FROM cars WHERE id=? AND status='available'");
$car->execute([$car_id]);
$car = $car->fetch();
if (!$car) { flash('error','Vehicle not available.','error'); redirect('/car-rental/fleet.php'); }

$days = max(1, (int)ceil((strtotime($return_date) - strtotime($pickup)) / 86400));
$addonPrices = ['gps'=>299,'insurance'=>599,'chauffeur'=>1499];
$addonTotal  = array_sum(array_map(fn($k) => ($addonPrices[$k] ?? 0) * $days, $addons));
$addonNames  = array_map(fn($k) => match($k) { 'gps'=>'GPS Navigation', 'insurance'=>'Premium Insurance', 'chauffeur'=>'Chauffeur Service', default=>$k }, $addons);

// Store draft in session
$_SESSION['booking_draft'] = [
    'car_id'       => $car_id,
    'pickup_date'  => $pickup,
    'return_date'  => $return_date,
    'full_name'    => $_POST['full_name'] ?? $_SESSION['name'],
    'email'        => $_POST['email'] ?? '',
    'phone'        => $_POST['phone'] ?? '',
    'pickup_location' => $_POST['pickup_location'] ?? '',
    'license_number'  => $_POST['license_number'] ?? '',
    'addons'       => $addonNames,
    'addon_total'  => $addonTotal,
    'days'         => $days,
    'base_total'   => $car['price'] * $days,
    'grand_total'  => ($car['price'] * $days) + $addonTotal,
];

redirect("/car-rental/booking_flow.php?car_id=$car_id&pickup=$pickup&return_date=$return_date&step=2");
