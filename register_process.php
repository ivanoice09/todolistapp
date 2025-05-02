<?php

require_once 'db.php';

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $firstName = sanitizeInput($_POST['firstName']);
    $lastName = sanitizeInput($_POST['lastName']);
    $dob = $_POST['dob']; // Date doesn't need sanitization for MySQL
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Validate password match
    if ($password !== $confirmPassword) {
        die("Error: Passwords do not match.");
    }
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if terms were agreed to
    if (!isset($_POST['termsCheck'])) {
        die("Error: You must agree to the terms and conditions.");
    }
    
    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, dob, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstName, $lastName, $dob, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        // Registration successful
        echo "Registration successful!";
        header("Location: login_form.php");
    } else {
        // Registration failed
        echo "Error: " . $stmt->error;
    }
    
    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();

// Function to sanitize form inputs
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>