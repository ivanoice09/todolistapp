<?php
header('Content-Type: application/json');
session_start();
require_once 'db.php';

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $title = trim($conn->real_escape_string($_POST['title'] ?? ''));
    $description = trim($conn->real_escape_string($_POST['description'] ?? ''));
    $due_date = $_POST['due_date'] ?? null;
    $due_time = $_POST['due_time'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    // Validate required fields
    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Title is required']);
        exit;
    }

    // Determine section based on input
    $section = 'inbox'; // Default section

    // Combine date and time if both are provided
    if ($due_date) {
        $due_datetime = $due_date;
        if ($due_time) {
            $due_datetime .= ' ' . $due_time . ':00';
        } else {
            $due_datetime .= ' 00:00:00';
        }

        $today = date('Y-m-d');
        $section = ($due_date == $today) ? 'today' : 'upcoming';
    } else {
        $due_datetime = null; // No date = inbox
    }

    // Prepare and execute SQL
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, due_datetime, section) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $title, $description, $due_datetime, $section);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Task created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating task: ' . $stmt->error]);
    }

    $stmt->close();
}
