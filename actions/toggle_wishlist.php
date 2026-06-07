<?php
require '../config.php';
requireLogin();
csrfVerify();

$carId = (int)($_POST['car_id'] ?? 0);
if (!$carId) { echo json_encode(['status'=>'error','msg'=>'Invalid car.']); exit; }

$check = $pdo->prepare("SELECT id FROM wishlist WHERE user_id=? AND car_id=?");
$check->execute([$_SESSION['user_id'], $carId]);

if ($check->fetch()) {
    $pdo->prepare("DELETE FROM wishlist WHERE user_id=? AND car_id=?")->execute([$_SESSION['user_id'], $carId]);
    echo json_encode(['status'=>'removed']);
} else {
    $pdo->prepare("INSERT INTO wishlist (user_id, car_id) VALUES (?,?)")->execute([$_SESSION['user_id'], $carId]);
    echo json_encode(['status'=>'added']);
}
