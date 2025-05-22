<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is a VC
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $isLoggedIn ? $_SESSION['user_role'] : '';
$isVC = ($isLoggedIn && $userRole === 'vc');

// If not logged in as VC, redirect to login
if (!$isVC) {
    header("Location: ../login.html?message=" . urlencode("You need to be logged in as a VC to submit investment inquiries."));
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $investmentType = $_POST['investment_type'] ?? '';
    $budgetRange = $_POST['budget_range'] ?? '';
    $timeline = $_POST['timeline'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Validate required fields
    if (empty($investmentType) || empty($budgetRange) || empty($timeline) || empty($message)) {
        header("Location: ../contact.php?section=investment&status=error&message=" . urlencode("Please fill in all required fields."));
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO investment_inquiries (user_id, investment_type, budget_range, timeline, message, status, created_at) 
                               VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$userId, $investmentType, $budgetRange, $timeline, $message]);
        
        // Redirect back with success message
        header("Location: ../contact.php?status=success&message=" . urlencode("Your investment inquiry has been submitted successfully. An investment specialist will contact you shortly to discuss your opportunities."));
        exit;
    } catch (PDOException $e) {
        // Redirect back with error message
        header("Location: ../contact.php?section=investment&status=error&message=" . urlencode("There was an error submitting your inquiry. Please try again."));
        exit;
    }
} else {
    // Redirect if not a POST request
    header("Location: ../contact.php");
    exit;
}
?>
