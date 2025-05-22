<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is a VC
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $isLoggedIn ? $_SESSION['user_role'] : '';
$isVC = ($isLoggedIn && $userRole === 'vc');

// If not logged in as VC, redirect to login
if (!$isVC) {
    header("Location: ../login.html?message=" . urlencode("You need to be logged in as a VC to submit inquiries."));
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $vehicle = $_POST['vehicle'] ?? '';
    $message = $_POST['message'] ?? '';
    $investmentType = $_POST['investment_type'] ?? '';
    
    try {
        $stmt = $pdo->prepare("INSERT INTO offmarket_inquiries (user_id, vehicle, message, investment_type, status, created_at) 
                               VALUES (?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$userId, $vehicle, $message, $investmentType]);
        
        // Redirect back with success message
        header("Location: ../offmarket.php?status=success&message=" . urlencode("Your inquiry has been submitted successfully. A specialist will contact you shortly."));
        exit;
    } catch (PDOException $e) {
        // Redirect back with error message
        header("Location: ../offmarket.php?status=error&message=" . urlencode("There was an error submitting your inquiry. Please try again."));
        exit;
    }
} else {
    // Redirect if not a POST request
    header("Location: ../offmarket.php");
    exit;
}
?>
