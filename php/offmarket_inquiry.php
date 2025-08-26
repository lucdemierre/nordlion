<?php
session_start();
require_once __DIR__ . '/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: ../login.html?message=" . urlencode("Please log in to submit inquiries."));
    exit;
}

$userRole = $_SESSION['user_role'] ?? '';
if ($userRole !== 'vc' && $userRole !== 'admin') {
    header("Location: ../login.html?message=" . urlencode("You need to be VC or Admin to submit inquiries."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../offmarket.php");
    exit;
}

$userId         = (int) $_SESSION['user_id'];
$vehicle        = trim($_POST['vehicle'] ?? '');
$message        = trim($_POST['message'] ?? '');
$investmentType = trim($_POST['investment_type'] ?? '');
$carId          = isset($_POST['car_id']) ? (int) $_POST['car_id'] : null;

if ($vehicle === '' || $message === '' || $investmentType === '') {
    header("Location: ../offmarket.php?status=error&message=" . urlencode("Please complete all fields."));
    exit;
}

try {
    $sql = "INSERT INTO offmarket_inquiries (user_id, vehicle, message, investment_type, car_id, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $vehicle, $message, $investmentType, $carId]);

    header("Location: ../offmarket.php?status=success&message=" . urlencode("Your inquiry has been submitted successfully."));
    exit;
} catch (PDOException $e) {
    header("Location: ../offmarket.php?status=error&message=" . urlencode("Error submitting inquiry. Please try again."));
    exit;
}
