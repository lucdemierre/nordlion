<?php
require 'db.php';

if (!isset($_POST['image_id'], $_POST['car_id'])) {
    die("Invalid request.");
}

$image_id = intval($_POST['image_id']);
$car_id = intval($_POST['car_id']);

// Get image path before deleting
$stmt = $pdo->prepare("SELECT image_path FROM car_images WHERE id = ?");
$stmt->execute([$image_id]);
$image = $stmt->fetch();

if ($image) {
    $path = '../uploads/' . basename($image['image_path']);
    if (file_exists($path)) {
        unlink($path);
    }
    // Delete from database
    $deleteStmt = $pdo->prepare("DELETE FROM car_images WHERE id = ?");
    $deleteStmt->execute([$image_id]);
}

// Redirect back
header("Location: ../edit_car.php?id=" . $car_id);
exit;
