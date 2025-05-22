<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is a VC
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $isLoggedIn ? $_SESSION['user_role'] : '';
$isVC = ($isLoggedIn && $userRole === 'vc');

// If not logged in as VC, redirect to login
if (!$isVC) {
    header("Location: ../login.html?message=" . urlencode("You need to be logged in as a VC to submit vehicle requests."));
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $vehicleName = $_POST['vehicle_name'] ?? '';
    $vehicleType = $_POST['vehicle_type'] ?? '';
    $budget = $_POST['budget'] ?? '';
    $details = $_POST['details'] ?? '';
    
    // Validate required fields
    if (empty($vehicleName) || empty($vehicleType) || empty($budget) || empty($details)) {
        header("Location: ../contact.php?section=request&status=error&message=" . urlencode("Please fill in all required fields."));
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO car_requests (user_id, vehicle_name, vehicle_type, budget, details, status, created_at) 
                               VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$userId, $vehicleName, $vehicleType, $budget, $details]);
        
        // Redirect back with success message
        header("Location: ../contact.php?status=success&message=" . urlencode("Your vehicle request has been submitted successfully. Our specialists will begin searching for your dream car and contact you shortly."));
        exit;
    } catch (PDOException $e) {
        // Redirect back with error message
        header("Location: ../contact.php?section=request&status=error&message=" . urlencode("There was an error submitting your request. Please try again."));
        exit;
    }
} else {
    // Redirect if not a POST request
    header("Location: ../contact.php");
    exit;
}
?>
