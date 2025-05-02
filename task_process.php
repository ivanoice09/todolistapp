<?php
// task_process.php
header('Content-Type: application/json');

// Start session if needed
session_start();

// Database configuration
require_once 'db.php';

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Process form data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $title = trim($conn->real_escape_string($_POST['title'] ?? ''));
    $description = trim($conn->real_escape_string($_POST['description'] ?? ''));
    $due_date = $_POST['due_date'] ?? null;
    $due_time = $_POST['due_time'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null; // Assuming you have user session

    // Validate required fields
    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Title is required']);
        exit;
    }

    // Combine date and time if both are provided
    $due_datetime = null;
    if ($due_date && $due_time) {
        $due_datetime = $due_date . ' ' . $due_time . ':00';
    } elseif ($due_date) {
        $due_datetime = $due_date . ' 00:00:00';
    }

    // Prepare and execute SQL
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, due_datetime) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $description, $due_datetime);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Task created successfully']);
        header("location: today.php");
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating task: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
