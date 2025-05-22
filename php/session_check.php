<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userRole = $isLoggedIn ? $_SESSION['user_role'] : '';
$isAdmin = ($isLoggedIn && $userRole === 'admin');
$isVC = ($isLoggedIn && $userRole === 'vc');
$isMember = ($isLoggedIn && ($userRole === 'mop' || $userRole === ''));

// Function to check if user has required role
function requireRole($requiredRole) {
    global $isLoggedIn, $userRole;
    
    if (!$isLoggedIn || $userRole !== $requiredRole) {
        header("Location: login.html?message=" . urlencode("You need to be logged in as a " . $requiredRole . " to access this page."));
        exit;
    }
}

// Function to check if user is logged in
function requireLogin() {
    global $isLoggedIn;
    
    if (!$isLoggedIn) {
        header("Location: login.html?message=" . urlencode("You need to be logged in to access this page."));
        exit;
    }
}
?>
