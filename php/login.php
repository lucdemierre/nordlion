<?php
require 'db.php';
session_start();

// Set proper content type for AJAX responses
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate inputs
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    
    if (!$email) {
        echo json_encode([
            'success' => false,
            'message' => "Please enter a valid email address."
        ]);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode([
            'success' => false,
            'message' => "Password must be at least 6 characters long."
        ]);
        exit;
    }

    // Fetch user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Store session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['last_activity'] = time();

        // Log the login
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $pdo->prepare("INSERT INTO login_logs (user_id, login_time, ip_address) VALUES (?, NOW(), ?)");
        $log_stmt->execute([$user['id'], $ip]);

        // Redirect all users to the unified index.php page
        $redirect_url = 'index.php';

        echo json_encode([
            'success' => true,
            'message' => "Login successful",
            'role' => $user['role'],
            'redirect_url' => $redirect_url
        ]);
        exit;
    } else {
        // Add delay for security (prevent timing attacks)
        sleep(1);
        echo json_encode([
            'success' => false,
            'message' => "Invalid email or password. Please try again."
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => "Invalid request method."
    ]);
}
?>
