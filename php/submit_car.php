<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit;
}

// Basic car fields
$name        = $_POST['name'];
$model       = $_POST['model'];
$price       = $_POST['price'];
$year        = $_POST['year'];
$mileage     = $_POST['mileage'];
$int_colour  = $_POST['int_colour'];
$ext_colour  = $_POST['ext_colour'];
$description = $_POST['description'];
$submitted_by = $_SESSION['user_id'];

// Insert car into database (initially without image)
$stmt = $pdo->prepare("INSERT INTO cars (name, model, price, year, mileage, int_colour, ext_colour, description, status, submitted_by) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
$stmt->execute([$name, $model, $price, $year, $mileage, $int_colour, $ext_colour, $description, $submitted_by]);

$car_id = $pdo->lastInsertId();

// Process uploaded images
$imageFiles = $_FILES['images'];
$captions = $_POST['captions'];

$uploadDir = '../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

for ($i = 0; $i < count($imageFiles['name']); $i++) {
    if ($imageFiles['error'][$i] === UPLOAD_ERR_OK) {
        $tmpName = $imageFiles['tmp_name'][$i];
        $fileName = uniqid('car_') . "_" . basename($imageFiles['name'][$i]);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $caption = htmlspecialchars($captions[$i]);

            // Save image path and caption to car_images table
            $imgStmt = $pdo->prepare("INSERT INTO car_images (car_id, image_path, caption) VALUES (?, ?, ?)");
            $imgStmt->execute([$car_id, 'uploads/' . $fileName, $caption]);
        }
    }
}

header("Location: ../dashboard.php");
exit;
