<?php
header('Content-Type: application/json');
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $task_id = $data['task_id'] ?? null;
    $due_datetime = $data['due_datetime'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    if ($task_id && $due_datetime && $user_id) {
        // Verify task belongs to user
        $stmt = $conn->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $task_id, $user_id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $update = $conn->prepare("UPDATE tasks SET due_datetime = ? WHERE id = ?");
            $update->bind_param("si", $due_datetime, $task_id);

            if ($update->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Update failed']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Task not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
}
