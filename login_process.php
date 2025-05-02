<?php
// login_process.php

// Start session
session_start();

// db connect
require_once 'db.php';

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize email input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $rememberMe = isset($_POST['rememberMe']) ? true : false;

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start user session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            
            // Set remember me cookie if checked
            if ($rememberMe) {
                $cookie_value = $user['id'] . ':' . hash('sha256', $user['password']);
                setcookie('remember_me', $cookie_value, time() + (86400 * 30), "/"); // 30 days
            }
            
            // Redirect to dashboard or home page
            header("Location: index.php");
            exit();
        } else {
            // Invalid password
            $error = "Invalid email or password.";
        }
    } else {
        // User not found
        $error = "Invalid email or password.";
    }
    
    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();

// If we got here, there was an error
// You might want to redirect back to login with error message
// For simplicity, we'll just display it here
if (isset($error)) {
    die($error);
}
?>