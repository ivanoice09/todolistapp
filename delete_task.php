<?php
// delete_task.php
header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Database configuration
require_once 'db.php';

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Process DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $task_id = isset($data['task_id']) ? intval($data['task_id']) : null;
    $user_id = $_SESSION['user_id'];

    // Validate task ID
    if (!$task_id || $task_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid task ID']);
        exit;
    }

    try {
        // First verify the task belongs to the user
        $stmt = $conn->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $task_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Task not found or access denied']);
            exit;
        }

        // Delete the task
        $delete_stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $delete_stmt->bind_param("ii", $task_id, $user_id);

        if ($delete_stmt->execute()) {
            if ($delete_stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No task was deleted']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting task']);
        }

        $delete_stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
