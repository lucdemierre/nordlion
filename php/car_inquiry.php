<?php
session_start();
require_once 'db.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in and get data
    $isLoggedIn = isset($_SESSION['user_id']);
    
    if ($isLoggedIn) {
        $userId = $_SESSION['user_id'];
        $name = $_SESSION['user_name'];
        $email = $_SESSION['user_email'];
    } else {
        $userId = null;
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        
        // Validate required fields for non-logged-in users
        if (empty($name) || empty($email)) {
            header("Location: ../onmarket.php?status=error&message=" . urlencode("Please fill in all required fields."));
            exit;
        }
    }
    
    // Common fields
    $vehicleId = $_POST['vehicle'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Validate required fields
    if (empty($vehicleId) || empty($message)) {
        header("Location: ../onmarket.php?status=error&message=" . urlencode("Please fill in all required fields."));
        exit;
    }
    
    try {
        // Get vehicle name for record
        $vehicleStmt = $pdo->prepare("SELECT name, model FROM cars WHERE id = ?");
        $vehicleStmt->execute([$vehicleId]);
        $vehicle = $vehicleStmt->fetch(PDO::FETCH_ASSOC);
        $vehicleName = $vehicle ? ($vehicle['name'] . ' ' . $vehicle['model']) : 'Unknown Vehicle';
        
        // Insert inquiry into database
        $stmt = $pdo->prepare("INSERT INTO car_inquiries (user_id, name, email, vehicle_id, vehicle_name, message, status, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, 'new', NOW())");
        $stmt->execute([$userId, $name, $email, $vehicleId, $vehicleName, $message]);
        
        // Redirect back with success message
        header("Location: ../onmarket.php?status=success&message=" . urlencode("Your inquiry has been submitted successfully. A specialist will contact you shortly."));
        exit;
    } catch (PDOException $e) {
        // Redirect back with error message
        header("Location: ../onmarket.php?status=error&message=" . urlencode("There was an error submitting your inquiry. Please try again."));
        exit;
    }
} else {
    // Redirect if not a POST request
    header("Location: ../onmarket.php");
    exit;
}
?>
