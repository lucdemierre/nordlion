<?php
require 'db.php';
session_start();

// Only allow admins to access this script
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = $_POST['car_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$car_id || !in_array($action, ['approve', 'reject'])) {
        echo "Invalid request.";
        exit;
    }

    $newStatus = $action === 'approve' ? 'approved' : 'rejected';

    try {
        $stmt = $pdo->prepare("UPDATE cars SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $car_id]);
        header("Location: ../dashboard.php");
        exit;
    } catch (PDOException $e) {
        echo "Error updating status: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>
