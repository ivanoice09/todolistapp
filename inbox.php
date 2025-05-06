<?php
session_start();
require_once 'db.php';

$inbox_tasks = [];
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, title, description, created_at FROM tasks 
                          WHERE user_id = ? AND section = 'inbox' AND completed = 0
                          ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $inbox_tasks[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- sweetalert link -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Inbox</title>
</head>

<body>
    <?php require 'sidenavbar.php'; ?>
    <?php require 'task_modal.php'; ?>

    <div class="main-content">
        <div class="container py-3">
            <h1 class="h3 mb-4"><i class="bi bi-inbox"></i> Inbox</h1>

            <?php if (empty($inbox_tasks)): ?>
                <div class="alert alert-info">
                    Your inbox is empty. Add a task without a due date to see it here.
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($inbox_tasks as $task): ?>
                        <div class="col-12">
                            <div class="card task-card shadow-sm">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-1"><?= htmlspecialchars($task['title']) ?></h5>
                                            <?php if (!empty($task['description'])): ?>
                                                <p class="card-text text-muted small mb-0"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
                                            <?php endif; ?>
                                            <small class="text-muted">
                                                Added: <?= date('M j, g:i A', strtotime($task['created_at'])) ?>
                                            </small>
                                        </div>
                                        <div class="task-actions">
                                            <button class="btn btn-sm btn-outline-primary me-2"
                                                onclick="addDueDate(<?= $task['id'] ?>)">
                                                <i class="bi bi-calendar-plus"></i> Add Due Date
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger"
                                                onclick="deleteTask(<?= $task['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function addDueDate(taskId) {
            // Implement a modal or other UI to add a due date
            // This would update the task's section to 'today' or 'upcoming'
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/delete_task.js"></script>
</body>

</html>