<?php
header('Content-Type: application/json');
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $task_id = $data['task_id'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    if ($task_id && $user_id) {
        // First verify the task belongs to the user
        $stmt = $conn->prepare("SELECT completed FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $task_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $task = $result->fetch_assoc();
            $new_status = $task['completed'] ? 0 : 1;

            // Update the task
            $update = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
            $update->bind_param("ii", $new_status, $task_id);

            if ($update->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Update failed']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Task not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
}
